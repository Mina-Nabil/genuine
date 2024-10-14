<?php

namespace App\Models\Tasks;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskWatcher extends Model
{
    use HasFactory;
    const MORPH_TYPE = 'task_watcher';

    protected $table = 'task_watchers';
    protected $fillable = ['user_id'];
    public $timestamps = false;

    //relations
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
