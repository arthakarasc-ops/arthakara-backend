<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderCreateRequest;
use App\Http\Resources\OrderItemResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderUserResource;
use App\Models\Address;
use App\Models\BillingAddress;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingMethod;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

class OrderController extends Controller
{
    public function getOrders(Request $request): JsonResponse
    {
        $date     = $request->query('date');
        $statusId = $request->query('status_id');

        $query = Order::query()->with(['users', 'statuses']);

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        if ($statusId) {
            $query->where('status_id', $statusId);
        }

        return response()->json([
            'data' => OrderResource::collection($query->get())
        ])->setStatusCode(200);
    }

    public function getUserOrders(Request $request): JsonResponse
    {
        $user     = Auth::user();
        $date     = $request->query('date');
        $statusId = $request->query('status_id');

        $query = Order::query()
            ->with(['users', 'statuses'])
            ->where('user_id', $user->id);

        if ($date != null) {
            $query->whereDate('created_at', $date);
        }

        if ($statusId != null) {
            $query->where('status_id', $statusId);
        }

        return response()->json([
            'data' => OrderUserResource::collection($query->get())
        ])->setStatusCode(200);
    }

    public function getOrderDetail(int $orderId): JsonResponse
    {
        $order = Order::with([
            'users',
            'orderItems.productVariants.product',
            'orderItems.productVariants.color',
            'shippingAddresses',
            'billingAddresses',
            'shippingMethods',
            'statuses',
        ])->where('id', $orderId)->first();

        if (!$order) {
            throw new HttpResponseException(response()->json([
                'error' => 'Order not found.'
            ])->setStatusCode(404));
        }

        $orderItems = OrderItem::with([
            'productVariants.product',
            'productVariants.color',
        ])->where('order_id', $orderId)->get();

        return response()->json([
            'data' => [
                'order_id'        => $order->id,
                'payment_status'  => $order->payment_status,
                'tracking_number' => $order->tracking_number,
                'user'            => $order->users->email,
                'shipping' => [
                    'first_name'       => $order->shippingAddresses->first_name,
                    'last_name'        => $order->shippingAddresses->last_name,
                    'address'          => $order->shippingAddresses->address,
                    'appartment_suite' => $order->shippingAddresses->appartment_suite,
                    'city'             => $order->shippingAddresses->city,
                    'province'         => $order->shippingAddresses->province,
                    'country'          => $order->shippingAddresses->country,
                    'phone_number'     => $order->shippingAddresses->phone_number,
                ],
                'billing_address' => [
                    'first_name'       => $order->billingAddresses->first_name,
                    'last_name'        => $order->billingAddresses->last_name,
                    'address'          => $order->billingAddresses->address,
                    'appartment_suite' => $order->billingAddresses->appartment_suite,
                    'city'             => $order->billingAddresses->city,
                    'province'         => $order->billingAddresses->province,
                    'country'          => $order->billingAddresses->country,
                ],
                'shipping_method' => [
                    'name'  => $order->shippingMethods->name,
                    'price' => (int) $order->shippingMethods->price,
                ],
                'total_price' => (int) $order->total_price,
                'status'      => $order->statuses->name,
                'created_at'  => $order->created_at->format('d-M-y'),
                'updated_at'  => $order->updated_at->format('d-M-y'),
                'items'       => OrderItemResource::collection($orderItems),
            ]
        ])->setStatusCode(200);
    }

