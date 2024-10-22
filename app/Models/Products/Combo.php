<?php

namespace App\Models\Products;

use App\Models\Users\AppLog;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Combo extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Create a new combo with associated products and prices.
     *
     * @param string $name
     * @param array $productData // Each item should have 'product_id', 'quantity', and 'price'
     * @return bool
     */
    public static function createCombo($name, $productData)
    {
        try {
            // Create the new combo without price
            $combo = self::create([
                'name' => $name,
            ]);

            // Associate products with the combo, including price and quantity for each product
            foreach ($productData as $data) {
                $combo->products()->attach($data['product_id'], [
                    'quantity' => $data['quantity'],
                    'price' => $data['price'], // Individual product price in the combo
                ]);
            }

            AppLog::info("Combo '{$name}' created successfully with products.", loggable: $combo);
            return $combo;
        } catch (Exception $e) {
            report($e);
            AppLog::error("Failed to create combo '{$name}': " . $e->getMessage(), loggable: null);
            return false;
        }
    }

    /**
     * Update the name of the current combo instance.
     *
     * @param string $newName
     * @return bool
     */
    public function updateCombo(string $newName): bool
    {
        try {
            // Update the combo's name
            $this->update([
                'name' => $newName,
            ]);

            AppLog::info("Combo '{$this->id}' updated to '{$newName}'.", loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error("Failed to update combo '{$this->id}': " . $e->getMessage(), loggable: $this);
            return false;
        }
    }

    /**
     * Add or update a product in the combo.
     *
     * @param int $productId
     * @param int $quantity
     * @param float $price
     * @return bool
     */
    public function addProductToCombo(int $productId, int $quantity, float $price): bool
    {
        try {
            // Use syncWithoutDetaching to add or update the product's quantity and price
            $this->products()->syncWithoutDetaching([
                $productId => [
                    'quantity' => $quantity,
                    'price' => $price, // Update or insert the price for this product in the combo
                ],
            ]);

            AppLog::info("Product ID {$productId} added/updated in combo '{$this->name}' successfully", loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error("Failed to add/update product ID {$productId} in combo '{$this->name}': " . $e->getMessage(), loggable: $this);
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
            report($e);
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
            ->withPivot('quantity', 'price') // Include the quantity in the pivot table
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
        // Ensure the column is not null
        if ($column) {
            // Check if sorting by a column in the main table
            if (in_array($column, Schema::getColumnListing($this->getTable()))) {
                return $query->orderBy($column, $direction);
            }

            // Check if sorting by product count in the combo
            if ($column === 'product_count') {
                return $query->withCount('products')->orderBy('products_count', $direction);
            }
        }

        // If column is not valid, just return the query without sorting
        return $query;
    }

    // Calculate the total price of the combo (sum of all product prices)
    public function getTotalPriceAttribute()
    {
        return $this->products->sum(function ($product) {
            return $product->pivot->price;
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
