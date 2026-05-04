<?php

namespace Database\Seeders;

use App\Models\Variant;
use Illuminate\Database\Seeder;

class VariantSeeder extends Seeder
{
    public function run(): void
    {
        $variants = [
            // Rasa
            ['name' => 'Vanilla', 'type' => 'rasa'],
            ['name' => 'Chocolate', 'type' => 'rasa'],
            ['name' => 'Strawberry', 'type' => 'rasa'],
            ['name' => 'Mint', 'type' => 'rasa'],
            
            // Wangi
            ['name' => 'Lavender', 'type' => 'wangi'],
            ['name' => 'Rose', 'type' => 'wangi'],
            ['name' => 'Jasmine', 'type' => 'wangi'],
            ['name' => 'Citrus', 'type' => 'wangi'],
        ];

        foreach ($variants as $variant) {
            Variant::firstOrCreate($variant);
        }
    }
}
