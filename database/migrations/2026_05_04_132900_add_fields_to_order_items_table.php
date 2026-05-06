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
        Schema::table('order_items', function (Blueprint $table) {
            // Drop product_id since product_variant_id is what we need and already points to a product
            if (Schema::hasColumn('order_items', 'product_id')) {
                $table->dropForeign(['product_id']);
                $table->dropColumn('product_id');
            }

            $table->integer('quantity')->default(1)->after('product_variant_id');
            $table->decimal('total_price', 12, 2)->default(0)->after('price_at_purchase');
            
            // Add scents json to store selected scents
            $table->json('scents')->nullable()->after('total_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'total_price', 'scents']);
            
            $table->foreignId('product_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
        });
    }
};
