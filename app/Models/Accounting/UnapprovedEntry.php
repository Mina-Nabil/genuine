<?php

namespace App\Models\Accounting;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Users\AppLog;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UnapprovedEntry extends Model
{
    use HasFactory;
    const MORPH_TYPE = 'unapproved_entry';

    protected $table = 'unapp_entries';
    protected $fillable = [
        'user_id',
        'entry_title_id',
        'credit_id',
        'debit_id',
        'credit_doc_url',
        'debit_doc_url',
        'amount',
        'currency',
        'currency_amount',
        'currency_rate',
        'entry_title_id',
        'comment',
        'receiver_name',
        'cash_entry_type',
    ];

    ////static functions
    public static function newEntry(
        $entry_title_id,
        $cash_entry_type = null,
        $receiver_name = null,
        $comment = null,
        $accounts = [],
    ) {
        try {
            $newEntry = new self([
                'user_id'           => Auth::id(),
                'entry_title_id'    => $entry_title_id,
                'comment'       => $comment,
                'receiver_name' => $receiver_name,
                'cash_entry_type'     => $cash_entry_type,
            ]);
            DB::transaction(function () use ($newEntry, $accounts) {
                $newEntry->save();
                foreach ($accounts as $account_id => $entry_arr) {
                    $newEntry->accounts()->attach($account_id, $entry_arr);
                }
            });

            $newEntry->save();
            return $newEntry;
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }


    ///model functions
    public function approveRecord()
    {
        /** @var User */
        $loggedInUser = Auth::user();
        $this->load('accounts');
        foreach ($this->accounts as $ac) {
            $accounts_arr[$ac->id] = [
                'nature'    =>  $ac->pivot->nature,
                'amount'    =>  $ac->pivot->amount,
                'currency' => $ac->pivot->currency,
                'currency_amount' => $ac->pivot->currency_amount,
                'currency_rate' => $ac->pivot->currency_rate,
                'doc_url' => $ac->pivot->doc_url,
            ];
        }

        /** @var EntryTitle */
        $entry = EntryTitle::findOrFail($this->entry_title_id);

        if (!$entry->isEntryValid($accounts_arr) && !$loggedInUser->can('approve', JournalEntry::class)) return false;
        $newEntry = null;
        try {

            DB::transaction(function () use ($accounts_arr, &$newEntry) {
                $newEntry = JournalEntry::newJournalEntry(
                    entry_title_id: $this->entry_title_id,
                    receiver_name: $this->receiver_name,
                    cash_entry_type: $this->cash_entry_type,
                    comment: $this->comment,
                    approver_id: Auth::id(),
                    approved_at: Carbon::now(),
                    user_id: Auth::id(),
                    accounts: $accounts_arr
                );

                if ($newEntry) {
                    $this->deleteRecord();
                }
            });
        } catch (Exception $e) {
            report($e);
        }
        return $newEntry;
    }



    public function deleteRecord()
    {
        $this->accounts()->sync([]);
        $this->delete();
        return true;
    }

    public function accounts(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'unapp_entry_accounts')->withPivot([
            'nature',
            'amount',
            'currency',
            'currency_amount',
            'currency_rate',
            'doc_url'
        ]);
    }

    public function entry_title(): BelongsTo
    {
        return $this->belongsTo(EntryTitle::class);
    }
}
