<?php

namespace App\Models\Customers;

use App\Models\Pets\Pet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Exception;
use App\Models\Users\AppLog;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'phone', 'location_url', 'zone_id'];

    // Create a new customer
    public static function newCustomer($name, $address = null, $phone, $location_url = null, $zone_id = null)
    {
        try {
            $customer = new self();
            $customer->name = $name;
            $customer->address = $address;
            $customer->phone = $phone;
            $customer->location_url = $location_url;
            $customer->zone_id = $zone_id;

            if ($customer->save()) {
                AppLog::info('Customer created', "Customer $name created successfully.");
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            AppLog::error('Customer creation failed', $e->getMessage());
            report($e);
            return false;
        }
    }

    // Edit customer info
    public function editInfo($name, $address, $phone, $location_url, $zone_id)
    {
        try {
            $this->name = $name;
            $this->address = $address;
            $this->phone = $phone;
            $this->location_url = $location_url;
            $this->zone_id = $zone_id;

            if ($this->save()) {
                AppLog::info('Customer updated', "Customer $name updated successfully.");
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            AppLog::error('Updating customer failed', $e->getMessage());
            report($e);
            return false;
        }
    }

    // Method to add a new pet for this customer
    public function addPet($name, $type, $bdate)
    {
        try {
            $pet = new Pet();
            $pet->name = $name;
            $pet->type = $type;
            $pet->bdate = $bdate;

            // Associate the pet with the current customer
            if ($this->pets()->save($pet)) {
                AppLog::info('Pet added', "Pet $name added for customer {$this->name}.");
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            AppLog::error('Adding pet failed', $e->getMessage());
            report($e);
            return false;
        }
    }

    // Delete customer and optionally associated pets
    public function deleteCustomer($deletePets = false): bool
    {
        try {
            // Optionally delete associated pets
            if ($deletePets) {
                $this->pets()->delete(); // Deletes all pets related to this customer
            }

            if ($this->delete()) {
                AppLog::info('Customer deleted', "Customer {$this->name} deleted successfully.");
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            AppLog::error('Deleting customer failed', $e->getMessage());
            report($e);
            return false;
        }
    }

    public function countPets(): int
    {
        return $this->pets()->count();
    }

    // Scopes
    public function scopeSearch($query, $term)
    {
        $term = "%{$term}%";
        return $query->where('name', 'like', $term)->orWhere('address', 'like', $term)->orWhere('phone', 'like', $term);
    }

    // Relations
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function pets()
    {
        return $this->hasMany(Pet::class);
    }
}
