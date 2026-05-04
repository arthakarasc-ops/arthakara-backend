<?php

namespace App\Http\Controllers;

use App\Http\Requests\TypeAddRequest;
use App\Http\Resources\TypeResource;
use App\Models\Type;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TypeController extends Controller
{
    public function createType(TypeAddRequest $request): JsonResponse {
        try {
            $data = $request->validated();
    
            $collection = new Type($data);
            $collection->save();
    
            return response()->json([
                'message' => 'Collection created successfully',
                'data' => new TypeResource($data)
            ], 201);
        } catch (Exception $ex) {
            throw new HttpResponseException(response()->json([
                'error' => 'Something went wrong.',
                'message' => $ex->getMessage()
            ])->setStatusCode(500));
        } 
    }

    public function getTypes(): JsonResponse {
        $types = Type::all();

        return response()->json([
            'data' => TypeResource::collection($types)
        ]);
    }
}

