<?php

namespace App\Models\Customers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Exception;
use App\Models\Users\AppLog;

class Zone extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'delivery_rate'];

    // Create a new zone
    public static function newZone($name, $delivery_rate)
    {
        try {
            $zone = new self();
            $zone->name = $name;
            $zone->delivery_rate = $delivery_rate;

            if ($zone->save()) {
                AppLog::info('Zone created', "Zone $name created successfully.");
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            AppLog::error('Zone creation failed', $e->getMessage());
            report($e);
            return false;
        }
    }

    // Edit zone info
    public function editInfo($name, $delivery_rate)
    {
        try {
            $this->name = $name;
            $this->delivery_rate = $delivery_rate;

            if ($this->save()) {
                AppLog::info('Zone updated', "Zone $name updated successfully.");
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            AppLog::error('Updating zone failed', $e->getMessage());
            report($e);
            return false;
        }
    }

    // Delete zone and optionally handle customers associated with the zone
    public function deleteZone($reassignCustomersTo = null): bool
    {
        try {
            // Check if there are customers associated with this zone
            if ($this->customers()->count() > 0) {
                if ($reassignCustomersTo) {
                    // Reassign customers to a new zone
                    $this->customers()->update(['zone_id' => $reassignCustomersTo]);
                } else {
                    // If no reassignment zone is provided, prevent deletion
                    throw new Exception("Cannot delete zone {$this->name} as there are customers associated with it.");
                }
            }

            if ($this->delete()) {
                AppLog::info('Zone deleted', "Zone {$this->name} deleted successfully.");
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            AppLog::error('Deleting zone failed', $e->getMessage());
            report($e);
            return false;
        }
    }

    public function updateDeliveryRate($newRate): bool
    {
        try {
            // Update the delivery rate
            $this->delivery_rate = $newRate;

            // Save the changes
            if ($this->save()) {
                AppLog::info('Delivery rate updated successfully', "New delivery rate: $newRate");
                return true;
            } else {
                AppLog::warning('Failed to update delivery rate', "Delivery rate could not be saved for Zone ID: {$this->id}");
                return false;
            }
        } catch (Exception $e) {
            // Log the error
            AppLog::error('Updating delivery rate failed', $e->getMessage());
            report($e);
            return false;
        }
    }

    public function countCustomers(): int
    {
        return $this->customers()->count();
    }

    // Scopes
    public function scopeSearch($query, $term)
    {
        $term = "%{$term}%";
        return $query->where('name', 'like', $term)->orWhere('delivery_rate', 'like', $term);
    }

    // Relations
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}