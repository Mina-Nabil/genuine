<?php

namespace App\Models\Products;

use App\Models\Materials\RawMaterial;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = ['inventory_id', 'quantity', 'before', 'after', 'remarks', 'user_id', 'created_at'];

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

    public function scopeProductionReport($query, Carbon $startDate = null, $endDate = null)
    {
        return $query
            ->select('transactions.inventory_id', 'products.name as prod_name', 'raw_materials.name as raw_name')
            ->selectRaw('SUM(transactions.quantity) as trans_count')
            ->when($startDate, function ($q, $v) {
                $q->where('transactions.created_at', '>=', $v->format('Y-m-d 00:00:00'));
            })->when($endDate, function ($q, $v) {
                $q->where('transactions.created_at', '<=', $v->format('Y-m-d 23:59:59'));
            })
            ->join('inventories', 'inventories.id', '=', 'transactions.inventory_id')
            ->leftjoin('products', function ($j) {
                $j->on('products.id', '=', 'inventories.inventoryable_id')
                    ->where('inventories.inventoryable_type', '=', Product::MORPH_TYPE)
                    ->where('quantity', '>', '0')
                    ->where('remarks', 'NOT LIKE', '%rder%');
            })->leftjoin('raw_materials', function ($j) {
                $j->on('raw_materials.id', '=', 'inventories.inventoryable_id')
                    ->where('inventories.inventoryable_type', '=', RawMaterial::MORPH_TYPE)
                    ->where('remarks', 'LIKE', "%Invoice%");
            })
            ->groupBy('transactions.inventory_id');
        return $query;
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
