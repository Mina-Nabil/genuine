<?php

use App\Models\Users\Driver;
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

        Schema::table('users', function (Blueprint $table) {
            $table->text('home_location_url_1')->nullable();
            $table->text('home_location_url_2')->nullable();
        });

        Schema::create('route_nav', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Driver::class)->constrained('drivers')->onDelete('cascade');
            $table->text('origin');
            $table->text('destination');
            $table->date('day');
            $table->json('response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_nav');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('home_location_url');
        });
    }
};