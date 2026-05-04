<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductWebCreateRequest;
use App\Models\Collection;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Type;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Str;

class ProductWebController extends Controller
{
    public function createProduct(ProductWebCreateRequest $request)
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

            // Create the product
            $product = Product::create([
                'name' => $data['name'],
                'collection_id' => $data['collection_id'],
                'type_id' => $data['type_id'],
                'slug' => Str::slug($data['name']),
                'price' => $data['price'],
                'description' => $data['description'],
            ]);

            // Create product usage image
            $product->productUsageImages()->create([
                'product_id' => $product->id,
                'image_url' => $uploadedFileUrl,
            ]);

            // 🔥 Attach colors
            if ($request->has('color_ids') && !empty($request->color_ids)) {
                $product->colors()->sync($request->color_ids);
            }

            // 🔥 Attach flavor variants (max 2)
            if ($request->has('flavor_variant_ids') && !empty($request->flavor_variant_ids)) {
                $flavorVariantIds = array_slice($request->flavor_variant_ids, 0, 2);
                $product->flavorVariants()->sync($flavorVariantIds);
            }

            Log::info('Product created successfully with image: ' . $uploadedFileUrl);

            return redirect()->route('products.index')->with('success', 'Product created successfully!');
        } catch (\Exception $e) {
            Log::error('Product creation failed: ' . $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function getProducts(Request $request) {
        $collectionId = $request->query('collection');
        $typeId = $request->query('type');

        $query = Product::with(['collections', 'types', 'productUsageImages']);

        if ($collectionId) {
            $query->where('collection_id', $collectionId);
        }

        if ($typeId) {
            $query->where('type_id', $typeId);
        }

        $products = $query->paginate(12);

        $collections = Collection::all();
        $types = Type::all();

        return view('components.products.list_products', compact('products', 'collections', 'types'));
    }

    public function destroy($id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return redirect()->route('products.index')->with('error', 'Produk tidak ditemukan.');
            }

            // Delete related product usage images
            $product->productUsageImages()->delete();

            // Delete the product
            $product->delete();

            return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Product deletion failed: ' . $e->getMessage());

            return redirect()->route('products.index')->with('error', 'Gagal menghapus produk. Silakan coba lagi.');
        }
    }

    public function editProduct(int $id)
    {
        try {
            $product = Product::with(['collections', 'types', 'productUsageImages'])->find($id);

            if (!$product) {
                return redirect()->route('products.index')->with('error', 'Produk tidak ditemukan.');
            }

            $collections = Collection::all();
            $types = Type::all();

            return view('components.products.edit_product', compact('product', 'collections', 'types'));
        } catch (\Exception $e) {
            Log::error('Edit product page failed: ' . $e->getMessage());
            return redirect()->route('products.index')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function updateProduct(int $id, Request $request)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return redirect()->route('products.index')->with('error', 'Produk tidak ditemukan.');
            }

            // Validate the request
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'collection_id' => 'required|integer|exists:collections,id',
                'type_id' => 'required|integer|exists:types,id',
                'price' => 'required|numeric|min:0',
                'description' => 'required|string',
                'color_ids' => 'nullable|array',
                'color_ids.*' => 'exists:colors,id',
                'flavor_variant_ids' => 'nullable|array|max:2',
                'flavor_variant_ids.*' => 'exists:variants,id',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5000'
            ]);

            // Update basic product information
            $product->update([
                'name' => $validated['name'],
                'collection_id' => $validated['collection_id'],
                'type_id' => $validated['type_id'],
                'slug' => Str::slug($validated['name']),
                'price' => $validated['price'],
                'description' => $validated['description']
            ]);

            // Handle image upload if provided
            if ($request->hasFile('image')) {
                $uploadResponse = Cloudinary::upload($request->file('image')->getRealPath());
                $uploadedFileUrl = $uploadResponse->getSecurePath();

                if (!$uploadedFileUrl) {
                    Log::error('Cloudinary upload returned null URL');
                    return redirect()->back()->with('error', 'Failed to upload image. Please check Cloudinary credentials.')->withInput();
                }

                // Update the main product usage image
                $productImage = $product->productUsageImages()->first();
                if ($productImage) {
                    $productImage->update(['image_url' => $uploadedFileUrl]);
                } else {
                    $product->productUsageImages()->create(['image_url' => $uploadedFileUrl]);
                }
            }

            // 🔥 Sync colors
            if ($request->has('color_ids') && !empty($request->color_ids)) {
                $product->colors()->sync($request->color_ids);
            } else {
                $product->colors()->detach();
            }

            // 🔥 Sync flavor variants (max 2)
            if ($request->has('flavor_variant_ids') && !empty($request->flavor_variant_ids)) {
                $flavorVariantIds = array_slice($request->flavor_variant_ids, 0, 2);
                $product->flavorVariants()->sync($flavorVariantIds);
            } else {
                $product->flavorVariants()->detach();
            }

            Log::info('Product updated successfully. ID: ' . $id);

            return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Product update failed: ' . $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function showProductDetail(int $productId)
    {
        $product = Product::with(['collections', 'types', 'productUsageImages'])->find($productId);

        if (!$product) {
            abort(404, 'Produk tidak ditemukan');
        }

        $variants = ProductVariant::with(['colors', 'fabrics', 'sizes'])->where('product_id', $productId)->get();

        return view('components.products.detail_product', [
            'product' => $product,
            'variants' => $variants
        ]);
    }
 
}

