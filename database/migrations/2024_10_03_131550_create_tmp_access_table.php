<?php

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
        Schema::create('tmp_access', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'from_id');
            $table->foreignIdFor(User::class, 'to_id');
            $table->date('expiry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tmp_access');
    }
};
