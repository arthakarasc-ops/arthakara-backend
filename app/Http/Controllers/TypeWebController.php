<?php

namespace App\Http\Controllers;

use App\Http\Requests\TypeAddRequest;
use App\Models\Type;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TypeWebController extends Controller
{
    public function createType(TypeAddRequest $request) {
        try {
            $data = $request->validated();
    
            $collection = new Type($data);
            $collection->save();

            return redirect()->route('type.get')->with('success', 'Type created successfully!');
        } catch (\Exception $ex) {
            Log::error('Collection creation failed: ' . $ex->getMessage());
            return redirect()->back()->with('error', 'Failed to create type. Please try again.');
        } 
    }

    public function getTypes() {
        $types = Type::all();

        return view('components.other.components.type.list_type', compact('types'));
    }

    public function showEditForm(int $typeId) {
        $type = Type::findOrFail($typeId);
        return view('components.other.components.type.edit_type', compact('type'));
    }

    public function updateType(Request $request, int $typeId) {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $type = Type::findOrFail($typeId);
            $type->update($validated);

            return redirect()->route('type.get')->with('success', 'Type updated successfully!');
        } catch (\Exception $ex) {
            Log::error('Type update failed: ' . $ex->getMessage());
            return redirect()->back()->with('error', 'Failed to update type.');
        }
    }

    public function deleteType(int $typeId) {
        $type = Type::findOrFail($typeId);
        $type->delete();

        return redirect()->route('type.get')->with('success', 'Type deleted!');
    }
}

