<?php

use App\Models\Pets\Pet;
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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->enum('category', Pet::CATEGORIES);
            $table->string('type');
            $table->date('bdate');
            $table->string('name')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
