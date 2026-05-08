<?php

namespace App\Http\Controllers;

use App\Models\Scent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScentWebController extends Controller
{
    public function createScent(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'        => 'required|string|max:255|unique:scents,name',
                'extra_price' => 'nullable|integer|min:0',
            ]);

            Scent::create([
                'name'        => $validated['name'],
                'extra_price' => $validated['extra_price'] ?? 0,
                'is_active'   => true,
            ]);

            return redirect()->route('scents.index')->with('success', 'Wangi berhasil dibuat!');
        } catch (\Exception $ex) {
            Log::error('Scent creation failed: ' . $ex->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat wangi: ' . $ex->getMessage());
        }
    }

    public function getScents()
    {
        $scents = Scent::all();
        return view('components.other.components.scent.list_scent', compact('scents'));
    }

    public function showEditForm(int $scentId)
    {
        $scent = Scent::findOrFail($scentId);
        return view('components.other.components.scent.edit_scent', compact('scent'));
    }

    public function updateScent(Request $request, int $scentId)
    {
        try {
            $scent = Scent::findOrFail($scentId);

            $validated = $request->validate([
                'name'        => 'required|string|max:255|unique:scents,name,' . $scentId,
                'extra_price' => 'nullable|integer|min:0',
                'is_active'   => 'nullable|boolean',
            ]);

            $scent->update([
                'name'        => $validated['name'],
                'extra_price' => $validated['extra_price'] ?? $scent->extra_price,
                'is_active'   => $validated['is_active'] ?? $scent->is_active,
            ]);

            return redirect()->route('scents.index')->with('success', 'Wangi berhasil diperbarui!');
        } catch (\Exception $ex) {
            Log::error('Scent update failed: ' . $ex->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui wangi: ' . $ex->getMessage());
        }
    }

    public function deleteScent(int $scentId)
    {
        try {
            $scent = Scent::findOrFail($scentId);
            $scent->delete();

            return redirect()->route('scents.index')->with('success', 'Wangi berhasil dihapus!');
        } catch (\Exception $ex) {
            Log::error('Scent deletion failed: ' . $ex->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus wangi.');
        }
    }

    public function toggleStatus(int $scentId)
    {
        try {
            $scent            = Scent::findOrFail($scentId);
            $scent->is_active = !$scent->is_active;
            $scent->save();

            $status = $scent->is_active ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()->back()->with('success', "Wangi berhasil {$status}!");
        } catch (\Exception $ex) {
            Log::error('Scent status toggle failed: ' . $ex->getMessage());
            return redirect()->back()->with('error', 'Gagal mengubah status wangi.');
        }
    }
}
