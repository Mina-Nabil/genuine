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
        Schema::table('zones', function (Blueprint $table) {
            $table->decimal('driver_order_rate', 8, 2)->default(0)->after('delivery_rate');
            $table->decimal('driver_return_rate', 8, 2)->default(0)->after('driver_order_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zones', function (Blueprint $table) {
            $table->dropColumn(['driver_order_rate', 'driver_return_rate']);
        });
    }
};
