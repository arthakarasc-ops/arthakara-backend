<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();

            // RELATION
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('color_id')->constrained()->cascadeOnDelete();

            // 🔥 CUSTOM CONFIG
            $table->json('scents'); // contoh: [1, 3]

            // DATA
            $table->integer('qty')->default(1);
            $table->integer('price'); // harga per item saat dimasukkan ke cart

            $table->timestamps();

            // 🔥 INDEX (BIAR CEPAT)
            $table->index(['user_id']);
            $table->index(['product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};