<?php

namespace App\Models\Pets;

use App\Models\Customers\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Exception;
use App\Models\Users\AppLog;
use DateInterval;
use DateTime;
use InvalidArgumentException;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category', 'type', 'bdate', 'customer_id'];

    const CATEGORY_DOG = 'dog';
    const CATEGORY_CAT = 'cat';
    const CATEGORIES = [self::CATEGORY_DOG, self::CATEGORY_CAT];

    // Create a new pet
    public static function newPet($name, $category, $type, $bdate, $customer_id, $note = null)
    {
        try {
            $pet = new self();
            $pet->name = $name;
            $pet->category = $category;
            $pet->type = $type;
            $pet->bdate = $bdate;
            $pet->note = $note;
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

    public static function calculateBirthDate(int $years, int $months, int $days)
    {
        // Validate input
        if (!is_int($years) || !is_int($months) || !is_int($days)) {
            throw new InvalidArgumentException('All inputs must be integers.');
        }

        // Get the current date
        $currentDate = new DateTime();

        // Subtract the years, months, and days from the current date
        $currentDate->sub(new DateInterval("P{$years}Y{$months}M{$days}D"));

        // Return the calculated birth date
        return $currentDate->format('Y-m-d');
    }

    // Edit pet info
    public function editInfo($name, $category, $type, $bdate, $customer_id, $note = null)
    {
        try {
            $this->name = $name;
            $this->category = $category;
            $this->type = $type;
            $this->bdate = $bdate;
            $this->customer_id = $customer_id;
            $this->note = $note;

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

    public function unassignFromCustomer(): bool
    {
        try {
            // Set the customer_id to null
            $this->customer_id = null;

            // Save the changes
            if ($this->save()) {
                AppLog::info('Pet unassigned', "Pet {$this->name} unassigned from customer successfully.");
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            // Log and handle the exception
            AppLog::error('Unassigning pet failed', $e->getMessage());
            report($e);
            return false;
        }
    }

    public static function reassignToCustomer(array $petIds, $customerId, $updatedBy = null): bool
    {
        try {
            // Validate the new customer ID
            if (!Customer::find($customerId)) {
                throw new Exception("Customer with ID $customerId does not exist.");
            }

            // Fetch all pets using the array of pet IDs
            $pets = Pet::whereIn('id', $petIds)->get();

            if ($pets->isEmpty()) {
                throw new Exception('No valid pets found for the given IDs.');
            }

            // Loop through each pet and reassign it to the customer
            foreach ($pets as $pet) {
                $pet->customer_id = $customerId;

                // Save the changes
                if (!$pet->save()) {
                    throw new Exception("Failed to reassign pet ID {$pet->id}.");
                }

                // Log the reassignment
                $logMessage = "Pet '{$pet->name}' (ID: {$pet->id}) reassigned to Customer ID: $customerId";
                if ($updatedBy) {
                    $logMessage .= " | Updated by: $updatedBy";
                }

                AppLog::info($logMessage);
            }

            return true;
        } catch (Exception $e) {
            // Log the error and report the exception
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

    public static function getDistinctPetTypes($category)
    {
        return self::where('category', $category)->select('type')->distinct()->get()->pluck('type');
    }

    // Relations
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
