<?php

namespace App\Policies;

use App\Models\Accounting\EntryTitle;
use App\Models\Accounting\JournalEntry;
use App\Models\Users\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class JournalEntryPolicy
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
        return $user->is_finance || $user->is_admin || $user->is_finance_assistant;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Users\User  $user
     * @param  \App\Models\Accounting\JournalEntry  $journalEntry
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, JournalEntry $journalEntry)
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
        return $user->is_admin || $user->is_finance;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Users\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function review(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Users\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function approve(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\Users\User  $user
     * @param  \App\Models\Accounting\JournalEntry  $journalEntry
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function manageTitle(User $user, JournalEntry $journalEntry = null)
    {
        return $user->is_admin || $user->is_finance;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\Users\User  $user
     * @param  \App\Models\Accounting\JournalEntry  $journalEntry
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, JournalEntry $journalEntry)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Users\User  $user
     * @param  \App\Models\Accounting\JournalEntry  $journalEntry
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, JournalEntry $journalEntry)
    {
        return true;
    }
}
