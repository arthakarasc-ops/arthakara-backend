<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductVariantCreateRequest;
use App\Http\Requests\ProductVariantUpdateRequest;
use App\Models\ProductVariant;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class ProductVariantController extends Controller
{
    public function createProductVariant(int $productId, ProductVariantCreateRequest $request): JsonResponse {
        try {
            $user = Auth::user();
    
            $decayMinutes = 1;
            $maxAttemps = 3;
            $key = 'create-product-variant: ' . $user->email;
    
            if (RateLimiter::tooManyAttempts($key, $maxAttemps)) {
                $second = RateLimiter::availableIn($key);
    
                throw new HttpResponseException(response()->json([
                    'error' => 'Too many attempts. Please try again after ' . $second . ' seconds'
                ]));
            }
    
            RateLimiter::hit($key, $decayMinutes * 60);
    
            $data = $request->validated();
            $productVariant = ProductVariant::create([
                'product_id' => $productId,
                'color_id' => $data['color_id'],
                'image_url' => $data['image_url'],
                'stock' => $data['stock']
            ]);
    
            $productVariant->load('color');
    
            return response()->json([
                'message' => 'Product variant created successfully',
                'data' => [
                    'id' => $productVariant->id,
                    'color' => optional($productVariant->color)->name,
                    'color_hex' => optional($productVariant->color)->hex_code,
                    'image_url' => $productVariant->image_url,
                    'stock' => $productVariant->stock,
                    'created_at' => $productVariant->created_at->format('d-M-y')
                ],
                'isSuccess' => true
            ])->setStatusCode(201);
        } catch (Exception $ex) {
            throw new HttpResponseException(response()->json([
                'error' => 'Something went wrong.',
                'message' => $ex->getMessage(),
                'isSuccess' => false
            ])->setStatusCode(500));
        } 
    }

    public function updateProductVariant(int $productVariantId, ProductVariantUpdateRequest $request): JsonResponse {
        try {

            $user = Auth::user();
    
            $decayMinutes = 1;
            $maxAttemps = 3;
            $key = 'update-product-variant: ' . $user->email;
    
            if (RateLimiter::tooManyAttempts($key, $maxAttemps)) {
                $second = RateLimiter::availableIn($key);
    
                throw new HttpResponseException(response()->json([
                    'error' => 'Too many attempts. Please try again after ' . $second . ' seconds'
                ])->setStatusCode(429));
            }
    
            $productVariant = ProductVariant::where('id', $productVariantId)->first();
            if (!$productVariant) {
                throw new HttpResponseException(response()->json([
                    'error' => 'Product variant not found.'
                ])->setStatusCode(404));
            }
                
            RateLimiter::hit($key, $decayMinutes * 60);
    
            $data = $request->validated();
            $productVariant->update([
                'color_id' => $data['color_id'] ?? $productVariant->color_id,
                'image_url' => $data['image_url'],
                'stock' => $data['stock']
            ]);
            $productVariant->save();
            $productVariant->load('color');
    
            return response()->json([
                'message' => 'Product variant updated successfully.',
                'data' => [
                    'id' => $productVariant->id,
                    'color' => optional($productVariant->color)->name,
                    'color_hex' => optional($productVariant->color)->hex_code,
                    'image_url' => $productVariant->image_url,
                    'stock' => $productVariant->stock,
                    'updated_at' => $productVariant->updated_at->format('d-M-y')
                ],
                'isSuccess' => true
            ])->setStatusCode(200);
        } catch (Exception $ex) {
            throw new HttpResponseException(response()->json([
                'error' => 'Something went wrong.',
                'message' => $ex->getMessage(),
                'isSuccess' => false
            ])->setStatusCode(500));
        } 
    }

    public function deleteProductVariant(int $productVariantId): JsonResponse {
        try {
            $productVariant = ProductVariant::where('id', $productVariantId)->first();

            if (!$productVariant) {
                throw new HttpResponseException(response()->json([
                    'error' => 'Product not found.'
                ])->setStatusCode(404));
            }

            $productVariant->delete();

            return response()->json([
                'message' => 'Product variant deleted successfully.',
                'isSuccess' => true
            ])->setStatusCode(200);
        } catch(Exception $ex) {
            throw new HttpResponseException(response()->json([
                'error' => 'Something went wrong.',
                'message' => $ex->getMessage(),
                'isSuccess' => false
            ])->setStatusCode(500));
        }
    }

}

