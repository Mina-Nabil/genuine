<?php

namespace App\Models\Orders;

use App\Models\Products\Combo;
use App\Models\Products\Product;
use App\Models\Users\AppLog;
use Exception;
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

    public function editProductInfo(int $newQuantity, float $newPrice): bool
    {
        try {
            // Update quantity and price
            $this->quantity = $newQuantity;
            $this->price = $newPrice;
            $this->save();

            $productName = $this->product->name;

            AppLog::info("Product ".$productName." updated.", loggable: $this->order);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error("Failed to update periodic order product" , $e->getMessage(), loggable:$this);
            return false;
        }
    }

    public function deleteProduct(): bool
    {
        try {
            $productName = $this->product->name;

            $this->delete();

            AppLog::info("Product ".$productName." removed.", loggable: $this->order);
            return true;
        } catch (Exception $e) {
            report($e); 
            AppLog::error("Failed to delete product: " . $e->getMessage(), ['product_id' => $this->product_id]);
            return false;
        }
    }

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
