<?php

namespace App\Http\Controllers;

use App\Http\Resources\ColorResource;
use App\Http\Resources\FabricResource;
use App\Http\Resources\SizeResource;
use App\Models\Color;
use App\Models\Fabric;
use App\Models\Size;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function getColors(): JsonResponse {
        $colors = Color::all();

        return response()->json([
            'data' => ColorResource::collection($colors)
        ]);
    }

    public function getSizes(): JsonResponse {
        $colors = Size::all();

        return response()->json([
            'data' => SizeResource::collection($colors)
        ]);
    }

    public function getFabrics(): JsonResponse {
        $colors = Fabric::all();

        return response()->json([
            'data' => FabricResource::collection($colors)
        ]);
    }

    public function index()
    {
        return response()->json(Color::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $color = Color::create($validated);

        return response()->json($color, 201);
    }

    public function update(Request $request, Color $color)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $color->update($validated);

        return response()->json($color);
    }

    public function destroy(Color $color)
    {
        $color->delete();

        return response()->json(null, 204);
    }
}

