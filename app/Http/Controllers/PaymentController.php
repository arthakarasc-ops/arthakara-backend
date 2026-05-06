<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Services\FonnteService;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * POST /api/pay
     * Buat Snap Token Midtrans dari order yang sudah ada
     */
    public function createTransaction(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id'
        ]);

        \Midtrans\Config::$serverKey    = config('midtrans.serverKey');
        \Midtrans\Config::$isProduction = config('midtrans.isProduction');
        \Midtrans\Config::$isSanitized  = true;
        \Midtrans\Config::$is3ds        = true;

        Log::info('Midtrans Config used:', [
            'server_key' => substr(\Midtrans\Config::$serverKey, 0, 10) . '...',
            'is_production' => \Midtrans\Config::$isProduction
        ]);

        // Ambil order beserta relasi
        $order = Order::with([
            'orderItems.productVariants.product',
            'users',
            'shippingMethods',
        ])->find($request->order_id);

        if (!$order) {
            return response()->json(['message' => 'Order tidak ditemukan'], 404);
        }

        // Cegah double-pay jika sudah paid
        if ($order->payment_status === 'paid') {
            return response()->json(['message' => 'Order ini sudah lunas.'], 400);
        }

        // Generate unique Midtrans order ID
        $midtransOrderId = 'MID-' . $order->id . '-' . time();

        $order->midtrans_order_id = $midtransOrderId;
        $order->payment_status    = 'pending';
        $order->save();

        // ✅ Item details dari order items
        $items = [];
        foreach ($order->orderItems as $item) {
            $productName = optional(optional($item->productVariants)->product)->name ?? 'Product';
            $unitPrice   = (int) round($item->price_at_purchase);

            $items[] = [
                'id'       => 'ITEM-' . $item->product_variant_id,
                'price'    => $unitPrice,
                'quantity' => $item->quantity,
                'name'     => mb_substr($productName, 0, 50), // max 50 char Midtrans
            ];
        }

        // ✅ Tambahkan ongkos kirim sebagai item tersendiri
        $shippingPrice = (int) $order->shippingMethods->price;
        if ($shippingPrice > 0) {
            $items[] = [
                'id'       => 'SHIPPING',
                'price'    => $shippingPrice,
                'quantity' => 1,
                'name'     => 'Ongkos Kirim - ' . $order->shippingMethods->name,
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $midtransOrderId,
                'gross_amount' => (int) $order->total_price,
            ],
            'item_details'    => $items,
            'customer_details' => [
                'first_name' => $order->users->name ?? 'User',
                'email'      => $order->users->email ?? 'user@email.com',
                'phone'      => $order->users->phone_number ?? '',
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            return response()->json([
                'snap_token'       => $snapToken,
                'midtrans_order_id' => $midtransOrderId,
                'isSuccess'        => true,
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap token error: ' . $e->getMessage());
            return response()->json([
                'message'   => 'Gagal membuat transaksi Midtrans.',
                'error'     => $e->getMessage(),
                'isSuccess' => false,
            ], 500);
        }
    }

    /**
     * POST /api/midtrans-callback
     * Dipanggil oleh Midtrans setelah transaksi selesai
     */
    public function callback(Request $request)
    {
        $serverKey = config('midtrans.serverKey');

        // Validasi signature Midtrans
        $signature = hash("sha512",
            $request->order_id .
            $request->status_code .
            $request->gross_amount .
            $serverKey
        );

        if ($signature !== $request->signature_key) {
            Log::warning('Midtrans callback: Invalid signature', ['order_id' => $request->order_id]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // Cari order berdasarkan midtrans_order_id
        $order = Order::with(['orderItems', 'shippingMethods', 'shippingAddresses', 'users'])
            ->where('midtrans_order_id', $request->order_id)
            ->first();

        if (!$order) {
            Log::warning('Midtrans callback: Order tidak ditemukan', ['midtrans_order_id' => $request->order_id]);
            return response()->json(['message' => 'Order tidak ditemukan'], 404);
        }

        $previousStatus = $order->payment_status;

        switch ($request->transaction_status) {

            case 'settlement':
            case 'capture': // Untuk credit card
                if ($previousStatus !== 'paid') {
                    $order->payment_status = 'paid';
                    $order->status_id      = 2; // Processing (Sedang Dikemas)

                    // ✅ Kurangi stok produk
                    foreach ($order->orderItems as $item) {
                        $variant = ProductVariant::with('product')->find($item->product_variant_id);
                        if ($variant && $variant->product) {
                            $variant->product->stock = max(0, $variant->product->stock - $item->quantity);
                            $variant->product->save();
                        }
                    }

                    // ✅ Notifikasi WA ke Admin jika Delivery (Bukan Take Away)
                    if ($order->shippingMethods && $order->shippingMethods->name !== 'Take Away') {
                        $this->sendNewOrderNotificationToAdmin($order);
                    }
                }
                break;

            case 'pending':
                $order->payment_status = 'pending';
                break;

            case 'expire':
                // ✅ Kembalikan stok jika sebelumnya sudah terpotong
                if ($previousStatus === 'paid') {
                    $this->restoreStock($order);
                }
                $order->payment_status = 'expired';
                $order->status_id      = 5; // Cancelled
                break;

            case 'cancel':
                // ✅ Kembalikan stok jika sebelumnya sudah terpotong
                if ($previousStatus === 'paid') {
                    $this->restoreStock($order);
                }
                $order->payment_status = 'canceled';
                $order->status_id      = 5; // Cancelled
                break;

            case 'deny':
                $order->payment_status = 'failed';
                break;
        }

        $order->save();

        return response()->json(['message' => 'OK']);
    }

    /**
     * Restore stock when order is cancelled/expired
     */
    private function restoreStock(Order $order): void
    {
        foreach ($order->orderItems as $item) {
            $variant = ProductVariant::with('product')->find($item->product_variant_id);
            if ($variant && $variant->product) {
                $variant->product->stock += $item->quantity;
                $variant->product->save();
            }
        }
    }

    /**
     * Kirim notifikasi WA pesanan baru ke Admin
     */
    private function sendNewOrderNotificationToAdmin(Order $order): void
    {
        $adminPhone = '087784488639'; // Nomor tujuan WA Admin
        
        $customerName = $order->shippingAddresses->first_name ?? ($order->users->full_name ?? 'Customer');
        $customerPhone = $order->shippingAddresses->phone_number ?? ($order->users->phone_number ?? '-');
        $address = $order->shippingAddresses->address ?? '-';
        
        $message = "Halo Admin,\n\n"
                 . "Terdapat pesanan *DELIVERY* baru yang sudah *LUNAS*!\n\n"
                 . "*Detail Pesanan:*\n"
                 . "- Order ID: #{$order->id}\n"
                 . "- Total Bayar: Rp" . number_format($order->total_price, 0, ',', '.') . "\n\n"
                 . "*Data Pengiriman:*\n"
                 . "- Nama: {$customerName}\n"
                 . "- No HP: {$customerPhone}\n"
                 . "- Alamat: {$address}\n\n"
                 . "Silakan cek dashboard admin untuk melihat barang yang dibeli dan memproses pesanannya. Terima kasih!";
                 
        $fonnte = new FonnteService();
        $fonnte->sendMessage($adminPhone, $message);
    }
}