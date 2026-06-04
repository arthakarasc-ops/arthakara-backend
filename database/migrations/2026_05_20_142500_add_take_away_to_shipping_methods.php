<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('shipping_methods')->updateOrInsert(
            ['id' => 4],
            [
                'name' => 'Take Away',
                'price' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('shipping_methods')->where('id', 4)->delete();
    }
};
