<?php

use App\Models\Payments\CustomerPayment;
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
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_delivered')->default(false)->after('is_confirmed');
            $table->enum('driver_payment_type', CustomerPayment::PAYMENT_METHODS)->nullable()->after('is_delivered');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['is_delivered', 'driver_payment_type']);
        });
    }
};
