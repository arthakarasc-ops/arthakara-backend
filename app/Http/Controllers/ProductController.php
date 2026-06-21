<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\RateLimiter;

class ProductController extends Controller
{
    /*
    |------------------------------------------
    | CREATE PRODUCT
    |------------------------------------------
    */
    public function createProduct(ProductCreateRequest $request): JsonResponse
    {
        try {
            $key = 'create-product:' . ($request->user()->email ?? 'guest');

            if (RateLimiter::tooManyAttempts($key, 3)) {
                return response()->json(['message' => 'Too many attempts'], 429);
            }

            RateLimiter::hit($key, 60);

            $product = Product::create($request->validated());

            if ($request->has('scent_ids')) {
                $product->scents()->sync($request->scent_ids);
            }

            return response()->json([
                'data'    => $product->load('variants.color', 'scents'),
                'message' => 'Product created successfully',
            ], 201);

        } catch (Exception $ex) {
            return response()->json([
                'message' => 'Failed to create product',
                'error'   => $ex->getMessage(),
            ], 500);
        }
    }

    /*
    |------------------------------------------
    | GET ALL PRODUCTS
    |------------------------------------------
    */
    public function getProducts(): JsonResponse
    {
        try {
            $products = Product::with([
                'collections',
                'types',
                'productUsageImages',
                'variants.color',
                'scents',
            ])->latest()->get();

            return response()->json([
                'data' => ProductResource::collection($products),
            ], 200);

        } catch (Exception $ex) {
            return response()->json([
                'message' => 'Failed to fetch products',
                'error'   => $ex->getMessage(),
            ], 500);
        }
    }

    /*
    |------------------------------------------
    | GET PRODUCTS PER COLLECTION
    |------------------------------------------
    */
    public function getProductsPerCollection(int $collectionId): JsonResponse
    {
        try {
            $products = Product::with([
                'collections',
                'types',
                'productUsageImages',
                'variants.color',
                'scents',
            ])
            ->where('collection_id', $collectionId)
            ->latest()
            ->get();

            return response()->json([
                'data' => ProductResource::collection($products),
            ], 200);

        } catch (Exception $ex) {
            return response()->json([
                'message' => 'Failed to fetch products',
                'error'   => $ex->getMessage(),
            ], 500);
        }
    }

    /*
    |------------------------------------------
    | GET PRODUCT DETAIL
    |------------------------------------------
    */
    public function getProductDetail(int $productId): JsonResponse
    {
        try {
            $product = Product::with([
                'productUsageImages',
                'variants.color',
                'scents',
            ])->find($productId);

            if (!$product) {
                return response()->json(['message' => 'Product not found'], 404);
            }

            return response()->json([
                'id'          => $product->id,
                'name'        => $product->name,
                'price'       => (int) ($product->price ?? 0),
                'description' => $product->description,

                'variants' => $product->variants->map(function ($v) {
                    return [
                        'id'    => $v->id,
                        'color' => optional($v->color)->name,
                        'stock' => $v->stock ?? 0,
                    ];
                })->values(),

                'scents' => $product->scents()
                    ->where('is_active', true)
                    ->get()
                    ->map(function ($s) {
                        return [
                            'id'          => $s->id,
                            'name'        => $s->name,
                            'extra_price' => (int) ($s->extra_price ?? 0),
                        ];
                    })->values(),

                'usage_image' => optional(
                    $product->productUsageImages->first()
                )->image_url ?? null,

                'usage_images' => $product->productUsageImages
                    ->map(fn($img) => $img->image_url)
                    ->values(),

            ], 200);

        } catch (Exception $ex) {
            return response()->json([
                'message' => 'Failed to fetch product detail',
                'error'   => $ex->getMessage(),
            ], 500);
        }
    }

    /*
    |------------------------------------------
    | UPDATE PRODUCT
    |------------------------------------------
    */
    public function updateProduct(int $productId, ProductUpdateRequest $request): JsonResponse
    {
        try {
            $product = Product::find($productId);

            if (!$product) {
                return response()->json(['message' => 'Product not found'], 404);
            }

            $product->update($request->validated());

            if ($request->has('scent_ids')) {
                $product->scents()->sync($request->scent_ids);
            }

            return response()->json([
                'data'    => $product->load('variants.color', 'scents'),
                'message' => 'Product updated successfully',
            ]);

        } catch (Exception $ex) {
            return response()->json([
                'message' => 'Failed to update product',
                'error'   => $ex->getMessage(),
            ], 500);
        }
    }

    /*
    |------------------------------------------
    | DELETE PRODUCT
    |------------------------------------------
    */
    public function deleteProduct(int $productId): JsonResponse
    {
        try {
            $product = Product::find($productId);

            if (!$product) {
                return response()->json(['message' => 'Product not found'], 404);
            }

            $product->scents()->detach();
            $product->delete();

            return response()->json(['message' => 'Product deleted successfully']);

        } catch (Exception $ex) {
            return response()->json([
                'message' => 'Failed to delete product',
                'error'   => $ex->getMessage(),
            ], 500);
        }
    }
}