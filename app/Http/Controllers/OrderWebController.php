<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Scent;
use App\Models\Status;
use App\Services\FonnteService;
use Illuminate\Http\Request;

class OrderWebController extends Controller
{
    protected FonnteService $fonnteService;

    public function __construct(FonnteService $fonnteService)
    {
        $this->fonnteService = $fonnteService;
    }

    public function showOrders(Request $request)
    {
        $statusId = $request->query('status_id');

        $query = Order::with(['users', 'statuses', 'shippingMethods']);

        if ($statusId) {
            $query->where('status_id', $statusId);
        }

        $orders   = $query->latest()->get();
        $statuses = Status::all();

        return view('components.orders.list_orders', [
            'orders'           => $orders,
            'statuses'         => $statuses,
            'selectedStatusId' => $statusId,
        ]);
    }

    public function showDetail($orderId)
    {
        $order = Order::with([
            'users',
            'orderItems.productVariants.product',
            'orderItems.productVariants.color',
            'shippingAddresses',
            'billingAddresses',
            'shippingMethods',
            'statuses',
        ])->findOrFail($orderId);

        // Kumpulkan semua scent ID unik dari seluruh order items
        $allScentIds = [];
        foreach ($order->orderItems as $item) {
            foreach ($this->resolveScentIds($item) as $id) {
                $allScentIds[] = $id;
            }
        }

        // Satu query untuk semua scent yang dibutuhkan — key by ID
        $scentsMap = Scent::whereIn('id', array_unique($allScentIds))
            ->get()
            ->keyBy('id');

        // Pasang resolved scent names ke setiap order item
        foreach ($order->orderItems as $item) {
            $ids = $this->resolveScentIds($item);

            $item->resolved_scent_names = collect($ids)
                ->map(fn($id) => $scentsMap->get($id))
                ->filter()
                ->pluck('name');
        }

        $statuses = Status::all();

        return view('components.orders.detail_order', [
            'order'    => $order,
            'statuses' => $statuses,
        ]);
    }

    public function updateStatus(Request $request, $orderId)
    {
        $request->validate([
            'status_id'       => 'required|exists:statuses,id',
            'tracking_number' => 'nullable|string|max:100',
        ]);

        $order = Order::with(['shippingAddresses', 'users'])->findOrFail($orderId);

        $order->status_id = $request->status_id;

        $oldTrackingNumber = $order->tracking_number;

        // Jika ada input nomor resi dan resinya baru (atau diubah)
        if ($request->filled('tracking_number') && $oldTrackingNumber !== $request->tracking_number) {
            $order->tracking_number = $request->tracking_number;

            $isUpdate = !empty($oldTrackingNumber);
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

    /**
     * Kirim notifikasi WhatsApp nomor resi ke pelanggan.
     */
    private function sendWA(Order $order, bool $isUpdate = false): bool
    {
        if (!$order->shippingAddresses || !$order->shippingAddresses->phone_number) {
            return false;
        }

        $phone        = $order->shippingAddresses->phone_number;
        $customerName = $order->shippingAddresses->first_name;

        if ($isUpdate) {
            $message = "Halo Kak {$customerName},\n\n"
                     . "Terdapat *Pembaruan Nomor Resi* untuk Pesanan Arthakara Anda (ID: #{$order->id}).\n"
                     . "Nomor Resi Baru: *{$order->tracking_number}*\n\n"
                     . 'Terima kasih sudah berbelanja!';
        } else {
            $message = "Halo Kak {$customerName},\n\n"
                     . "Pesanan Arthakara Anda (ID: #{$order->id}) sudah dikirim!\n"
                     . "Nomor Resi: *{$order->tracking_number}*\n\n"
                     . 'Terima kasih sudah berbelanja!';
        }

        return $this->fonnteService->sendMessage($phone, $message);
    }

    /**
     * Resolve daftar integer scent ID dari satu order item.
     * Menangani berbagai format penyimpanan (array cast, JSON string, double-encoded).
     *
     * @return int[]
     */
    private function resolveScentIds($item): array
    {
        $raw = $item->getRawOriginal('scents');

        if (empty($raw) || $raw === 'null') {
            return [];
        }

        $ids = is_array($item->scents) ? $item->scents : [];

        if (empty($ids)) {
            $decoded = json_decode($raw, true);
            // Tangani double-encoded JSON
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }
            $ids = is_array($decoded) ? $decoded : [];
        }

        return array_values(array_filter(array_map('intval', $ids)));
    }
}
