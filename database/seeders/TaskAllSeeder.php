<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Tasks\Task;
use App\Models\Tasks\TaskComment;
use App\Models\Tasks\TaskFile;
use App\Models\Tasks\TaskWatcher;
use App\Models\Tasks\TaskTempAssignee;
use App\Models\Users\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class TaskAllSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (App::isProduction()) return;

        // Seed Users for Task-Related Data
        $users = User::take(5)->get();

        // Seed Tasks
        foreach ($users as $user) {
            $task = Task::create([
                'taskable_type' =>null, // Adjust as per taskable entities
                'taskable_id' => null, // Example, adjust as needed
                'title' => 'Task Title for ' . $user->name,
                'desc' => 'Description for the task',
                'open_by_id' => $user->id,
                'assigned_to_id' => $user->id,
                'last_action_by_id' => $user->id,
                'assigned_to_type' => null, // or another entity type
                'due' => now()->addDays(10),
                'status' => Task::STATUS_NEW, // Use a valid status from Task::STATUSES
            ]);

            // Seed Task Comments
            TaskComment::create([
                'user_id' => $user->id,
                'task_id' => $task->id,
                'comment' => 'This is a comment by ' . $user->name,
            ]);

            // Seed Task Files
            TaskFile::create([
                'task_id' => $task->id,
                'user_id' => $user->id,
                'name' => 'File_' . $task->id . '_' . $user->id,
                'file_url' => 'http://example.com/file' . $task->id . '.pdf',
            ]);

            // Seed Task Watchers
            TaskWatcher::create([
                'task_id' => $task->id,
                'user_id' => $user->id,
            ]);

            // Seed Task Temp Assignees
            TaskTempAssignee::create([
                'task_id' => $task->id,
                'user_id' => $user->id,
                'status' => TaskTempAssignee::STATUS_NEW,
                'end_date' => now()->addWeeks(1),
                'note' => 'Temporary assignment for task ' . $task->id,
            ]);
        }
    }
}
