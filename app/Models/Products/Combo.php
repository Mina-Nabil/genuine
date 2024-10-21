<?php

namespace App\Models\Products;

use App\Models\Users\AppLog;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Combo extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price'];

    /**
     * Create a new combo with associated products and initialize inventory.
     *
     * @param string $name
     * @param float $price
     * @param array $productData // Each item should have 'product_id' and 'quantity'
     * @param int $initialQuantity // Initial quantity for the inventory
     * @return bool
     */
    public static function createCombo($name, $price, $productData, $initialQuantity)
    {
        try {
            // Create the new combo
            $combo = self::create([
                'name' => $name,
                'price' => $price,
            ]);

            // Associate products with the combo
            foreach ($productData as $data) {
                $combo->products()->attach($data['product_id'], ['quantity' => $data['quantity']]);
            }

            // Create an inventory record for the combo
            Inventory::create([
                'inventoryable_id' => $combo->id, // The ID of the combo
                'inventoryable_type' => Combo::class, // The type of the model (Combo)
                'quantity' => $initialQuantity, // The initial quantity
            ]);

            AppLog::info("Combo '{$name}' created successfully with products.", loggable: $combo);
            return true;
        } catch (Exception $e) {
            AppLog::error("Failed to create combo '{$name}': " . $e->getMessage(), loggable: null);
            return false;
        }
    }

    /**
     * Add a product to the combo.
     *
     * @param int $productId
     * @param int $quantity
     * @return bool
     */
    public function addProductToCombo(int $productId, int $quantity): bool
    {
        try {
            $this->products()->attach($productId, ['quantity' => $quantity]);
            AppLog::info("Product ID {$productId} added to combo '{$this->name}' successfully", loggable: $this);
            return true;
        } catch (Exception $e) {
            AppLog::error("Failed to add product ID {$productId} to combo '{$this->name}': ", $e->getMessage(), loggable: $this);
            return false;
        }
    }

    /**
     * Remove a product from the combo.
     *
     * @param int $productId
     * @return bool
     */
    public function removeProductFromCombo(int $productId): bool
    {
        try {
            $this->products()->detach($productId);
            AppLog::info("Product ID {$productId} removed from combo '{$this->name}' successfully", loggable: $this);
            return true;
        } catch (Exception $e) {
            AppLog::error("Failed to remove product ID {$productId} from combo '{$this->name}': ", $e->getMessage(), loggable: $this);
            return false;
        }
    }

    /**
     * Relationships
     */

    public function products()
    {
        return $this->belongsToMany(Product::class, 'combo_products')
            ->withPivot('quantity') // Include the quantity in the pivot table
            ->withTimestamps(); // Automatically manage created_at and updated_at timestamps
    }

    /**
     * Scopes
     */

    // Scope to search combos
    public function scopeSearch(Builder $query, $term)
    {
        return $query->where('combos.name', 'LIKE', "%{$term}%")->orWhereHas('products', function (Builder $q) use ($term) {
            $q->where('products.name', 'LIKE', "%{$term}%");
        });
    }

    // Scope to sort combos by name (default order: ascending)
    public function scopeSortByName(Builder $query, $direction = 'asc')
    {
        return $query->orderBy('name', $direction);
    }

    // Scope to sort combos by total product price (custom logic for sorting by sum of product prices in the combo)
    public function scopeSortByTotalPrice(Builder $query, $direction = 'asc')
    {
        return $query->withSum('products', 'combo_products.price')->orderBy('products_sum_combo_products_price', $direction);
    }

    // Scope to filter combos by a minimum number of products
    public function scopeHasAtLeastProducts(Builder $query, $count = 1)
    {
        return $query->has('products', '>=', $count);
    }

    /**
     * Custom Functions
     */

     public function scopeSortBy($query, $column = null, $direction = 'asc')
    {
        // Ensure the column is not null and exists in the table
        if ($column && in_array($column, Schema::getColumnListing($this->getTable()))) {
            return $query->orderBy($column, $direction);
        }

        // If column is not valid, just return the query without sorting
        return $query;
    }

    // Calculate the total price of the combo (sum of all product prices)
    public function getTotalPriceAttribute()
    {
        return $this->products->sum(function ($product) {
            return $product->pivot->price * $product->pivot->quantity;
        });
    }

    // Count the total number of products in the combo
    public function getTotalProductsAttribute()
    {
        return $this->products->sum('pivot.quantity');
    }

    // Check if a specific product is part of the combo
    public function hasProduct(Product $product)
    {
        return $this->products->contains($product->id);
    }
}

// File: app/Models/Products/Combo.php
