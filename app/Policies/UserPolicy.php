<?php

namespace App\Policies;

use App\Models\Users\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->is_admin || $user->id == $model->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if($user->is_admin) return true;
        return Response::deny("Unauthorized action");
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        if($user->is_admin || $user->id == $model->id) return true;
        return Response::deny("Unauthorized action");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        if($user->is_admin || $user->id == $model->id) return true;
        return Response::deny("Unauthorized action");
    }

    public function activate(User $user, User $model): bool
    {
        if($user->is_admin) return true;
        return Response::deny("Unauthorized action");
    }

    // You can add more methods for actions like restore, forceDelete, etc.
}
