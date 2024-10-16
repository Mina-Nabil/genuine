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
         // Main catalog (e.g., فراخ)
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Catalog name (e.g., فراخ)
            $table->timestamps();
        });

        // Items related to a catalog, which include name, price, and weight
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('name'); // Item name (e.g., رز بالخلطه)
            $table->decimal('price', 10, 2); // Price of the item
            $table->integer('weight')->unsigned(); // Weight in grams (or other units)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
