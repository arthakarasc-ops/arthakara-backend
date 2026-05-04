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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable(false);
            $table->unsignedBigInteger('color_id')->nullable();
            $table->unsignedBigInteger('fabric_id')->nullable();
            $table->unsignedBigInteger('size_id')->nullable();
            $table->string('image_url', 200)->nullable(false);
            $table->integer('stock')->nullable(false);
            $table->timestamps();

            $table->foreign('product_id')->on('products')->references('id');
            $table->foreign('color_id')->on('colors')->references('id');
            $table->foreign('fabric_id')->on('fabrics')->references('id');
            $table->foreign('size_id')->on('sizes')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
