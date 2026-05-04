<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->post('/api/products', [
            'name' => 'produk1',
            'collection_id' => 1,
            'type_id' => 1,
            'slug' => 'produk-satu',
            'price' => 10000,
            'description' => 'produk satu bagus sekali',
            'image_url' => 'https://image.url/produk1.png'
        ])->assertStatus(201)
            ->assertJson([
                'message' => 'Product created successfully',
                'data' => [
                    'id' => 1,
                    'name' => 'produk1',
                    'collection' => 'koleksi satu',
                    'type' => 'tipe satu',
                    'slug' => 'produk-satu',
                    'price' => 10000,
                    'description' => 'produk satu bagus sekali',
                    'image_url' => 'https://image.url/produk1.png',
                    'created_at' => 11-07-2025,  
                ]
            ]);
    }
}
