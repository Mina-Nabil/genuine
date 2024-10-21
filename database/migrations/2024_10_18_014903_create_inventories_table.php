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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->morphs('inventoryable'); // This creates `inventoryable_id` and `inventoryable_type` columns
            $table->integer('on_hand')->default(0); // Total stock physically in store
            $table->integer('committed')->default(0); // Inventory reserved for unfulfilled orders
            $table->integer('available')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
