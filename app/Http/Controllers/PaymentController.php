<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class PaymentController extends Controller
{
    public function createTransaction(Request $request)
    {
        \Midtrans\Config::$serverKey = config('midtrans.serverKey');
        \Midtrans\Config::$isProduction = config('midtrans.isProduction');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        // ambil order dari database
        $order = Order::with(['orderItems.productVariants', 'users'])
            ->find($request->order_id);

        if (!$order) {
            return response()->json([
                'message' => 'Order tidak ditemukan'
            ], 404);
        }

        // generate ID untuk midtrans
        $midtransOrderId = 'MID-' . $order->id . '-' . time();

        // simpan ke database
        $order->midtrans_order_id = $midtransOrderId;
        $order->payment_status = 'pending';
        $order->save();

        // item details
        $items = [];

        foreach ($order->orderItems as $item) {
            $items[] = [
                'id' => $item->product_variant_id,
                'price' => $item->total_price / $item->quantity,
                'quantity' => $item->quantity,
                'name' => $item->productVariants->name ?? 'Product'
            ];
        }

        // params midtrans
        $params = [
            'transaction_details' => [
                'order_id' => $midtransOrderId,
                'gross_amount' => (int) $order->total_price,
            ],
            'item_details' => $items,
            'customer_details' => [
                'first_name' => $order->users->name ?? 'User',
                'email' => $order->users->email ?? 'user@email.com',
            ],
        ];

        // generate snap token
        $snapToken = \Midtrans\Snap::getSnapToken($params);

        return response()->json([
            'snap_token' => $snapToken
        ]);
    }

    public function callback(Request $request)
    {
        $serverKey = config('midtrans.serverKey');

        // validasi signature
        $signature = hash("sha512",
            $request->order_id .
            $request->status_code .
            $request->gross_amount .
            $serverKey
        );

        if ($signature !== $request->signature_key) {
            return response()->json([
                'message' => 'Invalid signature'
            ], 403);
        }

        // cari order
        $order = Order::where('midtrans_order_id', $request->order_id)->first();

        if (!$order) {
            return response()->json([
                'message' => 'Order tidak ditemukan'
            ], 404);
        }

        // update status
        switch ($request->transaction_status) {
            case 'settlement':
                $order->payment_status = 'paid';
                $order->status_id = 2; // sesuaikan dengan tabel status kamu
                break;

            case 'pending':
                $order->payment_status = 'pending';
                break;

            case 'expire':
                $order->payment_status = 'expired';
                $order->status_id = 3;
                break;

            case 'cancel':
                $order->payment_status = 'canceled';
                break;
        }

        $order->save();

        return response()->json(['message' => 'OK']);
    }
}