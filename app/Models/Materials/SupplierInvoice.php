<?php

namespace App\Models\materials;

use App\Models\Users\AppLog;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SupplierInvoice extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'title', 'note', 'supplier_id', 'total_items', 'total_amount', 'payment_due', 'is_paid'];

    public static function createInvoice($supplierId, $code, $title, $note, $paymentDue, $rawMaterials, $updateSupplierMaterials = false)
    {
        try {
            return DB::transaction(function () use ($supplierId, $code, $title, $note, $paymentDue, $rawMaterials, $updateSupplierMaterials) {
                $totalItems = 0;
                $totalAmount = 0;

                foreach ($rawMaterials as $material) {
                    $totalItems += $material['quantity'];
                    $totalAmount += $material['quantity'] * $material['price'];
                }

                // Create the invoice
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
                        'raw_material_id' => $material['id'],
                        'quantity' => $material['quantity'],
                        'price' => $material['price'],
                    ]);

                    if ($updateSupplierMaterials) {
                        SupplierRawMaterial::updateOrCreate(
                            [
                                'supplier_id' => $supplierId,
                                'raw_material_id' => $material['id'],
                            ],
                            ['price' => $material['price']],
                        );
                    }
                }

                // Log success
                AppLog::info('Supplier invoice created successfully', loggable: $invoice);

                return $invoice;
            });
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to create supplier invoice: ' , $e->getMessage());
            return false;
        }
    }

    public function updateNote($note)
    {
        try {
            $this->update(['note' => $note]);

            AppLog::info('Supplier invoice note updated.', loggable: $this);

            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to update supplier invoice note: ' , $e->getMessage());
            return false;
        }
    }

    public function updatePaymentDue($paymentDue)
    {
        try {
            $this->update(['payment_due' => $paymentDue]);

            AppLog::info('Supplier invoice payment due updated.', loggable: $this);

            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to update supplier invoice payment due: ' , $e->getMessage());
            return false;
        }
    }

    public function addRawMaterial($rawMaterialId, $quantity, $price, $updateSupplierMaterials = false)
    {
        try {
            return DB::transaction(function () use ($rawMaterialId, $quantity, $price, $updateSupplierMaterials) {
                $this->total_items += $quantity;
                $this->total_amount += $quantity * $price;
                $this->save();

                InvoiceRawMaterial::create([
                    'supplier_invoice_id' => $this->id,
                    'raw_material_id' => $rawMaterialId,
                    'quantity' => $quantity,
                    'price' => $price,
                ]);

                if ($updateSupplierMaterials) {
                    SupplierRawMaterial::updateOrCreate(
                        [
                            'supplier_id' => $this->supplier_id,
                            'raw_material_id' => $rawMaterialId,
                        ],
                        ['price' => $price],
                    );
                }

                AppLog::info('Raw material added to supplier invoice successfully', loggable: $this);

                return true;
            });
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to add raw material to supplier invoice: ' . $e->getMessage());
            return false;
        }
    }

    public function returnRawMaterial($rawMaterialId, $quantity)
    {
        try {
            return DB::transaction(function () use ($rawMaterialId, $quantity) {
                $invoiceRawMaterial = InvoiceRawMaterial::where('supplier_invoice_id', $this->id)
                    ->where('raw_material_id', $rawMaterialId)
                    ->firstOrFail();

                if ($invoiceRawMaterial->quantity < $quantity) {
                    throw new Exception('Quantity to remove exceeds the available quantity.');
                }

                $invoiceRawMaterial->quantity -= $quantity;
                if ($invoiceRawMaterial->quantity == 0) {
                    $invoiceRawMaterial->delete();
                } else {
                    $invoiceRawMaterial->save();
                }

                $this->total_items -= $quantity;
                $this->total_amount -= $quantity * $invoiceRawMaterial->price;
                $this->save();

                AppLog::info('Raw material removed from supplier invoice successfully', loggable: $this);

                return true;
            });
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to remove raw material from supplier invoice: ' . $e->getMessage());
            return false;
        }
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('code', 'like', '%' . $term . '%')
                ->orWhere('title', 'like', '%' . $term . '%')
                ->orWhere('note', 'like', '%' . $term . '%')
                ->orWhere('total_items', 'like', '%' . $term . '%')
                ->orWhere('total_amount', 'like', '%' . $term . '%')
                ->orWhere('payment_due', 'like', '%' . $term . '%')
                ->orWhereHas('supplier', function ($q) use ($term) {
                    $q->where('name', 'like', '%' . $term . '%');
                });
        });
    }

    //
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function rawMaterials()
    {
        return $this->belongsToMany(RawMaterial::class, 'invoice_raw_materials')->withPivot('quantity', 'price');
    }
}
