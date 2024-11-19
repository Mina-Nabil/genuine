<?php

namespace App\Models\Orders;

use App\Models\Customers\Customer;
use App\Models\Customers\Zone;
use App\Models\Users\AppLog;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PeriodicOrder extends Model
{
    use HasFactory, SoftDeletes;

    // Define constants for periodic options
    const PERIODIC_OPTIONS = [self::PERIODIC_WEEKLY, self::PERIODIC_BI_WEEKLY, self::PERIODIC_MONTHLY];
    const PERIODIC_WEEKLY = 'weekly';
    const PERIODIC_BI_WEEKLY = 'bi-weekly';
    const PERIODIC_MONTHLY = 'monthly';

    const daysOfWeek = [
        'Sunday', //1
        'Monday', //2
        'Tuesday', //3
        'Wednesday', //4
        'Thursday', //5
        'Friday', //6
        'Saturday', //7
    ];

    protected $fillable = ['customer_id', 'customer_name', 'shipping_address', 'location_url', 'customer_phone', 'zone_id', 'order_name', 'periodic_option', 'order_day', 'last_order_id', 'note', 'is_active'];

    public function getIsActiveAttribute($value): bool
    {
        return (bool) $value;
    }

    // Determine the next order creation date based on frequency and last order
    public function calculateNextOrderDate(): ?\Carbon\Carbon
    {
        $lastOrderDate = optional($this->lastOrder)->created_at;

        if (!$lastOrderDate) {
            return null;
        }

        switch ($this->periodic_option) {
            case 'weekly':
                return $lastOrderDate->addWeek();
            case 'bi-weekly':
                return $lastOrderDate->addWeeks(2);
            case 'monthly':
                return $lastOrderDate->addMonth()->day($this->order_day ?? 1);
            default:
                return null;
        }
    }

    // Activate or deactivate the periodic order
    public function toggleActive(bool $status): void
    {
        $this->is_active = $status;
        $this->save();
    }

    // Create a new periodic order instance
    public static function newPeriodicOrder(int $customerId, string $customerName, string $shippingAddress, string $customerPhone, int $zoneId, $locationURL = null, string $periodicOption, ?string $orderName = null, ?int $orderDay = null, ?string $note = null, bool $isActive = true, array $products = []): self|bool
    {
        /** @var User */
        $loggedInUser = Auth::user();
        if ($loggedInUser && !$loggedInUser->can('create', self::class)) {
            return false;
        }

        try {
            $periodicOrder = new self();
            $periodicOrder->customer_id = $customerId;
            $periodicOrder->customer_name = $customerName;
            $periodicOrder->shipping_address = $shippingAddress;
            $periodicOrder->location_url = $locationURL;
            $periodicOrder->customer_phone = $customerPhone;
            $periodicOrder->zone_id = $zoneId;
            $periodicOrder->order_name = $orderName;
            $periodicOrder->periodic_option = $periodicOption;
            $periodicOrder->order_day = $orderDay;
            $periodicOrder->note = $note;
            $periodicOrder->is_active = $isActive;

            $periodicOrder->save();

            foreach ($products as $product) {
                $periodicOrder->products()->create([
                    'product_id' => $product['id'],
                    'combo_id' => $product['combo_id'] ?? null,
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                ]);
            }

            AppLog::info('Periodic Order successfully created', loggable: $periodicOrder);
            return $periodicOrder;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to create new periodic order', $e->getMessage());
            return false;
        }
    }

    /**
     * Scopes.
     */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByFrequency($query, string $frequency)
    {
        return $query->where('periodic_option', $frequency);
    }

    public function scopeWithTotalQuantity(Builder $query)
    {
        $query->withCount([
            'products as total_quantity' => function ($query) {
                $query->select(DB::raw('SUM(quantity)'));
            },
        ]);
    }

    /**
     * Relations.
     */

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function lastOrder()
    {
        return $this->belongsTo(Order::class, 'last_order_id');
    }

    public function products()
    {
        return $this->hasMany(PeriodicOrderProduct::class);
    }
}
