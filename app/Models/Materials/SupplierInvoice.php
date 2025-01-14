<?php

namespace App\Models\materials;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierInvoice extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'title', 'note', 'supplier_id', 'total_items', 'total_amount', 'payment_due', 'is_paid'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function rawMaterials()
    {
        return $this->belongsToMany(RawMaterial::class, 'invoice_raw_materials')
            ->withPivot('quantity', 'price');
    }
}
