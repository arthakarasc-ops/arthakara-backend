<?php

namespace App\Http\Controllers;

use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VariantWebController extends Controller
{
    public function createVariant(Request $request) {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|in:rasa,wangi',
            ]);
    
            $variant = new Variant($validated);
            $variant->save();

            return redirect()->route('other.other')->with('success', 'Variant created successfully!');
        } catch (\Exception $ex) {
            Log::error('Variant creation failed: ' . $ex->getMessage());
            return redirect()->back()->with('error', 'Failed to create variant. Please try again.');
        } 
    }

    public function getVariants() {
        $variants = Variant::all();

        return view('components.other.components.variant.list_variant', compact('variants'));
    }

    public function deleteVariant(int $variantId) {
        $variant = Variant::findOrFail($variantId);
        $variant->delete();

        return redirect()->route('variant.get')->with('success', 'Variant deleted!');
    }
}
