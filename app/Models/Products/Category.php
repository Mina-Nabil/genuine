<?php

namespace App\Models\Products;

use App\Models\Users\AppLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Create a new category
    public static function createCategory($name)
    {
        try {
            $category = self::create(['name' => $name]);
            AppLog::info("Category created successfully with ID {$category->id}",$category);
            return $category;
        } catch (\Exception $e) {
            AppLog::error("Failed to create category: " . $e->getMessage());
            return null;
        }
    }

    // Update category name
    public function updateCategory($name)
    {
        try {
            $this->name = $name;
            $this->save();
            AppLog::info("Category ID {$this->id} updated successfully",loggable:$this);
            return true;
        } catch (\Exception $e) {
            AppLog::error("Failed to update category ID {$this->id}: " . $e->getMessage(),loggable:$this);
            return false;
        }
    }

    // Delete category
    public function deleteCategory()
    {
        try {
            $this->delete();
            AppLog::info("Category ID {$this->id} deleted successfully");
            return true;
        } catch (\Exception $e) {
            AppLog::error("Failed to delete category ID {$this->id}: " . $e->getMessage());
            return false;
        }
    }

    // Relationships
    public function products() :HasMany
    {
        return $this->hasMany(Product::class);
    }
}
