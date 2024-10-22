<?php

namespace App\Models\Products;

use App\Models\Users\AppLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Exception;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Product extends Model
{
    use HasFactory;

    const MORPH_TYPE = 'product';

    protected $fillable = ['category_id', 'name', 'price', 'weight', 'desc'];

    /**
     * Create a new product along with its initial inventory.
     *
     * @param int $category_id
     * @param string $name
     * @param float $price
     * @param float $weight
     * @param string|null $desc
     * @param int $initial_quantity
     * @return \App\Models\Product|null
     */
    public static function createProduct($category_id, $name, $price, $weight, $desc = null, $initial_quantity)
    {
        try {
            // Create a new product
            $product = self::create([
                'category_id' => $category_id,
                'name' => $name,
                'price' => $price,
                'weight' => $weight,
                'desc' => $desc,
            ]);

            // Initialize inventory for the created product
            Inventory::initializeQuantity($product, $initial_quantity);

            // Log the successful creation
            AppLog::info('Product created successfully', loggable: $product);

            return $product;
        } catch (Exception $e) {
            // Log the error if creation fails
            report($e);
            AppLog::error('Failed to create product: ' . $e->getMessage());
            return null;
        }
    }


    public static function importData($file)
    {
        $spreadsheet = IOFactory::load($file);
        if (!$spreadsheet) {
            throw new Exception('Failed to read files content');
        }
        $activeSheet = $spreadsheet->getSheet(1);
        $highestRow = $activeSheet->getHighestDataRow();

        for ($i = 2; $i <= $highestRow; $i++) {
            $category = $activeSheet->getCell('B' . $i)->getValue();
            //skip if no car category found
            if (!$category) {
                continue;
            }
            $catg = Category::firstOrCreate([
                'name' => $category,
            ]);

            //skip if no brand found
            if (!$catg) {
                continue;
            }
            $product_name = $activeSheet->getCell('C' . $i)->getValue();
            $price = $activeSheet->getCell('D' . $i)->getValue();
            $weight = $activeSheet->getCell('E' . $i)->getValue();
            $balance = $activeSheet->getCell('F' . $i)->getValue();

            $prod = self::firstOrCreate([
                'category_id'   => $catg->id,
                'name'          => $product_name,
            ], [
                'price'         =>  $price,
                'weight'        =>  $weight,
            ]);
            if (!$prod->inventory()->first())
                Inventory::initializeQuantity($prod, $balance);
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
        return $this->morphOne(Inventory::class, 'inventoryable');
    }

    public function transactions()
    {
        return $this->hasManyThrough(Transaction::class, Inventory::class, 'inventoryable_id', 'inventory_id')->where('inventoryable_type', self::class);
    }

    // Calculate unavailable stock
    public function getUnavailableAttribute()
    {
        return $this->inventory->on_hand - $this->available;
    }
}
