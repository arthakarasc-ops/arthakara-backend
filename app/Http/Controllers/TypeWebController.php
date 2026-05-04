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

            return redirect()->route('other.other')->with('success', 'Type created successfully!');
        } catch (\Exception $ex) {
            Log::error('Collection creation failed: ' . $ex->getMessage());
            return redirect()->back()->with('error', 'Failed to create type. Please try again.');
        } 
    }

    public function getTypes() {
        $types = Type::all();

        return view('components.other.components.type.list_type', compact('types'));
    }

    public function deleteType(int $typeId) {
        $type = Type::findOrFail($typeId);
        $type->delete();

        return redirect()->route('type.get')->with('success', 'Type deleted!');
    }
}

