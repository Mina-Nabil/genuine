<?php

namespace App\Policies;

use App\Models\Accounting\EntryTitle;
use App\Models\Users\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class EntryTitlePolicy
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
     * @param  \App\Models\Accounting\EntryTitle  $entryTitle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, EntryTitle $entryTitle)
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
     * @param  \App\Models\Accounting\EntryTitle  $entryTitle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, EntryTitle $entryTitle)
    {
        return true;
    }

    /**
     * Determine whether the user can create title entry
     *
     * @param  \App\Models\Users\User  $user
     * @param  \App\Models\Accounting\EntryTitle  $entryTitle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function createEntry(User $user, EntryTitle $entryTitle)
    {
        return $user->is_admin || $user->is_finance || $entryTitle->allowedTo($user);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Users\User  $user
     * @param  \App\Models\Accounting\EntryTitle  $entryTitle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, EntryTitle $entryTitle)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Users\User  $user
     * @param  \App\Models\Accounting\EntryTitle  $entryTitle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, EntryTitle $entryTitle)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\Users\User  $user
     * @param  \App\Models\Accounting\EntryTitle  $entryTitle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, EntryTitle $entryTitle)
    {
        //
    }
}
