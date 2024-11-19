<?php

namespace App\Models\Orders;

use App\Models\Products\Combo;
use App\Models\Products\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodicOrderProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'periodic_order_id',
        'product_id',
        'combo_id',
        'quantity',
        'price',
    ];

    //Scopes
    public function scopeSearch($query, $searchTerm = null)
    {
        if (!is_null($searchTerm)) {
            return $query->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%');
            });
        }

        return $query; // Return the original query if no search term is provided
    }


    public function periodicOrder()
    {
        return $this->belongsTo(periodicOrder::class);
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
