<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Tambahkan kolom baru
            $table->foreignId('product_variant_id')->after('user_id')->constrained('product_variants')->cascadeOnDelete();
            
            // Hapus kolom lama yang sudah tidak relevan
            $table->dropForeign(['product_id']);
            $table->dropForeign(['color_id']);
            $table->dropColumn(['product_id', 'color_id']);
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
            $table->dropColumn('product_variant_id');
            
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('color_id')->constrained()->cascadeOnDelete();
        });
    }
};
