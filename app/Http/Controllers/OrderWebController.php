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

        $query = Order::with(['users', 'statuses']);

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
            'orderItems.productVariants.products',
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
        ]);

        $order = Order::findOrFail($orderId);
        $order->status_id = $request->status_id;
        $order->save();

        return redirect()->route('order.detail', $orderId)->with('success', 'Status berhasil diperbarui.');
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



