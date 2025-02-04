<?php

namespace App\Policies;

use App\Models\Accounting\Account;
use App\Models\Users\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountPolicy
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
     * @param  \App\Models\Accounting\AccountPolicy  $accountPolicy
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Account $accountPolicy)
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
     * @param  \App\Models\Accounting\AccountPolicy  $accountPolicy
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Account $accountPolicy)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Users\User  $user
     * @param  \App\Models\Accounting\AccountPolicy  $accountPolicy
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Account $accountPolicy)
    {
        return true;
    }
}
