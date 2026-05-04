<?php

namespace App\Http\Controllers;

use App\Models\Scent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class ScentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | GET ALL SCENTS (PUBLIC - USER)
    |--------------------------------------------------------------------------
    */
    public function index(): JsonResponse
    {
        $scents = Scent::where('is_active', true)->get();

        return response()->json([
            'data' => $scents
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE SCENT (ADMIN)
    |--------------------------------------------------------------------------
    */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:scents,name',
                'extra_price' => 'nullable|integer|min:0'
            ]);

            $scent = Scent::create([
                'name' => $validated['name'],
                'extra_price' => $validated['extra_price'] ?? 0,
                'is_active' => true
            ]);

            return response()->json([
                'message' => 'Scent created successfully',
                'data' => $scent,
                'isSuccess' => true
            ], 201);

        } catch (Exception $ex) {
            return response()->json([
                'error' => 'Failed to create scent',
                'message' => $ex->getMessage(),
                'isSuccess' => false
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE SCENT (ADMIN)
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $scent = Scent::find($id);

            if (!$scent) {
                return response()->json([
                    'error' => 'Scent not found'
                ], 404);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:scents,name,' . $id,
                'extra_price' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean'
            ]);

            $scent->update($validated);

            return response()->json([
                'message' => 'Scent updated successfully',
                'data' => $scent,
                'isSuccess' => true
            ], 200);

        } catch (Exception $ex) {
            return response()->json([
                'error' => 'Failed to update scent',
                'message' => $ex->getMessage(),
                'isSuccess' => false
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE / DISABLE SCENT (ADMIN)
    |--------------------------------------------------------------------------
    */
    public function destroy(int $id): JsonResponse
    {
        try {
            $scent = Scent::find($id);

            if (!$scent) {
                return response()->json([
                    'error' => 'Scent not found'
                ], 404);
            }

            // 🔥 Soft delete versi simple (disable)
            $scent->update([
                'is_active' => false
            ]);

            return response()->json([
                'message' => 'Scent disabled successfully',
                'isSuccess' => true
            ], 200);

        } catch (Exception $ex) {
            return response()->json([
                'error' => 'Failed to delete scent',
                'message' => $ex->getMessage(),
                'isSuccess' => false
            ], 500);
        }
    }
}