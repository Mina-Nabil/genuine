<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Users\User;

class TaskComment extends Model
{
    use HasFactory;
    const MORPH_TYPE = 'task_comment';

    protected $table = 'task_comments';
    protected $fillable = [
        "comment", "task_id", "user_id"
    ];

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
