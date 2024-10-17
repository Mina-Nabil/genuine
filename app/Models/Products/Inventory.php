<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'on_hand',
        'committed',
    ];

    /**
     * Get the available inventory dynamically.
     *
     * @return int
     */
    public function getAvailableAttribute()
    {
        return $this->on_hand - $this->committed;
    }

    /**
     * Get the unavailable inventory dynamically.
     *
     * @return int
     */
    public function getUnavailableAttribute()
    {
        return $this->on_hand - $this->available;
    }

    /**
     * Define the one-to-one relationship with the Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
