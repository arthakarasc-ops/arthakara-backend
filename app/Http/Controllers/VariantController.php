<?php

namespace App\Http\Controllers;

use App\Models\Variant;
use Illuminate\Http\Request;

class VariantController extends Controller
{
    public function index()
    {
        return response()->json(Variant::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:rasa,wangi',
        ]);

        $variant = Variant::create($validated);

        return response()->json($variant, 201);
    }

    public function update(Request $request, Variant $variant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:rasa,wangi',
        ]);

        $variant->update($validated);

        return response()->json($variant);
    }

    public function destroy(Variant $variant)
    {
        $variant->delete();

        return response()->json(null, 204);
    }
}