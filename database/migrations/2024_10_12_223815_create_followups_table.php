<?php

use App\Models\Customers\Followup;
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
        Schema::create('followups', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'creator_id')->constrained('users');
            $table->morphs('called');
            $table->string('title');
            $table->enum('status', Followup::STATUSES)->default(Followup::STATUS_NEW);
            $table->dateTime('call_time')->nullable();
            $table->dateTime('action_time')->nullable();
            $table->string('desc')->nullable();
            $table->string('caller_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('followups');
    }
};
