<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100);
            $table->string('slug', 100)->unique();

            // 🔥 FIXED
            $table->decimal('price', 12, 2);

            $table->text('description');

            $table->unsignedBigInteger('collection_id');
            $table->unsignedBigInteger('type_id');

            $table->timestamps();

            $table->foreign('collection_id')->references('id')->on('collections');
            $table->foreign('type_id')->references('id')->on('types');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};