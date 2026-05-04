<?php

namespace App\Http\Controllers;

use App\Models\ShippingMethod;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ShippingMethodController extends Controller
{
    /**
     * Get all shipping methods
     */
    public function getShippingMethods(): JsonResponse
    {
        try {
            $methods = ShippingMethod::all();

            return response()->json([
                'data' => $methods->map(function ($method) {
                    return [
                        'id' => $method->id,
                        'name' => $method->name,
                        'price' => (float)$method->price,
                        'description' => $method->description,
                    ];
                }),
                'isSuccess' => true
            ])->setStatusCode(200);
        } catch (Exception $e) {
            Log::error('Error fetching shipping methods: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan saat mengambil metode pengiriman.',
                'message' => $e->getMessage(),
                'isSuccess' => false
            ])->setStatusCode(500);
        }
    }

    /**
     * Get single shipping method
     */
    public function getShippingMethod(int $methodId): JsonResponse
    {
        try {
            $method = ShippingMethod::find($methodId);

            if (!$method) {
                return response()->json([
                    'error' => 'Metode pengiriman tidak ditemukan.',
                    'isSuccess' => false
                ])->setStatusCode(404);
            }

            return response()->json([
                'data' => [
                    'id' => $method->id,
                    'name' => $method->name,
                    'price' => (float)$method->price,
                    'description' => $method->description,
                ],
                'isSuccess' => true
            ])->setStatusCode(200);
        } catch (Exception $e) {
            Log::error('Error fetching shipping method: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan saat mengambil metode pengiriman.',
                'message' => $e->getMessage(),
                'isSuccess' => false
            ])->setStatusCode(500);
        }
    }
}

