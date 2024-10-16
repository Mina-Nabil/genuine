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
        // Combos table (grouping of products into combos)
        Schema::create('combos', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Combo name (e.g., Family Meal)
            $table->timestamps();
        });

        // Combo Products table (connects products to combos with custom price and quantity)
        Schema::create('combo_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('combo_id')->constrained('combos')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->decimal('price', 10, 2); // Custom price for this product in the combo
            $table->integer('quantity')->unsigned(); // Quantity of this product in the combo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('combo_products');
        Schema::dropIfExists('combos');
    }
};
