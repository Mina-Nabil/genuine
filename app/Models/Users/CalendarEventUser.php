<?php

namespace App\Models\Users;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarEventUser extends Model
{
    use HasFactory;
    protected $table = 'events_users';
    public $timestamps = false;
    protected $fillable = [
        'tag',
        'guest_name',
        'user_id'
    ];

    const TAG_GUEST = 'guest';
    const TAG_ATTENDEE = 'attendee';
    const TAG_OWNER = 'owner';

    const TAGS = [
        self::TAG_GUEST,
        self::TAG_ATTENDEE,
        self::TAG_OWNER,
    ];

    ///attributes
    public function getTitleAttribute()
    {
        if ($this->user_id) {
            $this->load('user');
            return $this->user->username;
        } else return $this->guest_name;
    }

    ///model functions
    public function editInfo($tag, $guest_name, $user_id = null)
    {
        assert($tag !== self::TAG_GUEST || $guest_name != null, 'Please set guest name');
        try {
            $res = $this->update([
                "tag"           =>  $tag,
                "guest_name"    =>  $guest_name,
                "user_id"       =>  $user_id
            ]);

            return $res;
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }
    public function deleteEventUser()
    {
        return $this->delete();
    }

    ///scopes
    public function scopeWithoutOwner($query)
    {
        return $query->whereNot('tag', CalendarEventUser::TAG_OWNER);
    }

    //relations
    public function event(): BelongsTo
    {
        return $this->belongsTo(CalendarEvent::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
