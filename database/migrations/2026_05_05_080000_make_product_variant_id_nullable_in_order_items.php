<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Ubah product_variant_id menjadi nullable + SET NULL on delete
     * Agar produk bisa dihapus tanpa merusak data riwayat pesanan
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Drop FK lama
            $table->dropForeign(['product_variant_id']);

            // Ubah kolom jadi nullable
            $table->unsignedBigInteger('product_variant_id')->nullable()->change();

            // Buat FK baru dengan SET NULL on delete
            $table->foreign('product_variant_id')
                ->references('id')
                ->on('product_variants')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);

            $table->unsignedBigInteger('product_variant_id')->nullable(false)->change();

            $table->foreign('product_variant_id')
                ->references('id')
                ->on('product_variants')
                ->restrictOnDelete();
        });
    }
};
