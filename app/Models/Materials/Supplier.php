<?php

namespace App\Models\Materials;

use App\Models\Payments\BalanceTransaction;
use App\Models\Payments\CustomerPayment;
use App\Models\Users\AppLog;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Supplier extends Model
{
    use HasFactory;

    const MORPH_TYPE = 'supplier';

    protected $fillable = ['name', 'phone1', 'phone2', 'email', 'address', 'contact_name', 'contact_phone', 'balance'];

    public static function newSupplier($name, $phone1, $phone2 = null, $email = null, $address = null, $contact_name = null, $contact_phone = null)
    {
        try {
            $supplier = new self();
            $supplier->name = $name;
            $supplier->phone1 = $phone1;
            $supplier->phone2 = $phone2;
            $supplier->email = $email;
            $supplier->address = $address;
            $supplier->contact_name = $contact_name;
            $supplier->contact_phone = $contact_phone;

            if ($supplier->save()) {
                AppLog::info('Supplier created', "Supplier $name created successfully.", loggable: $supplier);
                return $supplier;
            } else {
                return false;
            }
        } catch (Exception $e) {
            AppLog::error('Supplier creation failed', $e->getMessage());
            report($e);
            return false;
        }
    }

    public function editInfo($name, $phone1, $phone2 = null, $email = null, $address = null, $contact_name = null, $contact_phone = null)
    {
        try {
            $this->name = $name;
            $this->phone1 = $phone1;
            $this->phone2 = $phone2;
            $this->email = $email;
            $this->address = $address;
            $this->contact_name = $contact_name;
            $this->contact_phone = $contact_phone;

            if ($this->save()) {
                AppLog::info('Supplier updated', "Supplier $this->name updated successfully.", loggable: $this);
                return $this;
            } else {
                return false;
            }
        } catch (Exception $e) {
            AppLog::error('Supplier update failed', $e->getMessage());
            report($e);
            return false;
        }
    }

    public function updateBalance($amount, $note = 'Balance update')
    {
        try {
            DB::transaction(function () use ($amount, $note) {
                /** @var User */
                $loggedInUser = Auth::user();
                if ($loggedInUser && !$loggedInUser->can('updateSupplierBalance', $this)) {
                    return false;
                }

                if ($amount == 0) {
                    throw new Exception('Amount must not be zero.');
                }

                $this->balance += $amount;
                $this->save();

                $description = $amount > 0 ? 'Add to balance' : 'Deduct from balance';

                $this->transactions()->create([
                    'amount' => $amount,
                    'balance' => $this->balance,
                    'description' => $note ?? $description,
                    'created_by' => $loggedInUser->id,
                ]);

                AppLog::info("Updated {$this->name}'s balance by {$amount}", loggable: $this);
            });
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to update balance', $e->getMessage(), loggable: $this);
            return false;
        }
    }

    // for supplier payments outside invoice
    public function deductBalanceWithPayment($amount, $paymentMethod, $paymentDate, $note = 'Balance update')
    {
        try {
            DB::transaction(function () use ($amount, $paymentMethod, $paymentDate, $note) {
                /** @var User */
                $loggedInUser = Auth::user();
                if ($loggedInUser && !$loggedInUser->can('updateSupplierBalance', $this)) {
                    return false;
                }

                if ($amount <= 0) {
                    throw new Exception('Amount to be added must be positive.');
                }

                $this->balance += $amount;
                $this->save();

                $new_type_balance = CustomerPayment::calculateNewBalance($amount, $paymentMethod);

                $payment = $this->payments()->create([
                    'amount' => $amount,
                    'payment_method' => $paymentMethod,
                    'type_balance' => $new_type_balance,
                    'payment_date' => $paymentDate,
                    'note' => $note ?? 'Add to balance',
                    'created_by' => $loggedInUser->id,
                ]);

                $this->transactions()->create([
                    'amount' => $amount,
                    'balance' => $this->balance,
                    'description' => $note ?? 'Add to balance',
                    'created_by' => $loggedInUser->id,
                ]);

                AppLog::info("Added {$amount} to {$this->name}'s balance and created payment", loggable: $this);
            });
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to add to balance and create payment', $e->getMessage(), loggable: $this);
            return false;
        }
    }

    public function addRawMaterial($rawMaterialId, $price, $expirationDate)
    {
        try {
            DB::transaction(function () use ($rawMaterialId, $price, $expirationDate) {
                if ($this->rawMaterials()->wherePivot('raw_material_id', $rawMaterialId)->exists()) {
                    throw new Exception('This raw material is already assigned to this supplier.');
                }
                $this->rawMaterials()->attach($rawMaterialId, [
                    'price' => $price,
                    'expiration_date' => $expirationDate,
                ]);

                AppLog::info("Added raw material (ID: $rawMaterialId) to supplier {$this->name}", loggable: $this);
            });
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to add raw material to supplier', $e->getMessage(), loggable: $this);
            return false;
        }
    }

    public function scopeSearch($query, $term)
    {
        $term = "%{$term}%";
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', $term)->orWhere('address', 'like', $term)->orWhere('phone1', 'like', $term)->orWhere('phone2', 'like', $term)->orWhere('contact_name', 'like', $term)->orWhere('contact_phone', 'like', $term);
        });
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(BalanceTransaction::class, 'transactionable');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(CustomerPayment::class);
    }

    public function supplierInvoices()
    {
        return $this->hasMany(SupplierInvoice::class);
    }

    public function avialableRawMaterials()
    {
        return $this->rawMaterials()->wherePivot('expiration_date', '>', now());
    }

    public function rawMaterials()
    {
        return $this->belongsToMany(RawMaterial::class, 'supplier_raw_materials')->withPivot('price','expiration_date');
    }
}
