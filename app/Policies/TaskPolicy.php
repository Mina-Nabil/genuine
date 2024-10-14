<?php

namespace App\Policies;

use App\Models\Tasks\Task;
use App\Models\Users\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\Users\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Users\User  $user
     * @param  \App\Models\Tasks\Task  $task
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Task $task)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Users\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\Users\User  $user
     * @param  \App\Models\Tasks\Task  $task
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Task $task)
    {
        return true;
    }

    /**
     * Determine whether the user can update the task main info like the title.
     *
     * @param  \App\Models\Users\User  $user
     * @param  \App\Models\Tasks\Task  $task
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function updateMainInfo(User $user, Task $task)
    {
        return $user->is_admin || $user->id == $task->open_by_id;
    }

    /**
     * Determine whether the user can update the task due.
     *
     * @param  \App\Models\Users\User  $user
     * @param  \App\Models\Tasks\Task  $task
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function updateDue(User $user, Task $task)
    {
        return $user->is_admin || $user->id == $task->open_by_id || $user->id == $task->assigned_to_id;
    }

    /**
     * Determine whether the user can update the task assignee.
     *
     * @param  \App\Models\Users\User  $user
     * @param  \App\Models\Tasks\Task  $task
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function updateAssignTo(User $user, Task $task)
    {
        return $user->is_admin || ($user->id == $task->open_by_id) || ($user->id == $task->assigned_to_id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Users\User  $user
     * @param  \App\Models\Tasks\Task  $task
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Task $task)
    {
        return $user->is_admin;
    }
}
