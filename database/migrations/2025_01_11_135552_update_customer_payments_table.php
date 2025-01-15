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
        Schema::table('customer_payments', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->after('order_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->after('supplier_id')->constrained('supplier_invoices')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_payments', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
            $table->dropColumn('invoice_id');
            $table->foreignId('customer_id')->nullable(false)->change();
        });
    }
};
