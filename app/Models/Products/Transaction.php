<?php

namespace App\Models\Products;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = ['inventory_id', 'quantity', 'before', 'after', 'remarks', 'user_id','created_at'];

    public function scopeFilterByProduct($query, $searchTerm = null, $productId = null)
    {
        return $query->whereHas('inventory.inventoryable', function ($query) use ($searchTerm, $productId) {
            // Ensure the inventoryable_type matches "Product" (or the appropriate morph type)
            $query->where(function ($query) use ($searchTerm, $productId) {
                if ($searchTerm) {
                    $query->where('name', 'like', '%' . $searchTerm . '%');
                }
                if ($productId) {
                    $query->where('id', $productId);
                }
            });
        });
    }

    /**
     * Relationship to Inventory
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    /**
     * Relationship to User (who made the transaction)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
