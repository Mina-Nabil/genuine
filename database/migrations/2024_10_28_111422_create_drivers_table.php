<?php

use App\Models\Users\Driver;
use App\Models\Users\User;
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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('shift_title');
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade')->unique();
            $table->integer('weight_limit')->nullable(); 
            $table->integer('order_quantity_limit')->nullable(); 
            $table->enum('car_type', Driver::CAR_TYPES)->nullable();
            $table->string('car_model')->nullable();
            $table->boolean('is_available')->default(true); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
