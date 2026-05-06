<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShippingMethodSeeder extends Seeder
{
    /**
     * Seed the application's database with shipping methods.
     */
    public function run(): void
    {
        $methods = [
            ['id' => 1, 'name' => 'Regular',  'price' => 15000, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Express',  'price' => 25000, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Same Day', 'price' => 40000, 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($methods as $method) {
            DB::table('shipping_methods')->updateOrInsert(
                ['id' => $method['id']],
                $method
            );
        }
    }
}
