<?php

namespace App\Models\Products;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'inventory_id',
        'quantity',
        'before',
        'after',
        'remarks',
        'user_id',
    ];

    /**
     * Relationship to Inventory
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    /**
     * Relationship to User (who made the transaction)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