    public function createNewOrder(OrderCreateRequest $request): JsonResponse
    {
        $user         = Auth::user();
        $decayMinutes = 1;
        $maxAttemps   = 3;
        $key          = 'create-order: ' . $user->email;

        if (RateLimiter::tooManyAttempts($key, $maxAttemps)) {
            $second = RateLimiter::availableIn($key);
            throw new HttpResponseException(response()->json([
                'error' => 'Too many attempts. Please try again after ' . $second . ' seconds.'
            ]));
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        $data = $request->validated();

        try {
            DB::beginTransaction();

            // ✅ Validasi: 2 scent harus BERBEDA per item
            foreach ($data['items'] as $index => $item) {
                if (count(array_unique($item['scents'])) !== 2) {
                    throw new Exception("Item ke-" . ($index + 1) . ": Pilih 2 wangi yang berbeda (tidak boleh memilih wangi yang sama 2x).");
                }
            }

            $shippingAddress = Address::create([
                'user_id'          => $user->id,
                'is_default'       => false,
                'first_name'       => $data['shipping_address']['first_name'],
                'last_name'        => $data['shipping_address']['last_name'],
                'address'          => $data['shipping_address']['address'],
                'appartment_suite' => $data['shipping_address']['appartment_suite'] ?? null,
                'city'             => $data['shipping_address']['city'],
                'province'         => $data['shipping_address']['province'],
                'postal_code'      => $data['shipping_address']['postal_code'],
                'country'          => $data['shipping_address']['country'],
                'phone_number'     => $data['shipping_address']['phone_number'],
            ]);

            $billingAddress = BillingAddress::create([
                'first_name'       => $data['billing_address']['first_name'],
                'last_name'        => $data['billing_address']['last_name'],
                'address'          => $data['billing_address']['address'],
                'appartment_suite' => $data['billing_address']['appartment_suite'] ?? null,
                'city'             => $data['billing_address']['city'],
                'province'         => $data['billing_address']['province'],
                'postal_code'      => $data['billing_address']['postal_code'],
                'country'          => $data['billing_address']['country'],
                'phone_number'     => $data['billing_address']['phone_number'],
            ]);

            $shippingMethod = ShippingMethod::find($data['shipping_method_id']);

            if (!$shippingMethod) {
                throw new Exception('Metode pengiriman tidak ditemukan.');
            }

            $itemTotalPrice = 0;
            $processedItems = [];

            foreach ($data['items'] as $item) {
                $variant = \App\Models\ProductVariant::with('product')->find($item['product_variant_id']);

                if (!$variant || !$variant->product) {
                    throw new Exception('Produk atau varian tidak ditemukan.');
                }

                // ✅ Validasi stok produk
                if ($variant->product->stock < $item['quantity']) {
                    throw new Exception(
                        'Stok tidak mencukupi untuk produk: ' . $variant->product->name .
                        '. Sisa stok: ' . $variant->product->stock
                    );
                }

                // ✅ Validasi semua scent aktif
                $scents = \App\Models\Scent::whereIn('id', $item['scents'])
                    ->where('is_active', true)
                    ->get();

                if ($scents->count() !== count($item['scents'])) {
                    throw new Exception('Salah satu wangi yang dipilih sudah tidak aktif.');
                }

                $extraPrice = $scents->sum('extra_price');
                $unitPrice  = $variant->product->price + $extraPrice;
                $itemTotal  = $unitPrice * $item['quantity'];

                $itemTotalPrice += $itemTotal;

                $processedItems[] = [
                    'product_variant_id' => $item['product_variant_id'],
                    'quantity'           => $item['quantity'],
                    'price_at_purchase'  => $unitPrice,
                    'total_price'        => $itemTotal,
                    'scents'             => $item['scents'],
                ];
            }

            $orderTotal = $itemTotalPrice + $shippingMethod->price;

            $order = Order::create([
                'user_id'             => $user->id,
                'shipping_address_id' => $shippingAddress->id,
                'billing_address_id'  => $billingAddress->id,
                'shipping_method_id'  => $data['shipping_method_id'],
                'total_price'         => $orderTotal,
                'status_id'           => 1, // Pending
                'payment_status'      => 'unpaid',
                'tanggal_lahir'       => $data['tanggal_lahir'],
            ]);

            foreach ($processedItems as $pItem) {
                OrderItem::create([
                    'order_id'           => $order->id,
                    'product_variant_id' => $pItem['product_variant_id'],
                    'quantity'           => $pItem['quantity'],
                    'price_at_purchase'  => $pItem['price_at_purchase'],
                    'total_price'        => $pItem['total_price'],
                    'scents'             => $pItem['scents'],
                ]);
            }

            // ✅ Kosongkan cart setelah order dibuat
            CartItem::where('user_id', $user->id)->delete();

            DB::commit();

            return response()->json([
                'message'   => 'Order berhasil dibuat.',
                'data'      => [
                    'id'          => $order->id,
                    'order_id'    => $order->id,
                    'total_price' => (int) $order->total_price,
                    'created_at'  => $order->created_at->format('d-m-Y'),
                ],
                'isSuccess' => true,
            ])->setStatusCode(200);

        } catch (Exception $ex) {
            DB::rollBack();
            return response()->json([
                'message'   => 'Gagal membuat order.',
                'error'     => $ex->getMessage(),
                'isSuccess' => false,
            ])->setStatusCode(500);
        }
    }
}
