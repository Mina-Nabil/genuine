<?php

namespace App\Policies;

use App\Models\Users\User;
use App\Models\orders\PeriodicOrder;
use Illuminate\Auth\Access\Response;

class PeriodicOrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PeriodicOrder $periodicOrder): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PeriodicOrder $periodicOrder): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PeriodicOrder $periodicOrder): bool
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PeriodicOrder $periodicOrder): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PeriodicOrder $periodicOrder): bool
    {
        return true;
    }
}
