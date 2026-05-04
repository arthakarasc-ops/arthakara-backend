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
        // Add phone_number to addresses table
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('phone_number', 20)->nullable()->after('country');
        });

        // Add phone_number to billing_addresses table
        Schema::table('billing_addresses', function (Blueprint $table) {
            $table->string('phone_number', 20)->nullable()->after('country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('phone_number');
        });

        Schema::table('billing_addresses', function (Blueprint $table) {
            $table->dropColumn('phone_number');
        });
    }
};
