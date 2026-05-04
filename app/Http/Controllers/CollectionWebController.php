<?php

namespace App\Http\Controllers;

use App\Http\Requests\CollectionCreateRequest;
use App\Http\Requests\CollectionUpdateRequest;
use App\Models\Collection;
use App\Models\Product;
use App\Models\Type;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CollectionWebController extends Controller
{
    public function getCollections() {
        $collections = Collection::all();

        return view('components.collections.list_collection ', compact('collections'));
    }

    public function getProductsPerCollection(int $collectionId, Request $request) {
        $typeId = $request->query('type');

        $query = Product::query()->with([
            'collections', 
            'types', 
            'productUsageImages',
            'productVariants.colors'
        ])->where('collection_id', $collectionId);

        if($typeId) {
            $query->where('type_id', $typeId);
        }

        $products = $query->paginate(12);

        $types = Type::all();
        $collectionName = Collection::where('id', $collectionId)->first()->name;

        return view('components.collections.collection_products', compact('products', 'types', 'collectionId', 'collectionName'));
    }

    public function createCollection(CollectionCreateRequest $request)
    {
        try {
            $data = $request->validated();

            // Upload image to Cloudinary
            if (!$request->hasFile('image')) {
                return redirect()->back()->with('error', 'Image file is required.')->withInput();
            }

            $uploadResponse = Cloudinary::upload($request->file('image')->getRealPath());
            $uploadedFileUrl = $uploadResponse->getSecurePath();

            if (!$uploadedFileUrl) {
                Log::error('Cloudinary upload returned null URL');
                return redirect()->back()->with('error', 'Failed to upload image. Please check Cloudinary credentials.')->withInput();
            }

            // Create the collection
            Collection::create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'image_url' => $uploadedFileUrl,
            ]);

            Log::info('Collection created successfully with image: ' . $uploadedFileUrl);

            return redirect()->route('collections.index')->with('success', 'Collection created successfully!');
        } catch (\Exception $e) {
            Log::error('Collection creation failed: ' . $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function editCollection(int $id) {
        $collection = Collection::findOrFail($id);
        return view('collections.edit', compact('collection'));
    }

    public function updateCollection(int $id, CollectionUpdateRequest $request) {
        try {
            $data = $request->validated();

            $collection = Collection::findOrFail($id);
            
            // Update basic collection information
            $collection->update([
                'name' => $data['name'],
                'slug' => Str::slug($data['name'])
            ]);

            // Handle image upload if provided
            if ($request->hasFile('image')) {
                $uploadResponse = Cloudinary::upload($request->file('image')->getRealPath());
                $uploadedFileUrl = $uploadResponse->getSecurePath();

                if (!$uploadedFileUrl) {
                    Log::error('Cloudinary upload returned null URL');
                    return redirect()->back()->with('error', 'Failed to upload image. Please check Cloudinary credentials.')->withInput();
                }

                $collection->update(['image_url' => $uploadedFileUrl]);
            }

            Log::info('Collection updated successfully. ID: ' . $id);

            return redirect()->route('collections.index')->with('success', 'Collection updated successfully!');
        } catch (\Exception $e) {
            Log::error('Collection update failed: ' . $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function deleteCollection(int $id) {
        try {
            $collection = Collection::findOrFail($id);
            
            // Check if collection has products
            $productCount = Product::where('collection_id', $id)->count();
            if ($productCount > 0) {
                return redirect()->route('collections.index')->with('error', "Tidak bisa menghapus koleksi yang masih memiliki {$productCount} produk. Silakan hapus semua produk terlebih dahulu.");
            }
            
            $collection->delete();

            return redirect()->route('collections.index')->with('success', 'Collection deleted!');
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error('Collection deletion failed: ' . $e->getMessage());
            
            if ($e->getCode() === '23000') {
                return redirect()->route('collections.index')->with('error', 'Tidak bisa menghapus koleksi ini karena masih ada produk yang terkait. Silakan hapus produk terkait terlebih dahulu.');
            }
            
            return redirect()->route('collections.index')->with('error', 'Error menghapus koleksi: ' . $e->getMessage());
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Collection deletion failed: ' . $e->getMessage());
            return redirect()->route('collections.index')->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
