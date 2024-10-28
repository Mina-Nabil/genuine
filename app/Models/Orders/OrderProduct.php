<?php

namespace App\Models\Orders;

use App\Models\Products\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',    // Foreign key to the order
        'product_id',  // Foreign key to the product
        'quantity',    // Quantity of the product
        'price',       // Price of the product
    ];

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
