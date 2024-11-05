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
