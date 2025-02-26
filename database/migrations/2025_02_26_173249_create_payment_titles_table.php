<?php

use App\Models\Payments\Title;
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
        Schema::create('payment_titles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->double('limit');
            $table->string('description')->nullable();
        });

        Schema::table('customer_payments', function (Blueprint $table) {
            $table->foreignIdFor(Title::class)->nullable()->constrained('payment_titles')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('payment_titles');
    }
};
