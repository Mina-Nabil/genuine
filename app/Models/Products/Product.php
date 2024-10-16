<?php

namespace App\Models\Products;

use App\Models\Users\AppLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'name', 'price', 'weight'];

    // Create a new product
    public static function createProduct($category_id, $name, $price, $weight)
    {
        try {
            $product = self::create([
                'category_id' => $category_id,
                'name' => $name,
                'price' => $price,
                'weight' => $weight,
            ]);
            AppLog::info("Product created successfully with ID {$product->id}", loggable: $product);
            return $product;
        } catch (\Exception $e) {
            AppLog::error('Failed to create product: ' . $e->getMessage());
            return null;
        }
    }

    // Update product
    public function updateProduct($category_id, $name, $price, $weight)
    {
        try {
            $this->category_id = $category_id;
            $this->name = $name;
            $this->price = $price;
            $this->weight = $weight;
            $this->save();
            AppLog::info("Product ID {$this->id} updated successfully", loggable: $this);
            return true;
        } catch (\Exception $e) {
            AppLog::error("Failed to update product ID {$this->id}: ", $e->getMessage(), loggable: $this);
            return false;
        }
    }

    // Delete product
    public function deleteProduct()
    {
        try {
            $this->delete();
            AppLog::info("Product ID {$this->id} deleted successfully");
            return true;
        } catch (\Exception $e) {
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

    public function scopeSortByName($query, $direction = 'asc')
    {
        return $query->orderBy('name', $direction);
    }

    public function scopeSortByPrice($query, $direction = 'asc')
    {
        return $query->orderBy('price', $direction);
    }

    public function scopeSortByWeight($query, $direction = 'asc')
    {
        return $query->orderBy('weight', $direction);
    }

    //Relations
    public function category() :BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // $products = Product::search($searchTerm)
    //                ->filterByCategory($categoryId)
    //                ->filterByPriceRange($minPrice, $maxPrice)
    //                ->filterByWeightRange($minWeight, $maxWeight)
    //                ->sortByName($sortDirection) // or sortByPrice($sortDirection) / sortByWeight($sortDirection)
    //                ->get();
}
