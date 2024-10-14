<?php

namespace App\Models\Users;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Notification extends Model
{
    use HasFactory;
    protected $fillable = [
        'sender_id', 'title', 'route', 'message', 'seen_at'
    ];

    //model functions
    public function setAsSeen()
    {
        $this->seen_at = Carbon::now();
        try {
            return $this->save();
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    public function setAsNotSeen()
    {
        $this->seen_at = null;
        try {
            return $this->save();
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    //mutator
    protected function route(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => url($value),
        );
    }

    //notf->is_seen (bool)
    public function getIsSeenAttribute()
    {
        return $this->seen_at !== null;
    }

    //relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
