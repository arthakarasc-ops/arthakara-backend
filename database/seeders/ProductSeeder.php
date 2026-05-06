<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Seed the application's database with product catalog data.
     */
    public function run(): void
    {
        // 1. Collection (name, slug, image_url)
        $collectionId = DB::table('collections')->insertGetId([
            'name' => 'Summer Collection',
            'slug' => 'summer-collection',
            'image_url' => 'https://via.placeholder.com/150',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Type (name)
        $typeId = DB::table('types')->insertGetId([
            'name' => 'Car Freshener',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Product (name, slug, price, description, collection_id, type_id)
        $productId = DB::table('products')->insertGetId([
            'name' => 'Little Trees Classic',
            'slug' => 'little-trees-classic',
            'price' => 25000,
            'description' => 'Pewangi mobil classic Little Trees',
            'stock' => 100,
            'collection_id' => $collectionId,
            'type_id' => $typeId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Color (name, hex)
        $colorId = DB::table('colors')->insertGetId([
            'name' => 'Green',
            'hex_code' => '#00FF00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Product Variant (product_id, color_id, image_url, stock)
        DB::table('product_variants')->insertGetId([
            'product_id' => $productId,
            'color_id' => $colorId,
            'image_url' => 'https://via.placeholder.com/150',
            'stock' => 100,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 6. Scents (name, extra_price, is_active)
        DB::table('scents')->insert([
            ['name' => 'Vanilla',  'extra_price' => 5000, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lavender', 'extra_price' => 2000, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 7. Pivot: product_scents
        $scentIds = DB::table('scents')->pluck('id');
        foreach ($scentIds as $scentId) {
            DB::table('product_scents')->insert([
                'product_id' => $productId,
                'scent_id' => $scentId,
            ]);
        }
    }
}
