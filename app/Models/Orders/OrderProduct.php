<?php

namespace App\Models\Orders;

use App\Models\Products\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class OrderProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id', // Foreign key to the order
        'product_id', // Foreign key to the product
        'quantity', // Quantity of the product
        'price', // Price of the product
    ];

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
            ->join('inventories', function ($join) {
                // Join on inventory table with polymorphic constraints
                $join->on('products.id', '=', 'inventories.inventoryable_id')->where('inventories.inventoryable_type', '=', 'Product');
            })
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
                'inventories.on_hand',
                'inventories.committed',
                'inventories.available',
                // DB::raw('(inventories.on_hand + inventories.available) as current_stock'),
                DB::raw('SUM(order_products.quantity) as required_stock'),
                DB::raw('-(inventories.on_hand - SUM(order_products.quantity)) as production_required'),
            )
            ->groupBy('products.id', 'products.name', 'inventories.on_hand', 'inventories.committed', 'inventories.available');
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
}
