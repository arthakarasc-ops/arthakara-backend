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
        // Skip for SQLite as it doesn't support dropping foreign keys
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            // Drop existing foreign key
            $table->dropForeign(['collection_id']);
            
            // Add new foreign key with cascade on delete
            $table->foreign('collection_id')->on('collections')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Skip for SQLite
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            // Drop the cascade foreign key
            $table->dropForeign(['collection_id']);
            
            // Restore original foreign key
            $table->foreign('collection_id')->on('collections')->references('id');
        });
    }
};
