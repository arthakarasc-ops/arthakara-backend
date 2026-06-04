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

            // Generate unique slug
            $slug = Str::slug($data['name']);
            $originalSlug = $slug;
            $count = 1;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }

            // Create the product
            $product = Product::create([
                'name' => $data['name'],
                'collection_id' => $data['collection_id'],
                'slug' => $slug,
                'price' => $data['price'],
                'stock' => $data['stock'] ?? 0,
                'description' => $data['description'],
            ]);

            $product->productUsageImages()->create([
                'product_id' => $product->id,
                'image_url' => $uploadedFileUrl,
            ]);

            // 🔥 Attach types
            if ($request->has('type_ids') && !empty($request->type_ids)) {
                $product->types()->sync($request->type_ids);
            }

            if ($request->has('color_ids') && !empty($request->color_ids)) {
                foreach ($request->color_ids as $colorId) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'color_id' => $colorId,
                        'image_url' => $uploadedFileUrl,
                        'stock' => $product->stock,
                    ]);
                }
            }

            // 🔥 Attach scents
            if ($request->has('scent_ids') && !empty($request->scent_ids)) {
                $product->scents()->sync($request->scent_ids);
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
            $query->whereHas('types', function ($q) use ($typeId) {
                $q->where('types.id', $typeId);
            });
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

            // Dapatkan semua variant ID
            $variantIds = $product->variants()->pluck('id')->toArray();

            // 1. Hapus isi keranjang (cart) yang memuat varian produk ini
            if (!empty($variantIds)) {
                \App\Models\CartItem::whereIn('product_variant_id', $variantIds)->delete();
                
                // 2. Hapus semua varian (Soft Delete otomatis jalan berkat trait)
                $product->variants()->delete();
            }

            // 3. Hapus produk utama (Soft Delete otomatis jalan berkat trait)
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
                'type_ids' => 'required|array|min:1',
                'type_ids.*' => 'exists:types,id',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'description' => 'required|string',
                'color_ids' => 'required|array|min:1',
                'color_ids.*' => 'exists:colors,id',
                'scent_ids' => 'nullable|array',
                'scent_ids.*' => 'exists:scents,id',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5000',
                'variant_images' => 'nullable|array',
                'variant_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5000',
                'variant_stocks' => 'nullable|array',
                'variant_stocks.*' => 'nullable|integer|min:0'
            ]);

            // Update basic product information
            $product->update([
                'name' => $validated['name'],
                'collection_id' => $validated['collection_id'],
                // Slug TIDAK diupdate di sini agar link di FE tidak mati (404)
                'price' => $validated['price'],
                'stock' => $validated['stock'],
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

            // 🔥 Sync colors via ProductVariant
            if ($request->has('color_ids') && !empty($request->color_ids)) {
                // Hapus variant lama yang color-nya tidak dipilih (dan bersihkan keranjang)
                $variantsToDelete = $product->variants()->whereNotIn('color_id', $request->color_ids)->get();
                foreach ($variantsToDelete as $v) {
                    \App\Models\CartItem::where('product_variant_id', $v->id)->delete();
                    \App\Models\OrderItem::where('product_variant_id', $v->id)->update(['product_variant_id' => null]);
                    $v->delete();
                }
                
                // Ambil gambar utama produk sebagai fallback
                $mainImageUrl = $product->productUsageImages->first()->image_url ?? 'https://via.placeholder.com/150';

                foreach ($request->color_ids as $colorId) {
                    $variantData = [];

                    // Ambil stok spesifik varian jika ada, jika tidak gunakan stok umum produk
                    if ($request->has("variant_stocks.{$colorId}") && $request->variant_stocks[$colorId] !== null) {
                        $variantData['stock'] = $request->variant_stocks[$colorId];
                    } else {
                        $variantData['stock'] = $product->stock;
                    }

                    // Cek apakah ada upload gambar khusus untuk varian ini
                    if ($request->hasFile("variant_images.{$colorId}")) {
                        $variantUploadResponse = Cloudinary::upload($request->file("variant_images.{$colorId}")->getRealPath());
                        $variantData['image_url'] = $variantUploadResponse->getSecurePath();
                    } else {
                        // Jika tidak ada upload baru, pastikan ada image_url (fallback ke main image jika baru dibuat)
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

            // 🔥 Sync types
            if ($request->has('type_ids') && !empty($request->type_ids)) {
                $product->types()->sync($request->type_ids);
            } else {
                $product->types()->detach();
            }

            // 🔥 Sync scents
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
            'product' => $product,
            'variants' => $variants
        ]);
    }
 
}

