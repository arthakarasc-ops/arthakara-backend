<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScentCreateRequest;
use App\Models\Scent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScentWebController extends Controller
{
    /**
     * Create a new scent
     */
    public function createScent(ScentCreateRequest $request)
    {
        try {
            $data = $request->validated();
    
            $scent = new Scent([
                'name' => $data['name'],
                'extra_price' => $data['extra_price'] ?? 0,
                'is_active' => true
            ]);
            $scent->save();

            return redirect()->route('other.other')->with('success', 'Wangi berhasil dibuat!');
        } catch (\Exception $ex) {
            Log::error('Scent creation failed: ' . $ex->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat wangi. Silakan coba lagi.');
        } 
    }

    /**
     * Get all scents
     */
    public function getScents()
    {
        $scents = Scent::all();
        return view('components.other.components.scent.list_scent', compact('scents'));
    }

    /**
     * Delete a scent
     */
    public function deleteScent(int $scentId)
    {
        try {
            $scent = Scent::findOrFail($scentId);
            $scent->delete();

            return redirect()->route('scent.get')->with('success', 'Wangi berhasil dihapus!');
        } catch (\Exception $ex) {
            Log::error('Scent deletion failed: ' . $ex->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus wangi.');
        }
    }

    /**
     * Toggle scent active status
     */
    public function toggleStatus(int $scentId)
    {
        try {
            $scent = Scent::findOrFail($scentId);
            $scent->is_active = !$scent->is_active;
            $scent->save();

            $status = $scent->is_active ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()->back()->with('success', "Wangi berhasil $status!");
        } catch (\Exception $ex) {
            Log::error('Scent status toggle failed: ' . $ex->getMessage());
            return redirect()->back()->with('error', 'Gagal mengubah status wangi.');
        }
    }
}
