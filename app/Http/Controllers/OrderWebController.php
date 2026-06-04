<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Status;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderWebController extends Controller
{
    public function showOrders(Request $request)
    {
        $statusId = $request->query('status_id');

        $query = Order::with(['users', 'statuses', 'shippingMethods']);

        if ($statusId) {
            $query->where('status_id', $statusId);
        }

        $orders = $query->latest()->get();
        $statuses = Status::all();

        return view('components.orders.list_orders', [
            'orders' => $orders,
            'statuses' => $statuses,
            'selectedStatusId' => $statusId
        ]);
    }

    public function showDetail($orderId)
    {
        $order = Order::with([
            'users',
            'orderItems.productVariants.product',
            'shippingAddresses',
            'billingAddresses',
            'shippingMethods',
            'statuses'
        ])->findOrFail($orderId);

        $statuses = Status::all();

        return view('components.orders.detail_order', [
            'order' => $order,
            'statuses' => $statuses
        ]);
    }

    public function updateStatus(Request $request, $orderId)
    {
        $request->validate([
            'status_id' => 'required|exists:statuses,id',
            'tracking_number' => 'nullable|string|max:100',
        ]);

        $order = Order::with(['shippingAddresses', 'users'])->findOrFail($orderId);

        // Validasi midtrans dimatikan sementara agar admin bisa proses order via WA
        // if ($order->payment_status !== 'paid' && !in_array($request->status_id, [1, 5])) {
        //     return redirect()->back()->with('error', 'Gagal memperbarui status: Pesanan ini belum lunas dibayar via Midtrans!');
        // }

        $order->status_id = $request->status_id;

        $oldTrackingNumber = $order->tracking_number;

        // Jika ada input nomor resi dan resinya baru (atau diubah)
        if ($request->filled('tracking_number') && $oldTrackingNumber !== $request->tracking_number) {
            $order->tracking_number = $request->tracking_number;
            
            // Tentukan apakah ini resi pertama kali atau perubahan resi
            $isUpdate = !empty($oldTrackingNumber);
            
            // Kirim notifikasi WA otomatis
            $this->sendWA($order, $isUpdate);
        }

        $order->save();

        return redirect()->route('orders.detail', $orderId)->with('success', 'Status dan/atau Resi berhasil diperbarui.');
    }

    public function resendTrackingWA($orderId)
    {
        $order = Order::with(['shippingAddresses', 'users'])->findOrFail($orderId);

        if (!$order->tracking_number) {
            return redirect()->back()->with('error', 'Nomor resi belum diisi.');
        }

        $sent = $this->sendWA($order);

        if ($sent) {
            return redirect()->back()->with('success', 'WhatsApp resi berhasil dikirim ulang.');
        }

        return redirect()->back()->with('error', 'Gagal mengirim WhatsApp. Pastikan No. HP tersedia dan Fonnte terhubung.');
    }

    private function sendWA($order, $isUpdate = false)
    {
        if ($order->shippingAddresses && $order->shippingAddresses->phone_number) {
            $phone = $order->shippingAddresses->phone_number;
            $customerName = $order->shippingAddresses->first_name;
            
            if ($isUpdate) {
                $message = "Halo Kak {$customerName},\n\n"
                         . "Terdapat *Pembaruan Nomor Resi* untuk Pesanan Arthakara Anda (ID: #{$order->id}).\n"
                         . "Nomor Resi Baru: *{$order->tracking_number}*\n\n"
                         . "Terima kasih sudah berbelanja!";
            } else {
                $message = "Halo Kak {$customerName},\n\n"
                         . "Pesanan Arthakara Anda (ID: #{$order->id}) sudah dikirim!\n"
                         . "Nomor Resi: *{$order->tracking_number}*\n\n"
                         . "Terima kasih sudah berbelanja!";
            }
            
            $fonnte = new \App\Services\FonnteService();
            return $fonnte->sendMessage($phone, $message);
        }
        return false;
    }

    // public function getOrders(Request $request) {
    //     $statuses = Status::all();

    //     $date = $request->query('date');
    //     $statusId = $request->query('status_id');

    //     $orders = Order::with(['users', 'statuses']);

    //     if ($date) {
    //         $orders->whereDate('created_at', $date);
    //     }

    //     if ($statusId) {
    //         $orders->where('status_id', $statusId);
    //     }

    //     return view('orders.components.list-orders', [
    //         'orders' => $orders->get(),
    //         'statuses' => $statuses,
    //         'filters' => [
    //             'date' => $date,
    //             'status_id' => $statusId
    //         ]
    //     ]);
    // }

    // public function updateOrderStatus(Request $request, $orderId): JsonResponse {
    //     $order = Order::findOrFail($orderId);
    //     $request->validate([
    //         'status_id' => 'required|exists:statuses,id',
    //     ]);

    //     $order->status_id = $request->status_id;
    //     $order->save();

    //     return response()->json([
    //         'message' => 'Status updated successfully'
    //     ]);
    // }
}



