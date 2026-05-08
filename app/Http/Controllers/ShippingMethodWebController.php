<?php

namespace App\Http\Controllers;

use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShippingMethodWebController extends Controller
{
    public function index()
    {
        $shippingMethods = ShippingMethod::all();
        return view('components.other.components.shipping.list_shipping', compact('shippingMethods'));
    }

    public function create()
    {
        return view('components.other.components.shipping.create_shipping');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'description' => 'nullable|string|max:500',
            ]);

            ShippingMethod::create($validated);

            return redirect()->route('shippings.index')->with('success', 'Metode pengiriman berhasil ditambahkan!');
        } catch (\Exception $ex) {
            Log::error('Shipping creation failed: ' . $ex->getMessage());
            return redirect()->back()->with('error', 'Gagal menambah pengiriman.')->withInput();
        }
    }

    public function edit($id)
    {
        $shipping = ShippingMethod::findOrFail($id);
        return view('components.other.components.shipping.edit_shipping', compact('shipping'));
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'description' => 'nullable|string|max:500',
            ]);

            $shipping = ShippingMethod::findOrFail($id);
            $shipping->update($validated);

            return redirect()->route('shippings.index')->with('success', 'Metode pengiriman berhasil diperbarui!');
        } catch (\Exception $ex) {
            Log::error('Shipping update failed: ' . $ex->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui pengiriman.')->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $shipping = ShippingMethod::findOrFail($id);
            
            // Cek apakah ada order yang menggunakan metode ini
            if ($shipping->orders()->count() > 0) {
                return redirect()->back()->with('error', 'Gagal menghapus! Metode ini sudah digunakan oleh beberapa pesanan.');
            }

            $shipping->delete();
            return redirect()->route('shippings.index')->with('success', 'Metode pengiriman berhasil dihapus!');
        } catch (\Exception $ex) {
            Log::error('Shipping deletion failed: ' . $ex->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus pengiriman.');
        }
    }
}
