<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\Scent;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | GET USER CART
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): JsonResponse
    {
        // 🔥 EAGER LOADING: Ambil semua data dalam 1 query (Product, Color)
        $cartItems = CartItem::with(['productVariant.product', 'productVariant.color'])
            ->where('user_id', $request->user()->id)
            ->get();

        // Ambil semua scent IDs unik dari seluruh cart untuk optimasi
        $allScentIds = $cartItems->pluck('scents')->flatten()->unique()->toArray();
        $scentsMap = Scent::whereIn('id', $allScentIds)->get()->keyBy('id');

        $cart = $cartItems->map(function ($item) use ($scentsMap) {
            $variant = $item->productVariant;
            $product = optional($variant)->product;
            
            return [
                'id' => $item->id,
                'product_variant_id' => $item->product_variant_id,
                'product_name' => optional($product)->name,
                'color' => optional($variant->color)->name,
                'color_hex' => optional($variant->color)->hex_code,
                'image_url' => $variant->image_url,
                'scents' => collect($item->scents)->map(fn($id) => $scentsMap->get($id)),
                'qty' => $item->qty,
                'price' => (int) $item->price,
                'subtotal' => (int) ($item->price * $item->qty),
                'stock_available' => optional($product)->stock ?? 0,
            ];
        });

        return response()->json([
            'data' => $cart
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ADD TO CART (🔥 PALING TRICKY)
    |--------------------------------------------------------------------------
    */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'scents' => 'nullable|array',
            'scents.*' => 'exists:scents,id',
            'qty' => 'nullable|integer|min:1'
        ]);

        $user = $request->user();
        $qty = $request->qty ?? 1;

        return DB::transaction(function () use ($request, $user, $qty) {
            $variant = \App\Models\ProductVariant::with('product')->findOrFail($request->product_variant_id);
            $product = $variant->product;

            $scents = [];
            $extraPrice = 0;

            if ($request->has('scents') && !empty($request->scents)) {
                $validScents = Scent::whereIn('id', $request->scents)
                    ->where('is_active', true)
                    ->get()
                    ->keyBy('id');

                foreach ($request->scents as $requestedId) {
                    if (!$validScents->has($requestedId)) {
                        return response()->json(['error' => 'Salah satu wangi tidak aktif atau tidak ditemukan.'], 422);
                    }
                    $scents[] = (int) $requestedId;
                    $extraPrice += $validScents->get($requestedId)->extra_price;
                }

                // SORT SCENT UNTUK CEK DUPLIKASI DI CART
                sort($scents);
            }

            // ✅ CEK STOK
            if ($product->stock < $qty) {
                return response()->json(['error' => 'Stok tidak mencukupi. Sisa: ' . $product->stock], 422);
            }

            // HITUNG HARGA (Base Price + Extra Price Scent)
            $finalPrice = $product->price + $extraPrice;

            // CEK APAKAH ITEM YANG SAMA SUDAH ADA DI KERANJANG
            $existingItem = CartItem::where('user_id', $user->id)
                ->where('product_variant_id', $variant->id)
                ->get()
                ->first(function ($item) use ($scents) {
                    $itemScents = $item->scents;
                    sort($itemScents);
                    return $itemScents === $scents;
                });

            if ($existingItem) {
                $newQty = $existingItem->qty + $qty;
                if ($product->stock < $newQty) {
                    return response()->json(['error' => 'Total di keranjang melebihi stok yang tersedia.'], 422);
                }
                
                $existingItem->increment('qty', $qty);

                return response()->json([
                    'message' => 'Jumlah produk di keranjang diperbarui.',
                    'data' => $existingItem
                ]);
            }

            // CREATE ITEM BARU
            $cartItem = CartItem::create([
                'user_id' => $user->id,
                'product_variant_id' => $variant->id,
                'scents' => $scents,
                'qty' => $qty,
                'price' => $finalPrice
            ]);

            return response()->json([
                'message' => 'Berhasil ditambahkan ke keranjang.',
                'data' => $cartItem
            ], 201);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | BULK ADD TO CART
    |--------------------------------------------------------------------------
    */
    public function storeBulk(Request $request): JsonResponse
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_variant_id' => 'required|exists:product_variants,id',
            'items.*.scents' => 'nullable|array',
            'items.*.scents.*' => 'exists:scents,id',
            'items.*.qty' => 'nullable|integer|min:1'
        ]);

        $user = $request->user();

        return DB::transaction(function () use ($request, $user) {
            $addedItems = [];

            foreach ($request->items as $itemData) {
                $variantId = $itemData['product_variant_id'];
                $qty = $itemData['qty'] ?? 1;
                $requestScents = $itemData['scents'] ?? [];

                $variant = \App\Models\ProductVariant::with('product')->findOrFail($variantId);
                $product = $variant->product;

                $scents = [];
                $extraPrice = 0;

                if (!empty($requestScents)) {
                    $validScents = Scent::whereIn('id', $requestScents)
                        ->where('is_active', true)
                        ->get()
                        ->keyBy('id');

                    foreach ($requestScents as $requestedId) {
                        if (!$validScents->has($requestedId)) {
                            return response()->json(['error' => 'Salah satu wangi tidak aktif.'], 422);
                        }
                        $scents[] = (int) $requestedId;
                        $extraPrice += $validScents->get($requestedId)->extra_price;
                    }
                    sort($scents);
                }

                if ($product->stock < $qty) {
                    return response()->json(['error' => 'Stok tidak mencukupi untuk ' . $product->name], 422);
                }

                $finalPrice = $product->price + $extraPrice;

                $existingItem = CartItem::where('user_id', $user->id)
                    ->where('product_variant_id', $variant->id)
                    ->get()
                    ->first(function ($item) use ($scents) {
                        $itemScents = $item->scents;
                        sort($itemScents);
                        return $itemScents === $scents;
                    });

                if ($existingItem) {
                    $newQty = $existingItem->qty + $qty;
                    if ($product->stock < $newQty) {
                        return response()->json(['error' => 'Total di keranjang melebihi stok yang tersedia untuk ' . $product->name], 422);
                    }
                    $existingItem->increment('qty', $qty);
                    $addedItems[] = $existingItem;
                } else {
                    $cartItem = CartItem::create([
                        'user_id' => $user->id,
                        'product_variant_id' => $variant->id,
                        'scents' => $scents,
                        'qty' => $qty,
                        'price' => $finalPrice
                    ]);
                    $addedItems[] = $cartItem;
                }
            }

            return response()->json([
                'message' => 'Berhasil ditambahkan ke keranjang.',
                'data' => $addedItems
            ], 201);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE QTY
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'qty' => 'required|integer|min:1'
        ]);

        $item = CartItem::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $item->update([
            'qty' => $request->qty
        ]);

        return response()->json([
            'message' => 'Cart updated',
            'data' => $item
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVE ITEM
    |--------------------------------------------------------------------------
    */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $item = CartItem::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $item->delete();

        return response()->json([
            'message' => 'Item removed'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CLEAR CART
    |--------------------------------------------------------------------------
    */
    public function clear(Request $request): JsonResponse
    {
        CartItem::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'message' => 'Cart cleared'
        ]);
    }
}