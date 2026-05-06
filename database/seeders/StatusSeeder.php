<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Seed the application's database with order statuses.
     */
    public function run(): void
    {
        $statuses = [
            ['id' => 1, 'name' => 'Pending',    'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Processing', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Shipped',    'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Delivered',  'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'Cancelled',  'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'name' => 'Refunded',   'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($statuses as $status) {
            DB::table('statuses')->updateOrInsert(
                ['id' => $status['id']],
                $status
            );
        }
    }
}
