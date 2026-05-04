<?php

namespace App\Http\Controllers;

use App\Http\Requests\ColorCreateRequest;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ColorWebController extends Controller
{
    public function createColor(ColorCreateRequest $request) {
        try {
            $data = $request->validated();
    
            $color = new Color([
                'name' => $data['name'],
                'hex_code' => $data['hex_code']
            ]);
            $color->save();

            return redirect()->route('other.other')->with('success', 'Warna berhasil dibuat!');
        } catch (\Exception $ex) {
            Log::error('Color creation failed: ' . $ex->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat warna. Silakan coba lagi.');
        } 
    }

    public function getColors() {
        $colors = Color::all();
        return view('components.other.components.color.list_color', compact('colors'));
    }

    public function deleteColor(int $colorId) {
        $type = Color::findOrFail($colorId);
        $type->delete();

        return redirect()->route('colors.get')->with('success', 'Color deleted!');
    }
}

