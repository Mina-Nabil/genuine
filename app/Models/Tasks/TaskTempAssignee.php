<?php

namespace App\Models\Tasks;

use App\Models\Users\AppLog;
use App\Models\Users\User;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskTempAssignee extends Model
{
    use HasFactory;

    protected $table = 'task_temp_assignee';
    protected $fillable = ['user_id', 'status', 'end_date', 'note'];

    const STATUS_NEW = 'new';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUSES = [
        self::STATUS_NEW,
        self::STATUS_ACCEPTED,
        self::STATUS_REJECTED,
    ];

    ///functions
    public function approveRequest()
    {
        $this->load('task', 'user');
        $this->status = self::STATUS_ACCEPTED;

        try {
            $this->save();
            $this->task->addComment("Task temporary assigned to {$this->user->username}", false);

            return true;
        } catch (Exception $e) {
            AppLog::error("Can't approve temp task assign request", $e->getMessage());
            report($e);
            return false;
        }
    }

    public function declineRequest()
    {
        $this->load('task');
        $this->status = self::STATUS_REJECTED;

        try {
            $this->save();
            $this->task->addComment("Temporary assignment rejected", false);
            return true;
        } catch (Exception $e) {
            AppLog::error("Can't decline temp task assign request", $e->getMessage());
            report($e);
            return false;
        }
    }

    public function deleteRequest()
    {
        try {
            return $this->delete();
        } catch (Exception $e) {
            AppLog::error("Can't delete temp task assign request", $e->getMessage());
            report($e);
            return false;
        }
    }


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
