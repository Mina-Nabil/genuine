<?php

namespace App\Models\Materials;

use App\Models\Materials\SupplierRawMaterial;
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

    protected $fillable = ['code', 'serial', 'title', 'note', 'supplier_id', 'total_items', 'total_amount', 'extra_fee_description', 'extra_fee_amount', 'payment_due', 'is_paid', 'entry_date'];

    protected $casts = [
        'payment_due' => 'date',
        'entry_date' => 'date',
    ];

    public static function createInvoice($supplierId, $entryDate, $rawMaterials, $code = null, $title = null, $note = null, $paymentDue = null, $extraFeeDescription = null, $extraFeeAmount = null)
    {
        if (!$entryDate) {
            throw new Exception('Entry date is required.');
        }

        try {
            return DB::transaction(function () use ($supplierId, $code, $title, $note, $paymentDue, $rawMaterials, $entryDate, $extraFeeDescription, $extraFeeAmount) {
                $totalItems = 0;
                $totalAmount = 0;

                foreach ($rawMaterials as $material) {
                    $totalItems += $material['quantity'];
                    $totalAmount += $material['quantity'] * $material['price'];
                }

                if ($extraFeeAmount) {
                    $totalAmount += $extraFeeAmount;
                }

                // Create the invoice
                $invoice = self::create([
                    'supplier_id' => $supplierId,
                    'serial' => self::generateInvoiceSerial($entryDate),
                    'code' => $code,
                    'title' => $title,
                    'note' => $note,
                    'payment_due' => $paymentDue,
                    'total_items' => $totalItems,
                    'total_amount' => $totalAmount,
                    'entry_date' => $entryDate,
                    'extra_fee_description' => $extraFeeDescription,
                    'extra_fee_amount' => $extraFeeAmount ?? 0,
                ]);

                foreach ($rawMaterials as $material) {
                    $m = InvoiceRawMaterial::create([
                        'supplier_invoice_id' => $invoice->id,
                        'raw_material_id' => $material['id'],
                        'quantity' => $material['quantity'],
                        'price' => $material['price'],
                    ]);

                    $m->rawMaterial->inventory->addTransaction($material['quantity'], 'Added From Invoice');
                }

                $supplier = $invoice->supplier;
                $supplier->updateBalance($totalAmount, 'Invoice created #' . $invoice->code ?? '');
                $supplier->save();

                // Log success
                AppLog::info('Supplier invoice created successfully', loggable: $invoice);

                return $invoice;
            });
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to create supplier invoice: ', $e->getMessage());
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
            AppLog::error('Failed to update supplier invoice note: ', $e->getMessage());
            return false;
        }
    }

    public function updatePaymentDue($paymentDue)
    {
        /** @var User */
        $loggedInUser = Auth::user();
        if ($loggedInUser && !$loggedInUser->can('updatePaymentDue', $this)) {
            return false;
        }

        try {
            $this->update(['payment_due' => $paymentDue]);

            AppLog::info('Supplier invoice payment due updated.', loggable: $this);

            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to update supplier invoice payment due: ', $e->getMessage());
            return false;
        }
    }

    public function updateExtraFee($description, $amount)
    {
        try {
            $oldAmount = $this->extra_fee_amount;
            $this->update([
                'extra_fee_description' => $description,
                'extra_fee_amount' => $amount,
            ]);

            $supplier = $this->supplier;
            $supplier->updateBalance($amount - $oldAmount, 'Extra fee updated for invoice #' . $this->code ?? '');
            $supplier->save();

            $this->refreshTotals();

            AppLog::info('invoice extra fee updated.', loggable: $this);

            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to update invoice extra fee: ', $e->getMessage(), loggable: $this);
            return false;
        }
    }

    public function removeExtraFee()
    {
        try {
            $oldAmount = $this->extra_fee_amount;
            $this->update([
                'extra_fee_description' => null,
                'extra_fee_amount' => 0,
            ]);

            $supplier = $this->supplier;
            $supplier->updateBalance(-$oldAmount, 'Extra fee removed from invoice #' . $this->code ?? '');
            $supplier->save();

            $this->refreshTotals();

            AppLog::info('Invoice extra fee removed.', loggable: $this);

            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to remove invoice extra fee: ', $e->getMessage(), loggable: $this);
            return false;
        }
    }

    public function addRawMaterial($rawMaterialId, $quantity, $updateSupplierMaterials = false)
    {
        try {
            return DB::transaction(function () use ($rawMaterialId, $quantity, $updateSupplierMaterials) {
                $invoiceRawMaterial = InvoiceRawMaterial::where('supplier_invoice_id', $this->id)->where('raw_material_id', $rawMaterialId)->first();

                $price = SupplierRawMaterial::where('supplier_id', $this->id)->where('raw_material_id', $rawMaterialId)->first()->price;

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

                RawMaterial::find($rawMaterialId)->inventory->addTransaction($quantity, 'Added From Invoice' . ($this->code ? ' #' . $this->code : ''));

                if ($updateSupplierMaterials) {
                    SupplierRawMaterial::updateOrCreate(
                        [
                            'supplier_id' => $this->supplier_id,
                            'raw_material_id' => $rawMaterialId,
                        ],
                        ['price' => $price],
                    );
                }

                $totalBefore = $this->total_amount;
                $this->refreshTotals();
                $totalAfter = $this->total_amount;

                $supplier = $this->supplier;
                $supplier->updateBalance($totalAfter - $totalBefore, 'Raw materials added to invoice #' . $this->code ?? '');
                $supplier->save();

                AppLog::info('Raw material added to supplier invoice successfully', loggable: $this);

                return true;
            });
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to add raw material to supplier invoice: ', $e->getMessage());
            return false;
        }
    }

    public function returnAllQuantityOfRawMaterial($rawMaterialId)
    {
        try {
            return DB::transaction(function () use ($rawMaterialId) {
                $invoiceRawMaterial = InvoiceRawMaterial::where('supplier_invoice_id', $this->id)->where('raw_material_id', $rawMaterialId)->first();

                if (!$invoiceRawMaterial) {
                    throw new Exception('No raw material found for the given ID.');
                }

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
            AppLog::error('Failed to return all quantity of raw material from supplier invoice: ', $e->getMessage());
            return false;
        }
    }

    public function returnRawMaterial($rawMaterialId, $quantity)
    {
        try {
            return DB::transaction(function () use ($rawMaterialId, $quantity) {
                $invoiceRawMaterial = InvoiceRawMaterial::where('supplier_invoice_id', $this->id)->where('raw_material_id', $rawMaterialId)->firstOrFail();

                if ($invoiceRawMaterial->quantity < $quantity) {
                    throw new Exception('Quantity to remove exceeds the available quantity.');
                }

                $invoiceRawMaterial->quantity -= $quantity;
                if ($invoiceRawMaterial->quantity == 0) {
                    $invoiceRawMaterial->delete();
                } else {
                    $invoiceRawMaterial->save();
                }

                RawMaterial::find($rawMaterialId)->inventory->removeQuantity($quantity, 'Removed From Invoice' . ($this->code ? ' #' . $this->code : ''));

                $totalBefore = $this->total_amount;
                $this->refreshTotals();
                $totalAfter = $this->total_amount;

                $supplier = $this->supplier;
                $supplier->updateBalance(-($totalBefore - $totalAfter), 'Raw materials returned from invoice #' . $this->code ?? '');
                $supplier->save();

                $this->save();

                AppLog::info('Raw material removed from supplier invoice successfully', loggable: $this);

                return true;
            });
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to remove raw material from supplier invoice: ', $e->getMessage(), loggable: $this);
            return false;
        }
    }

    public function createPayment($amount, $paymentMethod, $paymentDate)
    {
        return DB::transaction(function () use ($amount, $paymentMethod, $paymentDate) {
            try {
                /** @var User */
                $loggedInUser = Auth::user();
                if ($loggedInUser && !$loggedInUser->can('pay', $this)) {
                    return false;
                }

                // Check if supplier exists for the invoice
                $supplier = $this->supplier;
                if (!$supplier) {
                    throw new Exception('Invoice does not have an associated supplier.');
                }

                if ($this->remaining_to_pay < $amount) {
                    throw new Exception('Payment amount exceeds the remaining amount to be paid.');
                }

                // Step 1: Adjust supplier balance
                $supplier->balance -= $amount;
                $supplier->save();

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

                // Step 3: create a balance transaction log
                $this->supplier->transactions()->create([
                    'payment_id' => $payment->id,
                    'amount' => -$amount,
                    'balance' => $supplier->balance,
                    'description' => 'Payment to supplier',
                    'created_by' => $loggedInUser->id,
                ]);

                // Step 4: Check if the payment amount matches the invoice total and mark as paid if so
                if ($this->remaining_to_pay == $amount) {
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

                $totalAmount += $this->extra_fee_amount;

                $this->total_items = $totalItems;
                $this->total_amount = $totalAmount;

                $this->save();
                return true;
            });
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to refresh supplier invoice totals: ', $e->getMessage());
            return false;
        }
    }

    public function getRemainingToPayAttribute()
    {
        $this->loadMissing(['payments']);
        $totalPayments = $this->payments->sum('amount');

        $remainingAmount = max(0, $this->total_amount + $totalPayments);

        return $remainingAmount > 0 ? $remainingAmount : 0;
    }

    public function getTotalPaidAttribute()
    {
        $this->loadMissing('payments');
        return abs($this->payments->sum('amount'));
    }

    public function isPartlyPaid()
    {
        $hasPayments = $this->payments()->exists();
        $hasBalanceTransactions = $this->balanceTransactions()->exists();

        return ($hasPayments || $hasBalanceTransactions) && ($this->remaining_to_pay > 0 && $this->remaining_to_pay < $this->total_amount);
    }

    private static function generateInvoiceSerial($entryDate)
    {
        $entryDate = new \DateTime($entryDate);
        $datePart = $entryDate->format('Ymd');
        $lastInvoice = self::whereYear('entry_date', $entryDate->format('Y'))->whereMonth('entry_date', $entryDate->format('m'))->orderBy('entry_date', 'desc')->first();

        $lastSerial = $lastInvoice ? (int) substr($lastInvoice->serial, -4) : 0;

        $serialPart = str_pad($lastSerial + 1, 4, '0', STR_PAD_LEFT);
        return $datePart . '-' . $serialPart;
    }

    public function scopeSearch($query, $term, $supplierId = null, $dueDateFrom = null, $dueDateTo = null, $isPaid = null, $entryDateFrom = null, $entryDateTo = null)
    {
        return $query
            ->where(function ($q) use ($term) {
                $q->where('code', 'like', '%' . $term . '%')
                    ->orWhere('title', 'like', '%' . $term . '%')
                    ->orWhere('note', 'like', '%' . $term . '%')
                    ->orWhere('total_items', 'like', '%' . $term . '%')
                    ->orWhere('total_amount', 'like', '%' . $term . '%')
                    ->orWhere('payment_due', 'like', '%' . $term . '%')
                    ->orWhereHas('supplier', function ($q) use ($term) {
                        $q->where('name', 'like', '%' . $term . '%');
                    })
                    ->orWhereHas('rawMaterials', function ($q) use ($term) {
                        $q->where('name', 'like', '%' . $term . '%');
                    });
            })
            ->when($supplierId, function ($query, $supplierId) {
                $query->where('supplier_id', $supplierId);
            })
            ->when($dueDateFrom, function ($query, $dueDateFrom) {
                $query->where('payment_due', '>=', $dueDateFrom);
            })
            ->when($dueDateTo, function ($query, $dueDateTo) {
                $query->where('payment_due', '<=', $dueDateTo);
            })
            ->when($isPaid !== null, function ($query) use ($isPaid) {
                $query->where('is_paid', $isPaid);
            })
            ->when($entryDateFrom, function ($query, $entryDateFrom) {
                $query->where('entry_date', '>=', $entryDateFrom);
            })
            ->when($entryDateTo, function ($query, $entryDateTo) {
                $query->where('entry_date', '<=', $entryDateTo);
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
