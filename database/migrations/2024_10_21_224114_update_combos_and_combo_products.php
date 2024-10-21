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
        // Add price column to combos table
        Schema::table('combos', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->after('name'); // Add the price column to the combos table
        });

        // Remove price column from combo_products table
        Schema::table('combo_products', function (Blueprint $table) {
            $table->dropColumn('price'); // Remove the price column from the combo_products table
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove price column from combos table if rolling back
        Schema::table('combos', function (Blueprint $table) {
            $table->dropColumn('price'); // Remove the price column from the combos table
        });

        // Re-add price column to combo_products table if rolling back
        Schema::table('combo_products', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->after('product_id'); // Re-add the price column to the combo_products table
        });
    }
};
