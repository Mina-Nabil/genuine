<?php

use App\Models\Products\Transaction;
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained()->onDelete('cascade'); // relationship to inventory
            $table->integer('quantity'); // quantity added or removed
            $table->integer('before'); // inventory quantity before the transaction
            $table->integer('after');  // inventory quantity after the transaction
            $table->text('remarks')->nullable(); // optional comments
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // user who made the change
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
