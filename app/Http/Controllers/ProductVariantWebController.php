<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductVariantWebCreateRequest;
use App\Http\Requests\ProductVariantWebUpdateRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log;

class ProductVariantWebController extends Controller
{
    public function showCreateForm($productId) {
        $product = Product::findOrFail($productId);
        return view('components.products.create_product_variant', compact('productId'));
    }

    public function createProductVariant($productId, ProductVariantWebCreateRequest $request)
    {
        try {
            $data = $request->validated();
            $product = Product::findOrFail($productId);

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

            // Create the product variant
            ProductVariant::create([
                'product_id' => $productId,
                'color_id' => $data['color_id'],
                'fabric_id' => $data['fabric_id'],
                'size_id' => $data['size_id'],
                'image_url' => $uploadedFileUrl,
                'stock' => $data['stock'],
            ]);

            Log::info('Product variant created successfully with image: ' . $uploadedFileUrl);

            return redirect()->route('products.index')->with('success', 'Product variant created successfully!');
        } catch (\Exception $e) {
            Log::error('Product variant creation failed: ' . $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function updateProductVariant($productId, $variantId, ProductVariantWebUpdateRequest $request) {
        try {
            // Verify product and variant exist
            $product = Product::findOrFail($productId);
            $productVariant = ProductVariant::findOrFail($variantId);

            $data = $request->validated();

            // Handle image upload if provided
            $imageUrl = $productVariant->image_url;
            if ($request->hasFile('image')) {
                $uploadResponse = Cloudinary::upload($request->file('image')->getRealPath());
                $uploadedUrl = $uploadResponse->getSecurePath();
                
                if (!$uploadedUrl) {
                    Log::error('Cloudinary upload returned null URL during variant update');
                    return redirect()->back()->with('error', 'Failed to upload image. Please check Cloudinary credentials.')->withInput();
                }
                
                $imageUrl = $uploadedUrl;
            }

            // Update the product variant
            $productVariant->update([
                'image_url' => $imageUrl,
                'stock' => $data['stock'],
            ]);

            Log::info('Product variant updated successfully. Image URL: ' . $imageUrl);

            return redirect()->route('products.index')->with('success', 'Product variant updated successfully!');
        } catch (\Exception $e) {
            Log::error('Product variant update failed: ' . $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function deleteProductVariant($productId, $variantId) {
        try {
            // Verify product and variant exist
            $product = Product::findOrFail($productId);
            $productVariant = ProductVariant::findOrFail($variantId);

            // Delete the variant
            $productVariant->delete();

            return redirect()->route('products.index')->with('success', 'Product variant deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Product variant deletion failed: ' . $e->getMessage());
            return redirect()->route('products.index')->with('error', 'Failed to delete product variant. Please try again.');
        }
    }

}

