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
        //admins only
        Schema::create('supplier_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable(); //رقم الفاتوره
            $table->string('title')->nullable();
            $table->string('note')->nullable();
            $table->foreignId('supplier_id')->constrained();
            $table->integer('total_items'); //lena e7na
            $table->decimal('total_amount', 15, 2);
            $table->date('payment_due')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->timestamps();
        });

        Schema::create('invoice_raw_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('raw_material_id')->constrained()->onDelete('cascade'); //
            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->timestamps();
        });

        Schema::create('supplier_raw_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->foreignId('raw_material_id')->constrained()->onDelete('cascade');
            $table->decimal('price', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_raw_materials');
        Schema::dropIfExists('supplier_invoices');
    }
};
