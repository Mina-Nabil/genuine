<?php

namespace App\Models\Users;

use App\Models\Orders\Order;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Driver extends Model
{
    use HasFactory;

    const MORPH_TYPE = 'driver';

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    // Car Types
    const CAR_TYPES = [self::CAR_TYPE_SEDAN, self::CAR_TYPE_SUV, self::CAR_TYPE_PICKUP, self::CAR_TYPE_VAN, self::CAR_TYPE_MOTORCYCLE];
    const CAR_TYPE_SEDAN = 'sedan';
    const CAR_TYPE_SUV = 'suv';
    const CAR_TYPE_PICKUP = 'pickup';
    const CAR_TYPE_VAN = 'van';
    const CAR_TYPE_MOTORCYCLE = 'motorcycle';

    protected $fillable = ['user_id', 'shift_title', 'weight_limit', 'order_quantity_limit', 'start_time', 'end_time', 'car_type', 'car_model', 'is_available'];

    public static function createDriver($shiftTitle, $userId, $start_time, $end_time, $weightLimit = null, $orderQuantityLimit = null, $carType = null, $carModel = null)
    {
        try {
            DB::beginTransaction();

            // Create a new driver record
            $driver = self::create([
                'shift_title' => $shiftTitle,
                'user_id' => $userId,
                'weight_limit' => $weightLimit,
                'order_quantity_limit' => $orderQuantityLimit,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'car_type' => $carType,
                'car_model' => $carModel,
                'is_available' => true,
            ]);

            DB::commit();

            // Log the successful creation
            AppLog::info("Driver Shift created successfully with ID {$driver->id}", loggable: $driver);

            return $driver;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            AppLog::error('Failed to create driver', $e->getMessage());

            return null;
        }
    }

    public function deleteDriver()
    {
        try {
            /** @var User */
            $loggedInUser = Auth::user();
            if ($loggedInUser && !$loggedInUser->can('delete', $this)) {
                return false;
            }

            DB::beginTransaction();

            $this->delete();

            DB::commit();

            AppLog::info("Driver Shift of {$this->user->full_name} deleted successfully", loggable: $this);

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            AppLog::error('Failed to delete driver shift', $e->getMessage(), loggable: $this);

            return false;
        }
    }

    public function updateDriver($shiftTitle, $weightLimit, $start_time, $end_time, $orderQuantityLimit, $carType, $carModel, $isAvailable)
    {
        try {
            DB::beginTransaction();

            $this->shift_title = $shiftTitle;
            $this->weight_limit = $weightLimit;
            $this->order_quantity_limit = $orderQuantityLimit;
            $this->car_type = $carType;
            $this->start_time = $start_time;
            $this->end_time = $end_time;
            $this->car_model = $carModel;
            $this->is_available = $isAvailable;

            $this->save();

            DB::commit();

            AppLog::info("Driver updated successfully with ID {$this->id}", loggable: $this);

            return true;
        } catch (Exception $e) {
            report($e);
            DB::rollBack();

            AppLog::error('Failed to update driver', $e->getMessage());
            return false;
        }
    }

    /**
     * Check if the driver can handle the new order based on weight and quantity limits.
     *
     * @param float $newProductsWeight The weight of the new products in the order.
     * @param Carbon $deliveryDate The date of the delivery.
     * @return bool True if the order can be handled, false otherwise.
     */
    public function checkProductsLimits($newProductsWeight, Carbon $deliveryDate)
    {
        $totalWeight = $this->orders()
            ->join('order_products', 'orders.id', '=', 'order_products.order_id')
            ->join('products', 'order_products.product_id', '=', 'products.id')
            ->selectRaw('SUM(order_products.quantity * products.weight) as total_calc_weight')
            ->whereIn('orders.status', Order::OK_STATUSES)
            ->whereDate('delivery_date', $deliveryDate->format('Y-m-d'))
            ->whereNull('order_products.deleted_at')
            ->whereNull('orders.deleted_at')
            ->first()->total_calc_weight;

        $ordersQuantity = $this->orders()->whereDate('delivery_date', $deliveryDate->format('Y-m-d'))->count();
        Log::info("totalWeight: $totalWeight, newProductsWeight: $newProductsWeight, weight_limit: $this->weight_limit");
        Log::info("ordersQuantity: $ordersQuantity, order_quantity_limit: $this->order_quantity_limit");
        if ($totalWeight + $newProductsWeight > $this->weight_limit) {
            return false;
        }

        if ($ordersQuantity + 1 > $this->order_quantity_limit) {
            return false;
        }

        return true;
    }

    /**
     * Check if the driver can handle the new order based on weight and quantity limits.
     *
     * @param Order $order The order to check.
     * @return bool True if the order can be handled, false otherwise.
     */
    public function checkOrderLimits(Order $order)
    {
        $totalWeight = $this->orders()
            ->join('order_products', 'orders.id', '=', 'order_products.order_id')
            ->join('products', 'order_products.product_id', '=', 'products.id')
            ->selectRaw('SUM(order_products.quantity * products.weight) as total_calc_weight')
            ->whereIn('orders.status', Order::OK_STATUSES)
            ->whereDate('delivery_date', $order->delivery_date)
            ->whereNull('order_products.deleted_at')
            ->whereNull('orders.deleted_at')
            ->first()->total_calc_weight;

        $ordersQuantity = $this->orders()
            ->whereIn('orders.status', Order::OK_STATUSES)
            ->whereNull('orders.deleted_at')
            ->whereDate('delivery_date', $order->delivery_date)->count();

        if ($totalWeight + $order->total_weight > $this->weight_limit) {
            Log::info("totalWeight: $totalWeight, order->total_weight: $order->total_weight, weight_limit: $this->weight_limit");
            return false;
        }

        if ($ordersQuantity + 1 > $this->order_quantity_limit) {
            Log::info("ordersQuantity: $ordersQuantity, order_quantity_limit: $this->order_quantity_limit");
            return false;
        }

        return true;
    }

    public static function getDriverWithMostOrders($date = null, $userId = null)
    {
        $date = $date ?? now()->toDateString();

        $query = Driver::select('drivers.*', DB::raw('COUNT(orders.id) as total_orders'))
            ->leftJoin('orders', 'drivers.id', '=', 'orders.driver_id')
            ->where('orders.is_confirmed', 1)
            ->where(function ($query) use ($date) {
                $query->whereDate('orders.delivery_date', $date)->orWhereNull('orders.delivery_date');
            })
            ->groupBy('drivers.id')
            ->orderByDesc('total_orders');

        if ($userId !== null) {
            $query->where('drivers.user_id', $userId);
        }

        return $query->first();
    }

    public function countOrders($date = null)
    {
        $date = $date ?? now()->toDateString();

        return $this->orders()->whereDate('delivery_date', $date)->count();
    }

    public function scopeHasOrdersOn($query, array $on = [])
    {
        $query
            ->select('drivers.*')
            ->join('orders', 'orders.driver_id', '=', 'drivers.id')
            ->when(count($on), function ($q) use ($on) {
                foreach ($on as $date) {
                    $q->orWhereDate('orders.delivery_date', '=', $date->format('Y-m-d'));
                }
            })
            ->groupBy('drivers.id');
    }

    public function scopeSearch($query, $searchTerm = null)
    {
        if (!is_null($searchTerm)) {
            return $query->where(function ($query) use ($searchTerm) {
                $query
                    ->where('weight_limit', 'like', '%' . $searchTerm . '%')
                    ->orWhere('order_quantity_limit', 'like', '%' . $searchTerm . '%')
                    ->orWhere('car_type', 'like', '%' . $searchTerm . '%')
                    ->orWhere('car_model', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('user', function ($query) use ($searchTerm) {
                        $query
                            ->where('username', 'like', '%' . $searchTerm . '%')
                            ->orWhere('first_name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('type', 'like', '%' . $searchTerm . '%')
                            ->orWhere('phone', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        return $query;
    }

    public function scopebyUserID($query, $user_id)
    {
        return $query->where('drivers.user_id', $user_id);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
