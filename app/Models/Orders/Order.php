<?php

namespace App\Models\Orders;

use App\Models\Customers\Customer;
use App\Models\Customers\Zone;
use App\Models\Users\AppLog;
use App\Models\Users\Driver;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['order_number', 'customer_id', 'customer_name', 'shipping_address', 'customer_phone', 'status', 'zone_id', 'driver_id', 'periodic_option', 'total_amount', 'delivery_amount', 'discount_amount', 'delivery_date', 'is_paid', 'note', 'created_by'];

    const PERIODIC_OPTIONS = [self::PERIODIC_WEEKLY, self::PERIODIC_BI_WEEKLY, self::PERIODIC_MONTHLY];
    const PERIODIC_WEEKLY = 'weekly';
    const PERIODIC_BI_WEEKLY = 'bi-weekly';
    const PERIODIC_MONTHLY = 'bi-monthly';

    const STATUS_NEW = 'new';
    const STATUS_READY = 'ready';
    const STATUS_IN_DELIVERY = 'in_delivery';
    const STATUS_DONE = 'done';
    const STATUS_RETURNED = 'returned';
    const STATUS_CANCELLED = 'cancelled';
    const STATUSES = [self::STATUS_NEW, self::STATUS_READY, self::STATUS_IN_DELIVERY, self::STATUS_DONE, self::STATUS_RETURNED, self::STATUS_CANCELLED];

    // Function to create a new order
    public static function newOrder(int $customerId, string $customerName, string $shippingAddress, string $customerPhone, int $zoneId, int $driverId = null, string $periodicOption = null, float $totalAmount = 0, float $deliveryAmount = 0, float $discountAmount = 0, Carbon $deliveryDate = null, string $note = null, array $products): Order|bool
    {
        /** @var User */
        $loggedInUser = Auth::user();
        if ($loggedInUser && !$loggedInUser->can('create', self::class)) {
            return false;
        }

        try {
            $order = new self();
            $order->order_number = self::generateNextOrderNumber();
            $order->customer_id = $customerId;
            $order->status = self::STATUS_NEW;
            $order->customer_name = $customerName;
            $order->shipping_address = $shippingAddress;
            $order->customer_phone = $customerPhone;
            $order->zone_id = $zoneId;
            $order->driver_id = $driverId;
            $order->periodic_option = $periodicOption;
            $order->total_amount = $totalAmount;
            $order->delivery_amount = $deliveryAmount;
            $order->discount_amount = $discountAmount;
            $order->delivery_date = $deliveryDate;
            $order->is_paid = false;
            $order->note = $note;
            $order->created_by = $loggedInUser->id;

            $order->save();

            foreach ($products as $product) {
                $order->products()->create([
                    'product_id' => $product['id'],
                    'combo_id' => $product['combo_id'],
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                ]);
            }

            AppLog::info("Order Created by {$loggedInUser->full_name}");
            return $order;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to create new order', $e->getMessage());
            return false;
        }
    }

    private static function generateNextOrderNumber(): string
    {
        $latestOrder = self::orderBy('created_at', 'desc')->first();

        // Determine the next order number based on the latest order
        if ($latestOrder) {
            $latestOrderNumber = (int) $latestOrder->order_number; // Cast to int for increment
            $nextOrderNumber = $latestOrderNumber + 1; // Increment by 1
        } else {
            $nextOrderNumber = 1; // Start from 1 if no orders exist
        }

        return str_pad($nextOrderNumber, 4, '0', STR_PAD_LEFT); // Format as a 6-digit number (e.g., 000001)
    }

    public function scopeWithTotalQuantity(Builder $query)
    {
        $query->withCount([
            'products as total_quantity' => function ($query) {
                $query->select(DB::raw('SUM(quantity)'));
            },
        ]);
    }

    // relations
    public function products()
    {
        return $this->hasMany(OrderProduct::class);
    }

    // relations
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    // relations
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    // relations
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
