<?php

namespace App\Models\Customers;


use App\Models\Users\AppLog;
use App\Models\Users\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;

class Followup extends Model
{
    use HasFactory;
    const MORPH_TYPE = 'followup';

    const STATUS_NEW = 'new';
    const STATUS_CALLED = 'called';
    const STATUS_CANCELLED = 'canceled';
    const STATUSES = [
        self::STATUS_NEW,
        self::STATUS_CALLED,
        self::STATUS_CANCELLED,
    ];

    protected $fillable = [
        'title',
        'status',
        'call_time',
        'action_time',
        'desc',
        'caller_note',
        'creator_id',
    ];

    ///model functions
    public function editInfo($title, $call_time = null, $desc = null)
    {
        try {
            $res = $this->update([
                "title"     =>  $title,
                "call_time" =>  Carbon::parse($call_time),
                "desc"      =>  $desc
            ]);
            AppLog::info("Follow-up updated", loggable: $this);
            return $res;
        } catch (Exception $e) {
            report($e);
            AppLog::error("Can't edit followup", desc: $e->getMessage());
            return false;
        }
    }

    public function setAsCalled($note = null)
    {
        if ($this->status !== self::STATUS_NEW) return false;
        try {
            $res = $this->update([
                "action_time"   =>  Carbon::now()->format('Y-m-d H:i:s'),
                "status"        =>  self::STATUS_CALLED,
                "caller_note"   =>  $note
            ]);
            AppLog::info("Follow-up done", loggable: $this);
            return $res;
        } catch (Exception $e) {
            AppLog::error("Can't set followup done", $e->getMessage(), $this);
            report($e);
            return false;
        }
    }

    public function setAsCancelled($note = null)
    {
        if ($this->status !== self::STATUS_NEW) return false;
        try {
            $res = $this->update([
                "action_time"   =>  Carbon::now()->format('Y-m-d H:i:s'),
                "status"        =>  self::STATUS_CANCELLED,
                "caller_note"   =>  $note
            ]);
            AppLog::info("Follow-up cancelled", loggable: $this);
            return $res;
        } catch (Exception $e) {
            AppLog::error("Can't cancel followup", $e->getMessage(), $this);
            report($e);
            return false;
        }
    }

    public function scopeUserData($query, $searchText = null, $upcoming_only = false, $mineOnly = false)
    {
        /** @var User */
        $loggedInUser = Auth::user();
        $query->select('followups.*')
            ->join('users', "followups.creator_id", '=', 'users.id');

        if ($loggedInUser->type !== User::TYPE_ADMIN || $mineOnly) {
            $query->where(function ($q) use ($loggedInUser) {
                $q->where('users.id', $loggedInUser->id);
            });
        }

        $query->when($searchText, function ($q, $v) {


            $splittedText = explode(' ', $v);

            foreach ($splittedText as $tmp) {
                $q->where(function ($qq) use ($tmp) {
                    $qq->where('followups.title', 'LIKE', "%$tmp%")
                        ->orwhere('customers.name', 'LIKE', "%$tmp%");
                });
            }
        })->when($upcoming_only, function ($q) {
            $now = new Carbon();
            $q->whereBetween('call_time', [
                $now->format('Y-m-01'),
                $now->addMonth()->format('Y-m-t')
            ]);
        });

        return $query->latest();
    }



    ///relations
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function called(): MorphTo
    {
        return $this->morphTo();
    }
}
