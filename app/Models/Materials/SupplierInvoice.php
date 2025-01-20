<?php

namespace App\Models\materials;

use App\Models\Payments\BalanceTransaction;
use App\Models\Payments\CustomerPayment;
use App\Models\Users\AppLog;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierInvoice extends Model
{
    use HasFactory;

    const MORPH_TYPE = 'supplier-invoice';
    
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
                $invoiceRawMaterial = InvoiceRawMaterial::where('supplier_invoice_id', $this->id)
                    ->where('raw_material_id', $rawMaterialId)
                    ->first();

                if ($invoiceRawMaterial) {
                    $invoiceRawMaterial->quantity += $quantity;
                    $invoiceRawMaterial->price = $price;
                    $invoiceRawMaterial->save();
                } else {
                    InvoiceRawMaterial::create([
                        'supplier_invoice_id' => $this->id,
                        'raw_material_id' => $rawMaterialId,
                        'quantity' => $quantity,
                        'price' => $price,
                    ]);
                }

                if ($updateSupplierMaterials) {
                    SupplierRawMaterial::updateOrCreate(
                        [
                            'supplier_id' => $this->supplier_id,
                            'raw_material_id' => $rawMaterialId,
                        ],
                        ['price' => $price],
                    );
                }

                $this->refreshTotals();

                AppLog::info('Raw material added to supplier invoice successfully', loggable: $this);

                return true;
            });
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to add raw material to supplier invoice: ' , $e->getMessage());
            return false;
        }
    }

    public function returnAllQuantityOfRawMaterial($rawMaterialId)
    {
        try {
            return DB::transaction(function () use ($rawMaterialId) {
                $invoiceRawMaterial = InvoiceRawMaterial::where('supplier_invoice_id', $this->id)
                    ->where('raw_material_id', $rawMaterialId)
                    ->firstOrFail();

                $this->returnRawMaterial($rawMaterialId, $invoiceRawMaterial->quantity);

                if ($this->rawMaterials()->count() == 0) {
                    throw new Exception('Cannot return the last raw material from the invoice.');
                } else {
                    AppLog::info('All quantity of raw material returned from supplier invoice successfully', loggable: $this);
                }

                return true;
            });
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to return all quantity of raw material from supplier invoice: ' , $e->getMessage());
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

                $this->refreshTotals();
                $this->save();

                AppLog::info('Raw material removed from supplier invoice successfully', loggable: $this);

                return true;
            });
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to remove raw material from supplier invoice: ' , $e->getMessage());
            return false;
        }
    }
    
    public function createPayment($amount, $paymentMethod, $paymentDate, $isAddToBalance = false)
    {
        return DB::transaction(function () use ($amount, $paymentMethod, $paymentDate, $isAddToBalance) {
            try {
                /** @var User */
                $loggedInUser = Auth::user();
                // if ($loggedInUser && !$loggedInUser->can('pay', $this)) {
                //     return false;
                // }

                // Check if supplier exists for the invoice
                $supplier = $this->supplier;
                if (!$supplier) {
                    throw new Exception('Invoice does not have an associated supplier.');
                }

                if ($this->remaining_to_pay < $amount) {
                    throw new Exception('Payment amount exceeds the remaining amount to be paid.');
                }

                // Step 1: Adjust supplier balance if specified
                if ($isAddToBalance) {
                    $supplier->balance += $amount;
                    $supplier->save();
                }

                $new_type_balance = CustomerPayment::calculateNewBalance(-$amount, $paymentMethod);

                // Step 2: Create the payment record
                $payment = $this->payments()->create([
                    'supplier_id' => $supplier->id,
                    'invoice_id' => $this->id,
                    'amount' => -$amount,
                    'payment_method' => $paymentMethod,
                    'type_balance' => $new_type_balance,
                    'payment_date' => $paymentDate,
                    'created_by' => $loggedInUser->id,
                ]);

                // Step 3: Conditionally create a balance transaction log
                if ($isAddToBalance) {
                    $this->balanceTransactions()->create([
                        'order_id' => $this->id,
                        'amount' => -$amount,
                        'balance' => $supplier->balance,
                        'description' => 'Added to balance',
                        'created_by' => $loggedInUser->id,
                    ]);
                }

                // Step 4: Check if the payment amount matches the invoice total and mark as paid if so
                if ($amount == $this->total_amount) {
                    $this->is_paid = true;
                    $this->save();
                }

                AppLog::info('Payment created for invoice', loggable: $this);
                return $payment;
            } catch (QueryException $e) {
                if ($e->getCode() === '40001') {
                    // Deadlock error code in MySQL
                    AppLog::error('Deadlock encountered', loggable: $this);
                }
                report($e);
                AppLog::error('Failed creating payment for invoice', $e->getMessage(), loggable: $this);
                return false;
            }
        });
    }

    public function refreshTotals()
    {
        try {
            return DB::transaction(function () {
                $totalItems = $this->rawMaterials->sum('pivot.quantity');
                $totalAmount = $this->rawMaterials->sum(function ($material) {
                    return $material->pivot->quantity * $material->pivot->price;
                });
                $this->total_items = $totalItems;
                $this->total_amount = $totalAmount;
                $this->save();
                return true;
            });
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to refresh supplier invoice totals: ' , $e->getMessage());
            return false;
        }
    }

    public function getRemainingToPayAttribute()
    {
        $this->loadMissing(['payments', 'balanceTransactions']);
        $totalPayments = $this->payments->sum('amount');
        $totalBalanceTransactions = $this->balanceTransactions->sum('amount');

        $remainingAmount = round($this->total_amount - (-$totalPayments + $totalBalanceTransactions));

        return $remainingAmount > 0 ? $remainingAmount : 0;
    }

    public function getTotalPaidAttribute()
    {
        $this->loadMissing('payments');
        return abs($this->payments->sum('amount'));
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

    public function payments(): HasMany
    {
        return $this->hasMany(CustomerPayment::class, 'invoice_id');
    }

    public function balanceTransactions(): MorphMany
    {
        return $this->morphMany(BalanceTransaction::class, 'transactionable');
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
