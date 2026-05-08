<?php

namespace App\Http\Controllers;

use App\Http\Requests\ColorCreateRequest;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ColorWebController extends Controller
{
    public function createColor(ColorCreateRequest $request)
    {
        try {
            $data = $request->validated();

            Color::create([
                'name'     => $data['name'],
            ]);

            return redirect()->route('colors.index')->with('success', 'Warna berhasil dibuat!');
        } catch (\Exception $ex) {
            Log::error('Color creation failed: ' . $ex->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat warna. Silakan coba lagi.');
        }
    }

    public function getColors()
    {
        $colors = Color::all();
        return view('components.other.components.color.list_color', compact('colors'));
    }

    public function showEditForm(int $colorId)
    {
        $color = Color::findOrFail($colorId);
        return view('components.other.components.color.edit_color', compact('color'));
    }

    public function updateColor(Request $request, int $colorId)
    {
        try {
            $validated = $request->validate([
                'name'     => 'required|string|max:255',
            ]);

            $color = Color::findOrFail($colorId);
            $color->update($validated);

            return redirect()->route('colors.index')->with('success', 'Warna berhasil diperbarui!');
        } catch (\Exception $ex) {
            Log::error('Color update failed: ' . $ex->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui warna.');
        }
    }

    public function deleteColor(int $colorId)
    {
        try {
            $color = Color::findOrFail($colorId);
            $color->delete();

            return redirect()->route('colors.index')->with('success', 'Warna berhasil dihapus!');
        } catch (\Exception $ex) {
            Log::error('Color deletion failed: ' . $ex->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus warna.');
        }
    }
}
