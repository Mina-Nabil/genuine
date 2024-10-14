<?php

use App\Models\Tasks\Task;
use App\Models\Tasks\TaskTempAssignee;
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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('taskable');
            $table->string('title');
            $table->text('desc')->nullable();
            $table->foreignIdFor(User::class, 'open_by_id')->nullable();
            $table->foreignIdFor(User::class, 'assigned_to_id')->nullable();
            $table->foreignIdFor(User::class, 'last_action_by_id')->nullable();
            $table->string('assigned_to_type')->nullable();
            $table->dateTime('due')->nullable();
            $table->enum("status", Task::STATUSES);
            $table->softDeletes();
            $table->timestamps();            
        });

        Schema::create('task_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->nullable();
            $table->foreignIdFor(Task::class);
            $table->string('comment');
            $table->timestamps();
        });

        Schema::create("task_files", function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Task::class);
            $table->foreignIdFor(User::class);
            $table->string('name');
            $table->string('file_url');
        });

        Schema::create("task_watchers", function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Task::class);
            $table->foreignIdFor(User::class);
        });

        Schema::create("task_temp_assignee", function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Task::class);
            $table->foreignIdFor(User::class);
            $table->enum('status', TaskTempAssignee::STATUSES)->default(TaskTempAssignee::STATUS_NEW);
            $table->date('end_date');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_temp_assignee');
        Schema::dropIfExists('task_watchers');
        Schema::dropIfExists('task_files');
        Schema::dropIfExists('task_comments');
        Schema::dropIfExists('tasks');
    }
};
