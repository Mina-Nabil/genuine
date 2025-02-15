<?php

namespace App\Models\Products;

use App\Models\Materials\RawMaterial;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = ['inventory_id', 'quantity', 'before', 'after', 'remarks', 'user_id', 'created_at'];

    //model functions
    public function recalculateBalance()
    {
        $this->load('inventory', 'user');

        $latest_balance = self::join('inventories', 'inventories.id', '=', 'transactions.inventory_id')
            ->where('transactions.inventory_id', $this->inventory_id)
            ->where('transactions.id', '<', $this->id)
            ->orderByDesc('transactions.id')->limit(1)->first();

        $this->before = $latest_balance->after ?? 0;
        if (
            $this->inventory->inventoryable_type == RawMaterial::MORPH_TYPE &&
            in_array($this->user->type, [User::TYPE_ADMIN, User::TYPE_INVENTORY]) &&
            !str_contains($this->remarks, 'Invoice')
        ) {
            $this->quantity = -1 * $this->quantity;
            $this->save();
            $this->inventory->on_hand =  $this->before + $this->quantity;
            $this->inventory->available =  $this->before + $this->quantity;
            $this->inventory->save();
        }
        $this->after = $this->before + $this->quantity;
        $this->save();
    }

    public function resetBalance()
    {
        $this->before = 0;
        $this->after = $this->quantity;
        $this->save();
    }

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
            ->join('users', 'transactions.user_id', '=', 'users.id')
            ->leftjoin('products', function ($j) {
                $j->on('products.id', '=', 'inventories.inventoryable_id')
                    ->where('inventories.inventoryable_type', '=', Product::MORPH_TYPE)
                    ->where('transactions.quantity', '>', 0)
                    ->whereNull('transactions.remarks')
                    ->whereIn('users.type', [User::TYPE_INVENTORY, User::TYPE_ADMIN]);
            })->leftjoin('raw_materials', function ($j) {
                $j->on('raw_materials.id', '=', 'inventories.inventoryable_id')
                    ->where('inventories.inventoryable_type', '=', RawMaterial::MORPH_TYPE)
                    ->where('transactions.quantity', '<', 0)
                    ->whereNull('transactions.remarks')
                    ->whereIn('users.type', [User::TYPE_INVENTORY, User::TYPE_ADMIN]);
            })
            ->groupBy('transactions.inventory_id');
        return $query;
    }

    public function scopeStartFrom($query, Carbon $startDate = null)
    {
        return $query->when($startDate, function ($q, $v) {
            $q->where('transactions.created_at', '>=', $v->format('Y-m-d 00:00:00'));
        });
    }

    public function scopeType($query, $type)
    {
        return $query->select('transactions.*')
            ->join('inventories', 'inventories.id', '=', 'transactions.inventory_id')
            ->where('inventoryable_type', $type);
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
