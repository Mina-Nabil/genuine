<?php
namespace App\Traits;

use App\Models\Users\AppLog;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Loggable
{

    public function logs(): MorphMany
    {
        return $this->morphMany(AppLog::class, 'loggable');
    }
}
