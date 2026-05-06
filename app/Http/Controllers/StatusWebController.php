<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatusAddRequest;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StatusWebController extends Controller
{
    public function createStatus(StatusAddRequest $request) {
        try {
            $data = $request->validated();
    
            $collection = new Status($data);
            $collection->save();

            return redirect()->route('status.get')->with('success', 'Status created successfully!');
        } catch (\Exception $ex) {
            Log::error('Status creation failed: ' . $ex->getMessage());
            return redirect()->back()->with('error', 'Failed to create status. Please try again.');
        } 
    }

    public function getStatuses() {
        $statuses = Status::all();

        return view('components.other.components.status.list_status', compact('statuses'));
    }

    public function showEditForm(int $statusId) {
        $status = Status::findOrFail($statusId);
        return view('components.other.components.status.edit_status', compact('status'));
    }

    public function updateStatus(Request $request, int $statusId) {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $status = Status::findOrFail($statusId);
            $status->update($validated);

            return redirect()->route('status.get')->with('success', 'Status updated successfully!');
        } catch (\Exception $ex) {
            Log::error('Status update failed: ' . $ex->getMessage());
            return redirect()->back()->with('error', 'Failed to update status.');
        }
    }

    public function deleteStatus(int $statusId) {
        $status = Status::findOrFail($statusId);
        $status->delete();

        return redirect()->route('status.get')->with('success', 'Status deleted!');
    }
}

