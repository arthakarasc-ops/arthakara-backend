<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Collection;

class ProductService
{
    public function createProduct(array $data)
    {
        return Product::create($data);
    }

    public function getProducts(array $filters, int $size, int $page)
    {
        $query = Product::with(['collections', 'types']);

        if (isset($filters['min_price']) && $filters['min_price']) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price']) && $filters['max_price']) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (isset($filters['type_id']) && $filters['type_id']) {
            $query->where('type_id', $filters['type_id']);
        }

        if (isset($filters['color_id']) && $filters['color_id']) {
            $query->whereHas('productVariants', function ($q) use ($filters) {
                $q->where('color_id', $filters['color_id']);
            });
        }

        return $query->paginate($size, ['*'], 'page', $page);
    }

    public function getProductsPerCollection(int $collectionId, array $filters, int $size, int $page)
    {
        $query = Product::with(['collections', 'types'])
                        ->whereHas('collections', function ($q) use ($collectionId) {
                            $q->where('collections.id', $collectionId);
                        });

        if (isset($filters['min_price']) && $filters['min_price']) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price']) && $filters['max_price']) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (isset($filters['type_id']) && $filters['type_id']) {
            $query->where('type_id', $filters['type_id']);
        }

        if (isset($filters['color_id']) && $filters['color_id']) {
            $query->whereHas('productVariants', function ($q) use ($filters) {
                $q->where('color_id', $filters['color_id']);
            });
        }

        return $query->paginate($size, ['*'], 'page', $page);
    }

    public function updateProduct(int $productId, array $data)
    {
        $product = Product::find($productId);
        if ($product) {
            $product->update($data);
            return $product;
        }
        return null;
    }

    public function deleteProduct(int $productId)
    {
        $product = Product::find($productId);
        if ($product) {
            $product->delete();
            return true;
        }
        return false;
    }
}