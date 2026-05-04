<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert admin user with specific credentials
        DB::table('users')->insertOrIgnore([
            'email' => 'arthakarasc@gmail.com',
            'password' => Hash::make('arthakara123'),
            'full_name' => 'Arthakara Admin',
            'nickname' => 'Admin',
            'phone_number' => '08123456789',
            'is_admin' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete admin user when rolling back
        DB::table('users')->where('email', 'arthakarasc@gmail.com')->delete();
    }
};
