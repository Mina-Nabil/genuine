<?php

use App\Models\Users\CalendarEvent;
use App\Models\Users\CalendarEventUser;
use App\Models\Users\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->text('location')->nullable();
            $table->text('note')->nullable();
            $table->boolean('all_day')->default(false);
            $table->boolean('all_users')->default(false);
            $table->timestamps();
        });

        Schema::create('events_users', function (Blueprint $table) {
            $table->id();
            $table->enum('tag', CalendarEventUser::TAGS);
            $table->string('guest_name')->nullable();
            $table->foreignIdFor(User::class)->nullable()->constrained('users');
            $table->foreignIdFor(CalendarEvent::class)->constrained('calendar_events');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calendar_events');
    }
};
