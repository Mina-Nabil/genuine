<?php

use App\Models\Customers\Customer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('balance_transactions', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');

            $table->morphs('transactionable','transactionable_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('balance_transactions', function (Blueprint $table) {
            $table->dropMorphs('transactionable');
            $table->foreignIdFor(Customer::class)->constrained();
        });
    }
};
