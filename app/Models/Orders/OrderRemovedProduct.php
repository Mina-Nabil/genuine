<?php

namespace App\Models\Orders;

use App\Models\Products\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderRemovedProduct extends Model
{
    use HasFactory;

    protected $table = 'order_removed_products';

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'reason',
    ];

    const removeReasons = [
        'Product damaged',
        'Wrong item sent',
        'Product not as described',
        'Changed my mind',
        'Order was canceled by the seller',
        'Product arrived late',
        'Quality not satisfactory',
        'Defective product',
        'Product not needed anymore',
        'Incorrect quantity received',
        'Better price found elsewhere',
        'Item out of stock',
        'Customer service issues',
        'Payment issues',
        'Shipping issues',
        'Product expired',
        'Other',
    ];



    //relations
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
