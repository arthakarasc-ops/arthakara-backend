<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Services\FonnteService;
use App\Services\DokuService;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected DokuService $dokuService;

    public function __construct(DokuService $dokuService)
    {
        $this->dokuService = $dokuService;
    }

    /**
     * POST /api/pay
     */
    public function createTransaction(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id'
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

        if ($order->payment_status === 'paid') {
            return response()->json(['message' => 'Order ini sudah lunas.'], 400);
        }

        $dokuInvoiceNumber = 'INV-' . $order->id . '-' . time();

        $items = [];
        foreach ($order->orderItems as $item) {
            $productName = optional(optional($item->productVariants)->product)->name ?? 'Product';
            $unitPrice   = (int) round($item->price_at_purchase);

            $items[] = [
                'name'     => mb_substr($productName, 0, 50), // max 50 char Doku
                'price'    => $unitPrice,
                'quantity' => $item->quantity,
            ];
        }

        // ✅ Tambahkan ongkos kirim sebagai item tersendiri
        $shippingPrice = (int) $order->shipping_cost;
        if ($shippingPrice > 0) {
            $shippingName = $order->courier_code 
                ? strtoupper($order->courier_code) . ' - ' . $order->courier_service 
                : 'Ongkos Kirim - ' . $order->shippingMethods->name;
                
            $items[] = [
                'name'     => substr($shippingName, 0, 50),
                'price'    => $shippingPrice,
                'quantity' => 1,
            ];
        }

        $dokuPayload = [
            'order' => [
                'invoice_number' => $dokuInvoiceNumber,
                'amount' => (int) $order->total_price,
                'currency' => 'IDR',
                'callback_url' => config('app.frontend_url', 'https://arthakara.id') . '/orders',
                'line_items' => $items,
            ],
            'payment' => [
                'payment_due_date' => 60, // 60 menit
            ],
            'customer' => [
                'name' => $order->users->name ?? 'User',
                'email' => $order->users->email ?? 'user@email.com',
            ],
        ];

        $dokuResponse = $this->dokuService->createCheckout($dokuPayload);

        if ($dokuResponse && isset($dokuResponse['response']['payment']['url'])) {
            $paymentUrl = $dokuResponse['response']['payment']['url'];

            // Update order info
            $order->doku_invoice_number = $dokuInvoiceNumber;
            $order->doku_payment_url = $paymentUrl;
            $order->payment_status    = 'pending';
            $order->save();

            return response()->json([
                'payment_url'         => $paymentUrl,
                'doku_invoice_number' => $dokuInvoiceNumber,
                'isSuccess'           => true,
            ]);
        }

        return response()->json([
            'message'   => 'Gagal membuat transaksi Doku.',
            'isSuccess' => false,
        ], 500);
    }

    /**
     * POST /api/doku-callback
     * Dipanggil oleh Doku setelah transaksi selesai
     */
    public function dokuCallback(Request $request)
    {
        $signature = $request->header('Signature') ?? $request->header('X-Signature') ?? '';
        $clientId = $request->header('Client-Id') ?? '';
        $requestId = $request->header('Request-Id') ?? '';
        $timestamp = $request->header('Request-Timestamp') ?? '';

        Log::info('Doku Callback Received:', [
            'headers' => [
                'Signature' => $signature,
                'Client-Id' => $clientId,
                'Request-Id' => $requestId,
                'Request-Timestamp' => $timestamp,
            ],
            'body' => $request->all()
        ]);

        $isValid = $this->dokuService->verifyCallbackSignature(
            $request->getContent(),
            $signature,
            $clientId,
            $requestId,
            $timestamp,
            '/api/doku-callback'
        );

        if (!$isValid) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $invoiceNumber = $request->input('order.invoice_number');
        $transactionStatus = $request->input('transaction.status');

        $order = Order::with(['orderItems', 'shippingMethods', 'shippingAddresses', 'users'])
            ->where('doku_invoice_number', $invoiceNumber)
            ->first();

        if (!$order) {
            Log::warning('Doku callback: Order tidak ditemukan', ['doku_invoice_number' => $invoiceNumber]);
            return response()->json(['message' => 'Order tidak ditemukan'], 404);
        }

        $previousStatus = $order->payment_status;

        if (strtoupper($transactionStatus) === 'SUCCESS') {
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
        } else if (strtoupper($transactionStatus) === 'FAILED') {
            // ⚠️ Per Doku best practice: FAILED diabaikan karena customer
            // bisa ganti metode pembayaran di checkout page yang sama.
            // Jangan ubah status order.
            Log::info('Doku callback: FAILED status received, ignoring.', [
                'doku_invoice_number' => $invoiceNumber
            ]);
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