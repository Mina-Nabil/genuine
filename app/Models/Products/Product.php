<?php

namespace App\Models\Products;

use App\Models\Users\AppLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Exception;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'name', 'price', 'weight', 'desc'];

    // Create a new product
    public static function createProduct($category_id, $name, $price, $weight, $desc = null, $initial_quantity)
    {
        try {
            $product = self::create([
                'category_id' => $category_id,
                'name' => $name,
                'price' => $price,
                'weight' => $weight,
                'desc' => $desc,
            ]);

            // Create an inventory record for the product
            Inventory::initializeQuantity($product->id,$initial_quantity);

            AppLog::info('Product created successfully', loggable: $product);
            return $product;
        } catch (Exception $e) {
            AppLog::error('Failed to create product: ' . $e->getMessage());
            return null;
        }
    }

    // Update product
    public function updateProduct($category_id, $name, $price, $weight, $desc = null)
    {
        try {
            $this->category_id = $category_id;
            $this->name = $name;
            $this->price = $price;
            $this->weight = $weight;
            $this->desc = $desc;
            $this->save();
            AppLog::info('Product updated successfully', loggable: $this);
            return true;
        } catch (Exception $e) {
            AppLog::error("Failed to update product ID {$this->id}: ", $e->getMessage(), loggable: $this);
            return false;
        }
    }

    // Update product
    public function updateProductTitleDesc($name, $desc = null)
    {
        try {
            $this->name = $name;
            $this->desc = $desc;
            $this->save();
            AppLog::info('Product title and description updated', loggable: $this);
            return true;
        } catch (Exception $e) {
            AppLog::error("Failed to update product ID {$this->id}: ", $e->getMessage(), loggable: $this);
            return false;
        }
    }

    // Update product
    public function updateProductPriceWeight($price, $weight)
    {
        try {
            $this->price = $price;
            $this->weight = $weight;
            $this->save();
            AppLog::info('Product price and weight updated', loggable: $this);
            return true;
        } catch (\Exception $e) {
            AppLog::error("Failed to update product ID {$this->id}: ", $e->getMessage(), loggable: $this);
            return false;
        }
    }

    public function addComment(string $comment): void
    {
        AppLog::comment($comment, $desc = null, loggable: $this);
    }

    // Delete product
    public function deleteProduct()
    {
        try {
            $this->delete();
            AppLog::info("Product ID {$this->id} deleted successfully");
            return true;
        } catch (Exception $e) {
            AppLog::error("Failed to delete product ID {$this->id}: ", $e->getMessage(), loggable: $this);
            return false;
        }
    }

    //Scopes
    public function scopeSearch($query, $searchTerm = null)
    {
        if (!is_null($searchTerm)) {
            return $query->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')->orWhereHas('category', function ($query) use ($searchTerm) {
                    $query->where('name', 'like', '%' . $searchTerm . '%');
                });
            });
        }

        return $query; // Return the original query if no search term is provided
    }

    public function scopeFilterByCategory($query, $categoryId = null)
    {
        if (!is_null($categoryId)) {
            return $query->where('category_id', $categoryId);
        }

        return $query; // Return the original query if no category ID is provided
    }

    public function scopeFilterByPriceRange($query, $minPrice = null, $maxPrice = null)
    {
        if (!is_null($minPrice) && !is_null($maxPrice)) {
            return $query->whereBetween('price', [$minPrice, $maxPrice]);
        } elseif (!is_null($minPrice)) {
            return $query->where('price', '>=', $minPrice);
        } elseif (!is_null($maxPrice)) {
            return $query->where('price', '<=', $maxPrice);
        }

        return $query; // Return the original query if both prices are null
    }

    public function scopeFilterByWeightRange($query, $minWeight = null, $maxWeight = null)
    {
        if (!is_null($minWeight) && !is_null($maxWeight)) {
            return $query->whereBetween('weight', [$minWeight, $maxWeight]);
        } elseif (!is_null($minWeight)) {
            return $query->where('weight', '>=', $minWeight);
        } elseif (!is_null($maxWeight)) {
            return $query->where('weight', '<=', $maxWeight);
        }

        return $query; // Return the original query if both weights are null
    }

    public function scopeSortBy($query, $column = null, $direction = 'asc')
    {
        // Ensure the column is not null and exists in the table
        if ($column && in_array($column, \Illuminate\Support\Facades\Schema::getColumnListing($this->getTable()))) {
            return $query->orderBy($column, $direction);
        }

        // If column is not valid, just return the query without sorting
        return $query;
    }

    //Relations
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(AppLog::class, 'loggable_id')->where('loggable_type', self::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function transactions()
    {
        return $this->hasManyThrough(Transaction::class, Inventory::class);
    }

    // Calculate unavailable stock
    public function getUnavailableAttribute()
    {
        return $this->inventory->on_hand - $this->available;
    }
}
