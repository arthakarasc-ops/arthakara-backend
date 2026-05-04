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
        $cart = CartItem::with(['product'])
            ->where('user_id', $request->user()->id)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product' => $item->product,
                    'color' => Color::find($item->color_id),
                    'scents' => Scent::whereIn('id', $item->scents)->get(),
                    'qty' => $item->qty,
                    'price' => $item->price,
                    'subtotal' => $item->price * $item->qty
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
            'product_id' => 'required|exists:products,id',
            'color_id' => 'required|exists:colors,id',
            'scents' => 'required|array|size:2',
            'scents.*' => 'exists:scents,id',
            'qty' => 'nullable|integer|min:1'
        ]);

        $user = $request->user();
        $qty = $request->qty ?? 1;

        return DB::transaction(function () use ($request, $user, $qty) {

            $product = Product::findOrFail($request->product_id);

            // 🔥 VALIDASI SCENT AKTIF
            $scents = Scent::whereIn('id', $request->scents)
                ->where('is_active', true)
                ->pluck('id')
                ->toArray();

            if (count($scents) !== 2) {
                return response()->json([
                    'error' => 'Invalid scents selected'
                ], 422);
            }

            // 🔥 SORT SCENT (BIAR CONSISTENT)
            sort($scents);

            // 🔥 HITUNG HARGA
            $extraPrice = Scent::whereIn('id', $scents)->sum('extra_price');
            $finalPrice = $product->price + $extraPrice;

            // 🔥 CEK ITEM SAMA
            $existingItem = CartItem::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->where('color_id', $request->color_id)
                ->get()
                ->first(function ($item) use ($scents) {
                    $itemScents = $item->scents;
                    sort($itemScents);
                    return $itemScents === $scents;
                });

            if ($existingItem) {
                $existingItem->increment('qty', $qty);

                return response()->json([
                    'message' => 'Cart updated (merged)',
                    'data' => $existingItem
                ]);
            }

            // 🔥 CREATE NEW ITEM
            $cartItem = CartItem::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'color_id' => $request->color_id,
                'scents' => $scents,
                'qty' => $qty,
                'price' => $finalPrice
            ]);

            return response()->json([
                'message' => 'Added to cart',
                'data' => $cartItem
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