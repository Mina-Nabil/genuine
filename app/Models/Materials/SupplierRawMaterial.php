<?php

namespace App\Models\materials;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierRawMaterial extends Model
{
    use HasFactory;

    protected $fillable = ['supplier_id', 'raw_material_id', 'price'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
