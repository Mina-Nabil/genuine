<?php

namespace App\Models\Materials;

use App\Models\Products\Inventory;
use App\Models\Products\Transaction;
use App\Models\Users\AppLog;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RawMaterial extends Model
{
    use HasFactory;

    const MORPH_TYPE = 'raw_materials';

    protected $fillable = ['name', 'desc', 'min_limit'];

    public static function createRawMaterial($name, $min_limit = null, $desc = null, $initial_quantity = 0)
    {
        try {
            $rawMaterial = self::create([
                'name' => $name,
                'desc' => $desc,
                'min_limit' => $min_limit,
            ]);

            Inventory::initializeQuantity($rawMaterial, $initial_quantity);

            AppLog::info('Raw material created successfully', loggable: $rawMaterial);

            return $rawMaterial;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to create raw material: ' . $e->getMessage());
            return null;
        }
    }

    public function updateInfo($name, $desc, $min_limit)
    {
        try {
            $this->update([
                'name' => $name,
                'desc' => $desc,
                'min_limit' => $min_limit,
            ]);

            AppLog::info('Raw material updated successfully', loggable: $this);

            return $this;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to update raw material: ' . $e->getMessage(), loggable: $this);
            return null;
        }
    }

    public function scopeSearch($query, $term)
    {
        $term = "%{$term}%";
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', $term)->orWhere('desc', 'like', $term);
        });
    }

    public function transactions()
    {
        return $this->hasManyThrough(Transaction::class, Inventory::class, 'inventoryable_id', 'inventory_id')->where('inventoryable_type', self::MORPH_TYPE);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(AppLog::class, 'loggable_id')->where('loggable_type', self::MORPH_TYPE);
    }

    public function supplierInvoices()
    {
        return $this->belongsToMany(SupplierInvoice::class, 'invoice_raw_materials')->withPivot('quantity', 'price');
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'supplier_raw_materials')->withPivot('price');
    }

    public function inventory()
    {
        return $this->morphOne(Inventory::class, 'inventoryable');
    }
}
