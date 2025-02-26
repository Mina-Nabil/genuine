<?php

namespace App\Policies;

use App\Models\Payments\Title;
use App\Models\Users\User;
use Illuminate\Auth\Access\Response;

class TitlePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->id == 1 || $user->id == 2;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Title $title): bool
    {
        return $user->id == 1 || $user->id == 2;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->id == 1 || $user->id == 2;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Title $title): bool
    {
        return $user->id == 1 || $user->id == 2;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Title $title): bool
    {
        return $user->id == 1 || $user->id == 2;
    }
}
