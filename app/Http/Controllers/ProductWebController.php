<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductWebCreateRequest;
use App\Models\CartItem;
use App\Models\Collection;
use App\Models\Color;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Type;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductWebController extends Controller
{
    /**
     * Upload a single file to Cloudinary and return the secure URL.
     * Returns null if the upload fails.
     */
    private function uploadToCloudinary(UploadedFile $file): ?string
    {
        $response = Cloudinary::upload($file->getRealPath());
        return $response->getSecurePath() ?: null;
    }

    public function createProduct(ProductWebCreateRequest $request)
    {
        try {
            $data = $request->validated();

            if (!$request->hasFile('image')) {
                return redirect()->back()->with('error', 'Image file is required.')->withInput();
            }

            $mainImageUrl = $this->uploadToCloudinary($request->file('image'));

            if (!$mainImageUrl) {
                Log::error('Cloudinary upload returned null URL');
                return redirect()->back()->with('error', 'Failed to upload image. Please check Cloudinary credentials.')->withInput();
            }

            // Generate unique slug
            $slug         = Str::slug($data['name']);
            $originalSlug = $slug;
            $count        = 1;
            while (Product::withTrashed()->where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }

            $product = Product::create([
                'name'          => $data['name'],
                'collection_id' => $data['collection_id'],
                'slug'          => $slug,
                'price'         => $data['price'],
                'stock'         => $data['stock'] ?? 0,
                'description'   => $data['description'],
            ]);

            $product->productUsageImages()->create(['image_url' => $mainImageUrl]);

            // Upload gambar tambahan (opsional)
            foreach (['image_2', 'image_3'] as $imageField) {
                if ($request->hasFile($imageField)) {
                    $url = $this->uploadToCloudinary($request->file($imageField));
                    if ($url) {
                        $product->productUsageImages()->create(['image_url' => $url]);
                    }
                }
            }

            if ($request->has('type_ids') && !empty($request->type_ids)) {
                $product->types()->sync($request->type_ids);
            }

            if ($request->has('color_ids') && !empty($request->color_ids)) {
                foreach ($request->color_ids as $colorId) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'color_id'   => $colorId,
                        'image_url'  => $mainImageUrl,
                        'stock'      => $product->stock,
                    ]);
                }
            }

            if ($request->has('scent_ids') && !empty($request->scent_ids)) {
                $product->scents()->sync($request->scent_ids);
            }

            Log::info('Product created successfully with image: ' . $mainImageUrl);

            return redirect()->route('products.index')->with('success', 'Product created successfully!');
        } catch (\Exception $e) {
            Log::error('Product creation failed: ' . $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function getProducts(Request $request)
    {
        $collectionId = $request->query('collection');
        $typeId       = $request->query('type');

        $query = Product::with(['collections', 'types', 'productUsageImages']);

        if ($collectionId) {
            $query->where('collection_id', $collectionId);
        }

        if ($typeId) {
            $query->whereHas('types', function ($q) use ($typeId) {
                $q->where('types.id', $typeId);
            });
        }

        $products    = $query->paginate(12);
        $collections = Collection::all();
        $types       = Type::all();

        return view('components.products.list_products', compact('products', 'collections', 'types'));
    }

    public function destroy($id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return redirect()->route('products.index')->with('error', 'Produk tidak ditemukan.');
            }

            $variantIds = $product->variants()->pluck('id')->toArray();

            if (!empty($variantIds)) {
                CartItem::whereIn('product_variant_id', $variantIds)->delete();
                $product->variants()->delete();
            }

            $product->delete();

            return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Product deletion failed: ' . $e->getMessage());
            return redirect()->route('products.index')->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
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
            $types       = Type::all();

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

            $validated = $request->validate([
                'name'             => 'required|string|max:100',
                'collection_id'    => 'required|integer|exists:collections,id',
                'type_ids'         => 'required|array|min:1',
                'type_ids.*'       => 'exists:types,id',
                'price'            => 'required|numeric|min:0',
                'stock'            => 'required|integer|min:0',
                'description'      => 'required|string',
                'color_ids'        => 'required|array|min:1',
                'color_ids.*'      => 'exists:colors,id',
                'scent_ids'        => 'nullable|array',
                'scent_ids.*'      => 'exists:scents,id',
                'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5000',
                'image_2'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5000',
                'image_3'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5000',
                'variant_images'   => 'nullable|array',
                'variant_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5000',
                'variant_stocks'   => 'nullable|array',
                'variant_stocks.*' => 'nullable|integer|min:0',
            ]);

            // Slug tidak diupdate agar link di FE tidak mati (404)
            $product->update([
                'name'          => $validated['name'],
                'collection_id' => $validated['collection_id'],
                'price'         => $validated['price'],
                'stock'         => $validated['stock'],
                'description'   => $validated['description'],
            ]);

            // Ambil semua gambar produk berurutan berdasarkan id
            $existingImages = $product->productUsageImages()->orderBy('id', 'asc')->get();

            // Handle update untuk tiap slot gambar (image, image_2, image_3)
            foreach (['image', 'image_2', 'image_3'] as $index => $imageField) {
                if (!$request->hasFile($imageField)) {
                    continue;
                }

                $url = $this->uploadToCloudinary($request->file($imageField));

                if (!$url) {
                    Log::error('Cloudinary upload returned null URL for field: ' . $imageField);
                    return redirect()->back()->with('error', 'Failed to upload image. Please check Cloudinary credentials.')->withInput();
                }

                if ($existingImages->count() > $index) {
                    $existingImages[$index]->update(['image_url' => $url]);
                } else {
                    $product->productUsageImages()->create(['image_url' => $url]);
                }

                // Refresh koleksi setelah update
                $existingImages = $product->productUsageImages()->orderBy('id', 'asc')->get();
            }

            // Sync warna via ProductVariant
            if ($request->has('color_ids') && !empty($request->color_ids)) {
                // Hapus varian lama yang warnanya tidak dipilih, bersihkan cart & order items
                $variantsToDelete = $product->variants()->whereNotIn('color_id', $request->color_ids)->get();
                foreach ($variantsToDelete as $variant) {
                    CartItem::where('product_variant_id', $variant->id)->delete();
                    OrderItem::where('product_variant_id', $variant->id)->update(['product_variant_id' => null]);
                    $variant->delete();
                }

                $mainImageUrl = $product->productUsageImages->first()->image_url ?? null;

                foreach ($request->color_ids as $colorId) {
                    $variantData = [];

                    if ($request->has("variant_stocks.{$colorId}") && $request->variant_stocks[$colorId] !== null) {
                        $variantData['stock'] = $request->variant_stocks[$colorId];
                    } else {
                        $variantData['stock'] = $product->stock;
                    }

                    if ($request->hasFile("variant_images.{$colorId}")) {
                        $variantUrl = $this->uploadToCloudinary($request->file("variant_images.{$colorId}"));
                        $variantData['image_url'] = $variantUrl;
                    } else {
                        // Gunakan gambar utama sebagai fallback untuk varian baru
                        $existingVariant = $product->variants()->where('color_id', $colorId)->first();
                        if (!$existingVariant) {
                            $variantData['image_url'] = $mainImageUrl;
                        }
                    }

                    $product->variants()->updateOrCreate(
                        ['color_id' => $colorId],
                        $variantData
                    );
                }
            } else {
                $product->variants()->delete();
            }

            // Sync types
            if ($request->has('type_ids') && !empty($request->type_ids)) {
                $product->types()->sync($request->type_ids);
            } else {
                $product->types()->detach();
            }

            // Sync scents
            if ($request->has('scent_ids') && !empty($request->scent_ids)) {
                $product->scents()->sync($request->scent_ids);
            } else {
                $product->scents()->detach();
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

        $variants = ProductVariant::with(['color'])->where('product_id', $productId)->get();

        return view('components.products.detail_product', [
            'product'  => $product,
            'variants' => $variants,
        ]);
    }
}
