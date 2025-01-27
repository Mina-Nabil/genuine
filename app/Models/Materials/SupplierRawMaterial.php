<?php

namespace App\Models\Materials;

use App\Models\Users\AppLog;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierRawMaterial extends Model
{
    use HasFactory;

    protected $fillable = ['supplier_id', 'raw_material_id', 'price', 'expiration_date'];

    protected $casts = [
        'expiration_date' => 'date',
    ];

    public function editInfo($price, $expirationDate)
    {
        try {
            $this->update([
                'price' => $price,
                'expiration_date' => $expirationDate,
            ]);

            AppLog::info('Supplier raw material info updated.', loggable: $this);

            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to update supplier raw material info: ', $e->getMessage(), loggable: $this);
            return false;
        }
    }

    public function deleteRawMaterial()
    {
        try {
            $this->delete();

            AppLog::info('Supplier raw material deleted.', loggable: $this);

            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to delete supplier raw material: ', $e->getMessage());
            return false;
        }
    }

    public function scopeNearlyExpired($query)
    {
        return $query->where('expiration_date', '<=', now()->addWeek())->where('expiration_date', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expiration_date', '<', now());
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
