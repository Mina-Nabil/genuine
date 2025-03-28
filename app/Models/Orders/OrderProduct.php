<?php

namespace App\Models\Orders;

use App\Models\Products\Combo;
use App\Models\Products\Inventory;
use App\Models\Products\Product;
use App\Models\Users\AppLog;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class OrderProduct extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'order_products';

    protected $fillable = [
        'order_id', // Foreign key to the order
        'product_id', // Foreign key to the product
        'combo_id',
        'quantity', // Quantity of the product
        'price', // Price of the product
        'is_ready',
    ];

    public function setAsReady()
    {
        try {

            if ($this->order->status !== Order::STATUS_NEW || $this->is_ready) {
                return false;
            }

            if ($this->product->inventory->fulfillCommit($this->quantity)) {
                $this->is_ready = 1;
                $this->save();
                AppLog::info('product ' . $this->product->name . ' set to ready', loggable: $this->order);
            } else {
                AppLog::info('Can\'t set product ' . $this->product->name . ' set to ready', loggable: $this->order);

            }


            if($this->order->areAllProductsReady()){
                $this->order->setStatus(Order::STATUS_READY);
            }

            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to toggle order product ready', $e->getMessage());
            return false;
        }
    }

    public function toggleDeletedProductReady()
    {
        try {

            $this->is_ready = !$this->is_ready;
            $this->save();

            AppLog::info('product ' . $this->product->name . ' set to ' . (!$this->is_ready ? 'not' : '') . ' ready', loggable: $this->order);

            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to toggle order product ready', $e->getMessage());
            return false;
        }
    }

    /**
     * Scope for production planning
     *
     * @param Builder $query
     * @param string|null $deliveryDate
     * @return Builder
     */
    public function scopeProductionPlanning(Builder $query, ?string $deliveryDate, bool $isToDate = false, string $searchText = null): Builder
    {
        return $query
            ->join('orders', 'order_products.order_id', '=', 'orders.id')
            ->join('products', 'order_products.product_id', '=', 'products.id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('inventories', function ($join) {
                // Join on inventory table with polymorphic constraints
                $join->on('products.id', '=', 'inventories.inventoryable_id')->where('inventories.inventoryable_type', '=', 'Product');
            })
            ->whereIn('orders.status', [Order::STATUS_NEW, Order::STATUS_READY, Order::STATUS_IN_DELIVERY, Order::STATUS_DONE])
            ->whereNull('order_products.deleted_at')
            ->whereDate('orders.delivery_date', '>=', now()->toDateString())
            ->when($deliveryDate, function ($q) use ($deliveryDate, $isToDate) {
                if ($isToDate) {
                    // Filter orders where delivery_date is less than or equal to the deliveryDate
                    return $q->where('orders.delivery_date', '<=', $deliveryDate);
                } else {
                    // Filter orders where delivery_date is exactly the deliveryDate
                    return $q->where('orders.delivery_date', '=', $deliveryDate);
                }
            })
            ->when($searchText, function ($q) use ($searchText) {
                return $q->where('products.name', 'like', '%' . $searchText . '%');
            })
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                'categories.name as category_name',
                'inventories.on_hand',
                // DB::raw('(inventories.on_hand + inventories.available) as current_stock'),
                DB::raw('SUM(order_products.quantity) as required_stock'),
                DB::raw('(inventories.on_hand - SUM(order_products.quantity)) as production_required'),
            )
            ->groupBy('products.id', 'products.name', 'inventories.on_hand', 'inventories.committed', 'inventories.available')
            ->orderBy('products.id');
    }

    // Define the relation to the Inventory model through the Product
    public function inventory()
    {
        return $this->hasOneThrough(Inventory::class, Product::class, 'id', 'inventoryable_id', 'product_id', 'id');
    }

    // Define the relationship to the Order model
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Define the relationship to the Product model
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function combo()
    {
        return $this->belongsTo(Combo::class);
    }
}
