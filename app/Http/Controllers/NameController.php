<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NameController extends Controller
{
    public function getAllProductCollectionNames(): JsonResponse {
        $products = Product::all()->map(function ($item) {
            return [
                'id' => $item->id,
                'type' => 'Product',
                'name' => $item->name
            ];
        });

        $collections = Collection::all()->map(function ($item) {
            return [
                'id' => $item->id,
                'type' => 'Collection',
                'name' => $item->name
            ];
        });

        $result = $products->concat($collections)->values();

        return response()->json([
            'data' => $result
        ], 200);
    }
}

