<?php

namespace App\Models\Materials;

use App\Models\Products\Inventory;
use App\Models\Users\AppLog;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function scopeSearch($query, $term)
    {
        $term = "%{$term}%";
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', $term)->orWhere('desc', 'like', $term);
        });
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
