<?php

namespace App\Models\Accounting;

use App\Models\Users\User;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EntryTitle extends Model
{
    use HasFactory;

    const MORPH_TYPE = 'entry_title';

    protected $table = 'entry_titles';
    protected $fillable = ['name', 'desc'];
    public $timestamps = false;

    ////static functions

    /**
     * @param $accounts should be an array of 
     * ['id' => [
     * 'nature' => 'debit' or 'credit' , 
     * 'limit => nullable or double
     * ] ] 
     */
    public static function newEntry($name, $desc = null, $accounts = [])
    {
        try {
            $entryTitle = new self([
                'name'          =>  $name,
                'desc'          =>  $desc,
            ]);
            $entryTitle->save();
            if (count($accounts)) {
                $entryTitle->accounts()->sync(
                    $accounts
                );
            }
            return $entryTitle;
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }


    /////////model functions
    public function isEntryValid($accounts)
    {
        if($this->id == 1) return true;
        $this->load('accounts');
        $entry_accounts_ids = $this->accounts->pluck('id')->toArray();
        foreach ($accounts as $account_id => $entry_arr) {
            $tmpAccount = Account::with('parent_account')->findOrFail($account_id);
            while (!in_array($tmpAccount->id, $entry_accounts_ids)) {
                $tmpAccount = $tmpAccount->parent_account;
            }
            $entryAccount = $this->accounts->firstWhere('id', $tmpAccount->id);

            if ($entryAccount->pivot->limit && $entryAccount->pivot->nature == $entry_arr['nature'] && $entryAccount->pivot->limit < $entry_arr['amount']) {
                return false;
            }
        }
        return true;
    }

    // public function setAllowedUsers($user_ids)
    // {
    //     /** @var User */
    //     $logged_user = auth()->user();
    //     if($logged_user->can('manageTitle', JournalEntry::class)){
    //         $this->allowed_users()->sync($user_ids);
    //         return;
    //     }

    // }

    // public function allowedTo(User $user)
    // {
    //     return $this->allowed_users()->get()->pluck('id')->contains($user->id);
    // }

    /** 
     * Must show a warning before the edit. That it's going to update title on old journal entries
     * @param $accounts should be an array of 
     * ['id' => [
     * 'nature' => 'debit' or 'credit' , 
     * 'limit => nullable or double
     * ] ] 
     */
    public function editTitle($name, $desc = null, array $accounts = [])
    {
        try {
            $this->name = $name;
            $this->desc = $desc;
            $this->save();
            if (count($accounts)) {
                $this->accounts()->sync(
                    $accounts
                );
            } else {
                $this->accounts()->sync([]);
            }
            return true;
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    public function deleteTitle()
    {
        if ($this->entries()->get()->count()) return false;

        DB::table('titles_accounts')->where('entry_title_id', $this->id)->delete();
        $this->delete();
        return true;
    }

    ///relations
    public function entries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    // public function allowed_users(): BelongsToMany
    // {
    //     return $this->belongsToMany(User::class, 'titles_users');
    // }

    public function accounts(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'titles_accounts')->withPivot('nature', 'limit');
    }

    public function debit_accounts(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'titles_accounts')->withPivot('nature', 'limit')->wherePivot('nature', 'debit');
    }

    public function credit_accounts(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'titles_accounts')->withPivot('nature', 'limit')->wherePivot('nature', 'credit');
    }
}
