<?php

use App\Models\Users\AppLog;
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
        // Re-add the enum type for the level column
        Schema::table('app_logs', function (Blueprint $table) {
            $table->enum('level', AppLog::LEVELS)->change(); // replace with your actual levels
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the level column back to its previous state
        Schema::table('app_logs', function (Blueprint $table) {
            // Here you may want to change it back to a string or whatever the previous state was
            $table->string('level')->change();
        });
    }
};
