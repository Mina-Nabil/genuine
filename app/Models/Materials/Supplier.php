<?php

namespace App\Models\Materials;

use App\Models\Users\AppLog;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone1', 'phone2', 'email', 'address', 'contact_name', 'contact_phone', 'balance'];

    public static function newSupplier($name, $phone1, $phone2 = null, $email = null, $address = null, $contact_name = null, $contact_phone = null, $balance = 0)
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
            $supplier->balance = $balance;

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
            $this->phone2 = $phone2 ; 
            $this->email = $email ; 
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

    public function supplierInvoices()
    {
        return $this->hasMany(SupplierInvoice::class);
    }

    public function rawMaterials()
    {
        return $this->belongsToMany(RawMaterial::class, 'supplier_raw_materials')
            ->withPivot('price');
    }
}
