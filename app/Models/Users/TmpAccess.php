<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TmpAccess extends Model
{
    use HasFactory;

    use HasFactory;

    protected $table = 'tmp_access';
    protected $fillable = ['from_id', 'to_id', 'expiry'];
    public $timestamps = false;

    ///relations
    public function user_from(): BelongsTo
    {
        return $this->belongsTo(User::class, "from_id");
    }

    public function user_to(): BelongsTo
    {
        return $this->belongsTo(User::class, "to_id");
    }
}
