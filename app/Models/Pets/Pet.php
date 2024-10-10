<?php

namespace App\Models\Pets;

use App\Models\Customers\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Exception;
use App\Models\Users\AppLog;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'bdate', 'customer_id'];

    const TYPE_DOG = 'dog';
    const TYPE_CAT = 'cat';

    const TYPES = [self::TYPE_DOG, self::TYPE_CAT];

    // Create a new pet
    public static function newPet($name, $type, $bdate, $customer_id)
    {
        try {
            $pet = new self();
            $pet->name = $name;
            $pet->type = $type;
            $pet->bdate = $bdate;
            $pet->customer_id = $customer_id;

            if ($pet->save()) {
                AppLog::info('Pet created', "Pet $name created successfully.");
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            AppLog::error('Pet creation failed', $e->getMessage());
            report($e);
            return false;
        }
    }

    // Edit pet info
    public function editInfo($name, $type, $bdate, $customer_id)
    {
        try {
            $this->name = $name;
            $this->type = $type;
            $this->bdate = $bdate;
            $this->customer_id = $customer_id;

            if ($this->save()) {
                AppLog::info('Pet updated', "Pet $name updated successfully.");
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            AppLog::error('Updating pet failed', $e->getMessage());
            report($e);
            return false;
        }
    }

    // Delete pet
    public function deletePet(): bool
    {
        try {
            if ($this->delete()) {
                AppLog::info('Pet deleted', "Pet {$this->name} deleted successfully.");
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            AppLog::error('Deleting pet failed', $e->getMessage());
            report($e);
            return false;
        }
    }

    public function reassignToCustomer($customerId, $reason = null, $notifyCustomer = false, $updatedBy = null): bool
    {
        try {
            // Validate the new customer ID
            if (!Customer::find($customerId)) {
                throw new Exception("Customer with ID $customerId does not exist.");
            }

            // Assign the pet to the new customer
            $this->customer_id = $customerId;

            // Save the changes
            if ($this->save()) {
                // Optionally notify the customer
                if ($notifyCustomer) {
                    // Here you can add a method to send notifications to the customer
                    // $this->notifyCustomerAboutReassignment($customerId);
                }

                // Log the reassignment with a reason and the person who made the change
                $logMessage = "Pet '{$this->name}' reassigned to Customer ID: $customerId";
                if ($reason) {
                    $logMessage .= " | Reason: $reason";
                }
                if ($updatedBy) {
                    $logMessage .= " | Updated by: $updatedBy";
                }

                AppLog::info($logMessage, 'Pet reassigned successfully.');

                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            // Log the error
            AppLog::error('Pet reassignment failed', $e->getMessage());
            report($e);
            return false;
        }
    }

    public function getAge(): int
    {
        return \Carbon\Carbon::parse($this->bdate)->age;
    }

    public static function findByType($type)
    {
        return self::where('type', $type)->get();
    }

    public function scopeSearch($query, $term)
    {
        $term = "%{$term}%";
        return $query
            ->where('name', 'like', $term)
            ->orWhere('type', 'like', $term)
            ->orWhereHas('customer', function ($q) use ($term) {
                $q->where('name', 'like', $term);
            });
    }

    // Relations
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
