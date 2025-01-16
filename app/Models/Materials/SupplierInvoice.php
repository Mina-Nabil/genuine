<?php

namespace App\Models\materials;

use App\Models\Users\AppLog;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierInvoice extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'title', 'note', 'supplier_id', 'total_items', 'total_amount', 'payment_due', 'is_paid'];

    public static function createInvoice($supplierId, $code, $title, $note, $paymentDue, $rawMaterials, $updateSupplierMaterials = false)
    {
        try {
            $totalItems = 0;
            $totalAmount = 0;

            foreach ($rawMaterials as $material) {
                $totalItems += $material['quantity'];
                $totalAmount += $material['quantity'] * $material['price'];
            }

            $invoice = self::create([
                'supplier_id' => $supplierId,
                'code' => $code,
                'title' => $title,
                'note' => $note,
                'payment_due' => $paymentDue,
                'total_items' => $totalItems,
                'total_amount' => $totalAmount,
            ]);

            foreach ($rawMaterials as $material) {
                InvoiceRawMaterial::create([
                    'supplier_invoice_id' => $invoice->id,
                    'raw_material_id' => $material['raw_material_id'],
                    'quantity' => $material['quantity'],
                    'price' => $material['price'],
                ]);

                if ($updateSupplierMaterials) {
                    SupplierRawMaterial::updateOrCreate(
                        [
                            'supplier_id' => $supplierId,
                            'raw_material_id' => $material['raw_material_id'],
                        ],
                        ['price' => $material['price']],
                    );
                }
            }

            AppLog::info('Supplier invoice created successfully', loggable: $invoice);

            return $invoice;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to create supplier invoice: ' . $e->getMessage());
            return null;
        }
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function rawMaterials()
    {
        return $this->belongsToMany(RawMaterial::class, 'invoice_raw_materials')->withPivot('quantity', 'price');
    }
}
