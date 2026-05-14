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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('courier_code', 20)->nullable()->after('shipping_method_id');
            $table->string('courier_service', 50)->nullable()->after('courier_code');
            $table->decimal('shipping_cost', 12, 2)->default(0)->after('courier_service');
            $table->string('destination_city_id', 10)->nullable()->after('shipping_cost');
            $table->string('origin_city_id', 10)->nullable()->after('destination_city_id');
            $table->integer('total_weight')->default(0)->after('origin_city_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'courier_code',
                'courier_service',
                'shipping_cost',
                'destination_city_id',
                'origin_city_id',
                'total_weight'
            ]);
        });
    }
};
