<?php

namespace App\Http\Controllers;

use App\Http\Requests\CollectionCreateRequest;
use App\Http\Requests\CollectionUpdateRequest;
use App\Http\Resources\CollectionResource;
use App\Models\Collection;
use App\Services\CollectionService;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class CollectionController extends Controller
{
    protected CollectionService $collectionService;

    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    public function createCollection(CollectionCreateRequest $request): JsonResponse {
        try {
            $user = Auth::user();
     
            $decayMinutes = 1;
            $maxAttempts = 3;
            $key = 'create-collection: ' . $user->email;
    
            if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                $second = RateLimiter::availableIn($key);
    
                throw new HttpResponseException(response()->json([
                    'error' => 'Too many attempts. Please try again after ' . $second . ' second'
                ])->setStatusCode(429));
            }
    
            RateLimiter::hit($key, $decayMinutes * 60);

            $collection = $this->collectionService->createCollection($request->validated());

            return response()->json([
                'message' => 'Collection created successfully',
                'data' => new CollectionResource($collection),
                'isSuccess' => true
            ])->setStatusCode(201);
        } catch (Exception $ex) {
            throw new HttpResponseException(response()->json([
                'error' => 'Something went wrong.',
                'message' => $ex->getMessage(),
                'isSuccess' => false
            ])->setStatusCode(500));
        } 
    }


    public function getCollections(): JsonResponse {
        $collections = $this->collectionService->getCollections();

        return response()->json([
            'data' => CollectionResource::collection($collections)
        ])->setStatusCode(200);
    }

    public function updateCollection(int $collectionId, CollectionUpdateRequest $request): JsonResponse {
        try {
            $user = Auth::user();
    
            $decayMinutes = 1;
            $maxAttempts = 3;
            $key = 'update-collection: ' . $user->email;
    
            if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                $second = RateLimiter::availableIn($key);
    
                throw new HttpResponseException(response()->json([
                    'error' => 'Too many attempts. Please try again after ' . $second . ' second'
                ])->setStatusCode(429));
            }
    
            $collection = $this->collectionService->updateCollection($collectionId, $request->validated());

            if (!$collection) {
                throw new HttpResponseException(response()->json([
                    'error' => 'Collection not found.'
                ])->setStatusCode(404));
            }

            RateLimiter::hit($key, $decayMinutes * 60);

            return response()->json([
                'message' => 'Collection updated successfully.',
                'data' => [
                    'id' => $collection->id,
                    'name' => $collection->name,
                    'slug' => $collection->slug,
                    'updated_at' => $collection->updated_at->format('d-M-y')
                ],
                'isSuccess' => true
            ])->setStatusCode(200);
        } catch (Exception $ex) {
            throw new HttpResponseException(response()->json([
                'error' => 'Something went wrong.',
                'message' => $ex->getMessage(),
                'isSuccess' => false
            ])->setStatusCode(500));
        } 
    }

    public function deleteCollection(int $collectionId): JsonResponse {
        try {
            if (!$this->collectionService->deleteCollection($collectionId)) {
                throw new HttpResponseException(response()->json([
                    'error' => 'Collection not found.'
                ])->setStatusCode(404));
            }

            return response()->json([
                'message' => 'Collection deleted successfully.',
                'isSuccess' => true
            ])->setStatusCode(200);
        } catch (HttpResponseException $ex) {
            throw $ex;
        } catch (\RuntimeException $ex) {
            throw new HttpResponseException(response()->json([
                'error' => $ex->getMessage(),
                'isSuccess' => false
            ])->setStatusCode(400));
        } catch (\Illuminate\Database\QueryException $ex) {
            \Illuminate\Support\Facades\Log::error('Collection deletion failed: ' . $ex->getMessage());
            
            throw new HttpResponseException(response()->json([
                'error' => 'Cannot delete collection that has related products.',
                'message' => $ex->getMessage(),
                'isSuccess' => false
            ])->setStatusCode(400));
        } catch (Exception $ex) {
            throw new HttpResponseException(response()->json([
                'error' => 'Something went wrong.',
                'message' => $ex->getMessage(),
                'isSuccess' => false
            ])->setStatusCode(500));
        }
    }

}

