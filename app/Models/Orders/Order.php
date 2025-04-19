<?php

namespace App\Models\Orders;

use App\Models\Customers\Customer;
use App\Models\Customers\Zone;
use App\Models\Payments\BalanceTransaction;
use App\Models\Payments\CustomerPayment;
use App\Models\Products\Product;
use App\Models\Users\AppLog;
use App\Models\Users\Driver;
use App\Models\Users\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Locale;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    const MORPH_TYPE = 'order';
    const currency = 'EGP';

    const DRIVER_LIMITS_EXCEEDED_CODE = 321;

    protected $casts = [
        'delivery_date' => 'date',
    ];

    protected $fillable = ['order_number', 'customer_id', 'customer_name', 'shipping_address', 'location_url', 'customer_phone', 'status', 'zone_id', 'driver_id', 'periodic_option', 'total_amount', 'delivery_amount', 'discount_amount', 'delivery_date', 'is_paid', 'is_confirmed', 'note', 'driver_note', 'created_by', 'is_delivered', 'driver_payment_type', 'driver_order', 'is_debit'];

    const PERIODIC_OPTIONS = [self::PERIODIC_WEEKLY, self::PERIODIC_BI_WEEKLY, self::PERIODIC_MONTHLY];
    const PERIODIC_WEEKLY = 'weekly';
    const PERIODIC_BI_WEEKLY = 'bi-weekly';
    const PERIODIC_MONTHLY = 'monthly';

    const STATUS_NEW = 'new';
    const STATUS_READY = 'ready';
    const STATUS_IN_DELIVERY = 'in_delivery';
    const STATUS_DONE = 'done';
    const STATUS_RETURNED = 'returned';
    const STATUS_CANCELLED = 'cancelled';
    const OK_STATUSES = [self::STATUS_NEW, self::STATUS_READY, self::STATUS_IN_DELIVERY, self::STATUS_DONE];

    const STATUSES = [self::STATUS_NEW, self::STATUS_READY, self::STATUS_IN_DELIVERY, self::STATUS_DONE, self::STATUS_RETURNED, self::STATUS_CANCELLED];

    public static function getNextStatuses(string $currentStatus): array
    {
        $statusLevels = [
            self::STATUS_NEW => [self::STATUS_READY, self::STATUS_CANCELLED], // Level 1
            self::STATUS_READY => [self::STATUS_IN_DELIVERY, self::STATUS_CANCELLED], // Level 2
            self::STATUS_IN_DELIVERY => [self::STATUS_DONE, self::STATUS_RETURNED], // Level 3
            self::STATUS_DONE => [], // Level 4 Final status with no further transitions
            self::STATUS_RETURNED => [],
            self::STATUS_CANCELLED => [],
        ];

        // Return the next possible statuses or an empty array if no transitions exist
        return $statusLevels[$currentStatus] ?? [];
    }

    /**
     * Set a bulk status on multiple orders.
     *
     * @param array $orderIds Array of order IDs.
     * @param string $newStatus The new status to set.
     * @return bool True if all statuses were updated successfully, false otherwise.
     * @throws Exception
     */
    public static function setBulkStatus(array $orderIds, string $newStatus): bool
    {
        DB::beginTransaction();

        try {
            $orders = self::whereIn('id', $orderIds)->get();

            foreach ($orders as $order) {
                // Get the current status and check the allowed next statuses
                $order->setStatus($newStatus);
            }
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            AppLog::error('Failed to changes bulk status for orders', $e->getMessage());
            return false;
        }
    }

    public static function setBulkConfirmed(array $orderIds, bool $isConfirmed): bool
    {
        DB::beginTransaction();

        try {
            // Fetch the orders by the given IDs
            $orders = self::whereIn('id', $orderIds)->get();

            foreach ($orders as $order) {
                /** @var User */
                $loggedInUser = Auth::user();

                // Check if the user has permission to update the order
                if (!$loggedInUser || !$loggedInUser->can('updateConfirm', $order)) {
                    throw new Exception("Unauthorized to confirm Order ID {$order->id}");
                }

                // Update the `is_confirmed` status
                $order->is_confirmed = $isConfirmed;
                $order->save();
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            AppLog::error('Failed to set bulk confirmation for orders', $e->getMessage());
            return false;
        }
    }

    public static function setBulkDebit(array $orderIds): bool
    {
        DB::beginTransaction();

        try {
            // Fetch the orders by the given IDs
            $orders = self::whereIn('id', $orderIds)->get();

            foreach ($orders as $order) {
                /** @var User */
                $loggedInUser = Auth::user();

                // Check if the user has permission to update the order
                if (!$loggedInUser || !$loggedInUser->can('update', $order)) {
                    throw new Exception("Unauthorized to update Order ID {$order->id}");
                }

                // Update the `is_confirmed` status
                $order->is_debit = 1;
                $order->save();
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            AppLog::error('Failed to set bulk debit for orders', $e->getMessage());
            return false;
        }
    }

    public function resetStatus(): bool
    {
        /** @var User */
        $user = Auth::user();
        if (!$user || !$user->can('resetStatus', $this)) {
            return false;
        }

        DB::beginTransaction();

        try {
            if ($this->status === self::STATUS_READY) {
                foreach ($this->products as $product) {
                    $product->product->inventory->unfulfillCommit($product->quantity);
                    $product->is_ready = false;
                    $product->save();
                    AppLog::info("Order product {$product->product->name} is back to stock", loggable: $this);
                }

                $this->status = self::STATUS_NEW;
                AppLog::info('Order reset completed', loggable: $this);
            } elseif ($this->status === self::STATUS_DONE) {
                $this->debitDriverPerOrder();
                $this->status = self::STATUS_IN_DELIVERY;
                AppLog::info('Order reset from done to in delivery', loggable: $this);
            } else {
                DB::rollBack();
                return false;
            }

            $this->save();
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            AppLog::error("Failed to reset status for Order ID {$this->id}", $e->getMessage());
            return false;
        }
    }


    public static function resetBulkStatus(array $orderIds): bool
    {
        DB::beginTransaction();

        try {
            $orders = self::whereIn('id', $orderIds)->get();

            foreach ($orders as $order) {
                $order->resetStatus();
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            AppLog::error('Failed to reset bulk status for orders', $e->getMessage());
            return false;
        }
    }

    public function setStatus(string $newStatus, $skipCheck = false): bool
    {
        DB::beginTransaction();

        try {
            /** @var User */
            $loggedInUser = Auth::user();
            if (($newStatus == self::STATUS_READY || $newStatus == self::STATUS_IN_DELIVERY) && !$loggedInUser->can('updateInventoryInfo', self::class)) {
                throw new Exception('User unauthorized');
            }

            // Get the current status and check the allowed next statuses
            $currentStatus = $this->status;
            $allowedNextStatuses = self::getNextStatuses($currentStatus);

            // If the new status is not allowed, throw an exception
            if (!in_array($newStatus, $allowedNextStatuses, true) && !$skipCheck) {
                throw new Exception("Order ID {$this->id} cannot transition from {$currentStatus} to {$newStatus}");
            }

            if ($this->remaining_to_pay != 0 && $newStatus == self::STATUS_DONE && !$skipCheck) {
                throw new Exception("Can't set order to done, pending payment");
            }

            if ($currentStatus === self::STATUS_NEW && $newStatus === self::STATUS_READY) {
                foreach ($this->products as $product) {
                    if (!$product->is_ready) {
                        $product->setAsReady();
                    }
                    $product->is_ready = true;
                    $product->save();
                }
            }

            if ($newStatus === self::STATUS_DONE) {
                $orderInAnotherShift = $this->driverHasOrdersInAnotherShift();
                if ($orderInAnotherShift) {
                    $orderInAnotherShift->creditDriverForReturnedShift();
                }

                $this->calculateStartDeliveryCrDriver();
                $this->creditDriverPerOrder();
            }

            // If the new status is returned or cancelled, handle product cancellation
            if ($newStatus === self::STATUS_RETURNED || $newStatus === self::STATUS_CANCELLED) {
                $this->cancelAllProducts();
            }

            // Update the status
            $this->status = $newStatus;

            if ($this->save()) {
                AppLog::info('Order status changed to ' . $newStatus, loggable: $this);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            AppLog::error("Failed to change status for Order ID {$this->id}", $e->getMessage());
            return false;
        }
    }

    // Function to create a new order
    public static function newOrder(int $customerId, string $customerName, string $shippingAddress, string $customerPhone, int $zoneId, $locationURL = null, ?int $driverId = null, float $totalAmount = 0, float $deliveryAmount = 0, float $discountAmount = 0, ?Carbon $deliveryDate = null, ?string $note = null, array $products, $detuctFromBalance = false, $migrated = false, $creator_id = null): Order|bool
    {
        /** @var User */
        $loggedInUser = Auth::user();
        if (!$migrated && $loggedInUser && !$loggedInUser->can('create', self::class)) {
            return false;
        }

        if ($deliveryDate->isToday()) {
            foreach ($products as $product) {
                $p = Product::findOrFail($product['id']);
                if ($p->inventory->available - $product['quantity'] < 0) {
                    return false;
                }
            }
        }

        if ($driverId && !$loggedInUser->can('overrideDriverLimits', self::class)) {
            $driver = Driver::findOrFail($driverId);
            $totalWeight = 0;
            foreach ($products as $product) {
                $productObject = Product::findOrFail($product['id']);
                $totalWeight += $product['quantity'] * $productObject->weight;
            }
            if (!$driver->checkProductsLimits($totalWeight, $deliveryDate)) {
                return throw new Exception('Driver limits exceeded', self::DRIVER_LIMITS_EXCEEDED_CODE);
            }
        }
        try {
            $order = new self();
            $order->order_number = self::generateNextOrderNumber();
            $order->customer_id = $customerId;
            $order->status = self::STATUS_NEW;
            $order->customer_name = $customerName;
            $order->shipping_address = $shippingAddress;
            $order->location_url = $locationURL;
            $order->customer_phone = $customerPhone;
            $order->zone_id = $zoneId;
            $order->driver_id = $driverId;
            $order->total_amount = round($totalAmount);
            $order->delivery_amount = $deliveryAmount;
            if ($loggedInUser->can('updateDiscount', self::class)) {
                $order->discount_amount = $discountAmount;
            }
            $order->delivery_date = $deliveryDate;
            $order->is_paid = false;
            $order->is_debit = false;
            $order->note = $note;
            $order->created_by = $creator_id ?? $loggedInUser->id;

            $order->save();

            foreach ($products as $product) {
                $orderProduct = $order->products()->create([
                    'product_id' => $product['id'],
                    'combo_id' => $product['combo_id'],
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                ]);
                if (!$migrated) {
                    $orderProduct->product->inventory->commitQuantity($product['quantity'], 'Order: #' . $order->order_number . ' committed');
                }
            }

            if ($detuctFromBalance) {
                $order->setAsPaid(Carbon::now(), deductFromBalance: true);
            }

            AppLog::info('Order Created successfuly', loggable: $order);
            if ($order && $migrated && $deliveryDate) {
                $order->setAsPaid($deliveryDate, CustomerPayment::PYMT_CASH, migrated: $migrated);
                $order->toggleConfirmation();
                $order->toggleIsDelivered();
                $order->setStatus(self::STATUS_DONE, true);
            }
            return $order;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to create new order', $e->getMessage());
            return false;
        }
    }

    public function assignToUser(int $user_id): bool
    {
        try {
            $this->created_by = $user_id;
            $user = User::findOrFail($user_id);
            $this->save();
            AppLog::info('Order Assigned to User ' . $user->full_name, loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to assign order to user', $e->getMessage());
            return false;
        }
    }

    public function assignDriverToOrder(?int $driverId = null): bool
    {
        /** @var User */
        $user = Auth::user();

        if ($driverId && !$user->can('overrideDriverLimits', $this)) {
            $driver = Driver::findOrFail($driverId);
            if (!$driver->checkOrderLimits($this)) {
                return throw new Exception('Driver limits exceeded', self::DRIVER_LIMITS_EXCEEDED_CODE);
            }
        }

        if (!$user->can('updateDeliveryInfo', $this)) {
            return false;
        }

        try {

            $this->driver_id = $driverId;
            if ($this->driver_id !== $driverId) {
                $this->driver_order = null;
            }

            $this->save();
            AppLog::info('Order Assigned to driver', loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to assign order to driver', $e->getMessage());
            return false;
        }
    }

    public function updateLocationUrl(?string $locationUrl = null): bool
    {
        /** @var User */
        $loggedInUser = Auth::user();
        if ($loggedInUser && !$loggedInUser->can('update', $this)) {
            return false;
        }

        try {
            $this->location_url = $locationUrl;

            $this->save();

            AppLog::info('Location URL updated', loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to update location URL for order', $e->getMessage());
            return false;
        }
    }

    public function updateDiscount(?string $discount = null): bool
    {
        /** @var User */
        $loggedInUser = Auth::user();
        if ($loggedInUser && !$loggedInUser->can('updateDiscount', $this)) {
            return false;
        }

        try {
            $this->discount_amount = $discount;
            $this->save();
            $this->refreshTotalAmount();
            AppLog::info('Discount updated to ' . $discount, loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to update discount for order', $e->getMessage());
            return false;
        }
    }

    public function updateCustomer($customer_id): bool
    {
        /** @var User */
        $loggedInUser = Auth::user();
        if ($loggedInUser && !$loggedInUser->can('updateCustomer', $this)) {
            return false;
        }

        $customer = Customer::findOrFail($customer_id);

        try {
            $this->customer_id = $customer->id;
            $this->customer_name = $customer->name;
            $this->shipping_address = $customer->address;
            $this->location_url = $customer->location_url;
            $this->customer_phone = $customer->phone;
            $this->zone_id = $customer->zone->id;
            $this->delivery_amount = $customer->zone->delivery_rate;
            $this->save();
            $this->refreshTotalAmount();
            AppLog::info('Customer updated to ' . $customer->name, loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to update customer for order', $e->getMessage());
            return false;
        }
    }

    public function updateDelivery(?string $delivery = null): bool
    {
        /** @var User */
        $loggedInUser = Auth::user();
        if ($loggedInUser && !$loggedInUser->can('updateDeliveryPrice', $this)) {
            return false;
        }

        try {
            $this->delivery_amount = $delivery;
            $this->save();
            $this->refreshTotalAmount();

            AppLog::info('Delivery updated to ' . $delivery, loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to update delivery for order', $e->getMessage());
            return false;
        }
    }

    /**
     * load يوميه التحميل
     */
    public static function loadDailyLoadingReport(string $startDay, string $endDay)
    {
        $startDay = Carbon::parse($startDay);
        $endDay = Carbon::parse($endDay);

        return DB::table('orders as o1')
            ->select('drivers.id', 'zones.name', 'users.username', 'users.first_name', 'users.last_name')
            ->selectRaw('drivers.shift_title, drivers.user_id')
            ->selectRaw('COUNT(o1.id) as orders_count')
            ->selectRaw('SUM(o1.total_amount) as orders_total')
            ->selectRaw('SUM((SELECT (SUM(order_products.quantity * products.weight/1000)) from order_products join products on order_products.product_id = products.id where o1.id = order_products.order_id and order_products.deleted_at is null )) as kgs_total')
            ->selectRaw('SUM((SELECT SUM(amount) from customer_payments as c2 where o1.id = c2.order_id and c2.payment_method = "' . CustomerPayment::PYMT_CASH . '")) as total_cash ')
            ->selectRaw('SUM((SELECT SUM(amount) from customer_payments as c2 where o1.id = c2.order_id and c2.payment_method = "' . CustomerPayment::PYMT_BANK_TRANSFER . '")) as total_bank ')
            ->selectRaw('SUM((SELECT SUM(amount) from customer_payments as c2 where o1.id = c2.order_id and c2.payment_method = "' . CustomerPayment::PYMT_WALLET . '")) as total_wallet ')
            ->selectRaw('SUM(CASE WHEN o1.is_debit = 1 AND o1.is_paid = 0 THEN o1.total_amount ELSE NULL END) as total_debit ')
            ->leftjoin('drivers', 'drivers.id', '=', 'o1.driver_id')
            ->leftjoin('users', 'users.id', '=', 'drivers.user_id')
            ->join('zones', 'zones.id', '=', 'o1.zone_id')
            ->whereBetween('o1.delivery_date', [$startDay->format('Y-m-d 00:00:00'), $endDay->format('Y-m-d 23:59:59')])
            ->where('o1.is_confirmed', 1)
            ->whereIn('o1.status', Order::OK_STATUSES)
            ->whereNull('o1.deleted_at')
            ->groupBy('zones.id', 'drivers.id')
            ->orderBy('drivers.shift_title')
            ->orderByDesc('orders_total')
            ->get();
    }

    // Static function for bulk assignment
    public static function assignDriverToOrders(array $orderIds, int $driverId): bool
    {
        DB::beginTransaction();

        try {
            foreach ($orderIds as $orderId) {
                $order = self::find($orderId);

                if ($order) {
                    if (!$order->assignDriverToOrder($driverId)) {
                        throw new Exception("Failed to assign driver to order ID: {$orderId}");
                    }
                    if ($order->driver_id !== $driverId) {
                        $order->driver_order = null;
                        $order->save();
                    }
                } else {
                    AppLog::warning('Order not found', ['order_id' => $orderId]);
                }
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            if ($e->getCode() == self::DRIVER_LIMITS_EXCEEDED_CODE) {
                throw $e;
            }
            report($e);
            AppLog::error('Failed to assign orders to driver', $e->getMessage());
            return false;
        }
    }

    /**
     * Bulk set a delivery date for multiple orders.
     *
     * @param array $orderIds Array of order IDs.
     * @param string $deliveryDate Delivery date to set for the orders.
     * @return bool True if successful, false otherwise.
     */
    public static function setDeliveryDateForOrders(array $orderIds, Carbon $deliveryDate): bool
    {
        DB::beginTransaction();
        /** @var User */
        $user = Auth::user();
        try {
            // Update the delivery_date for each order in the array
            foreach ($orderIds as $orderId) {
                $order = self::find($orderId);

                // Proceed if the order exists
                if ($order) {
                    $order->updateDeliveryDate($deliveryDate);
                }
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            if ($e->getCode() == self::DRIVER_LIMITS_EXCEEDED_CODE) {
                throw $e;
            }

            report($e); // Log the exception
            AppLog::error('Failed to set delivery date to orders', $e->getMessage());
            return false;
        }
    }

    public function updateDeliveryDate(?Carbon $deliveryDate = null): bool
    {
        /** @var User */
        $loggedInUser = Auth::user();
        if ($loggedInUser && !$loggedInUser->can('updateDeliveryInfo', $this)) {
            return false;
        }

        if (!$deliveryDate) {
            return false;
        }

        if ($this->driver_id && !$loggedInUser->can('overrideDriverLimits', $this)) {
            $driver = Driver::findOrFail($this->driver_id);
            if (!$driver->checkProductsLimits($this->total_weight, $deliveryDate)) {
                return throw new Exception('Driver limits exceeded', self::DRIVER_LIMITS_EXCEEDED_CODE);
            }
        }

        try {
            $this->delivery_date = $deliveryDate;

            if ($this->delivery_date !== $deliveryDate) {
                $this->driver_order = null;
            }

            $this->save();

            AppLog::info('Delivery date updated', loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to update delivery date for order', $e->getMessage());
            return false;
        }
    }

    public function rescheduleOrder(?Carbon $newDeliveryDate = null, $isDriverReturned = false, $is_2x = false): bool
    {
        /** @var User */
        $loggedInUser = Auth::user();
        if ($loggedInUser && !$loggedInUser->can('rescheduleOrder', $this)) {
            return false;
        }

        try {
            return DB::transaction(function () use ($newDeliveryDate, $isDriverReturned, $is_2x) {
                $oldDeliveryDate = $this->delivery_date?->format('d/m/Y') ?? 'N/A';

                if ($isDriverReturned) {
                    $this->creditDriverForReturnedShift();
                }

                if ($is_2x) {
                    $this->creditDriverPerOrder(true);
                }

                $this->delivery_date = $newDeliveryDate;
                $this->status = self::STATUS_READY;
                $this->save();

                $newDeliveryDateFormatted = $newDeliveryDate?->format('d/m/Y') ?? 'N/A';

                AppLog::info('Order rescheduled', "$oldDeliveryDate → $newDeliveryDateFormatted", loggable: $this);

                return true;
            });
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to reschedule order', $e->getMessage());
            return false;
        }
    }

    public function setAsDebit(): bool
    {
        /** @var User */
        $loggedInUser = Auth::user();
        if ($loggedInUser && !$loggedInUser->can('update', $this)) {
            return false;
        }

        try {

            $this->creditDriverPerOrder();
            $this->is_debit = 1;
            $this->save();

            AppLog::info('Order debit changed to ' . $this->is_debit ? 'Yes' : 'No', loggable: $this);

            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to toggle confirmation for order', $e->getMessage());
            return false;
        }
    }

    public function toggleConfirmation(): bool
    {
        /** @var User */
        $loggedInUser = Auth::user();
        if ($loggedInUser && !$loggedInUser->can('updateConfirm', $this)) {
            return false;
        }

        try {
            $this->is_confirmed = !$this->is_confirmed;
            $this->save();

            AppLog::info('Order confirmation toggled', loggable: $this);

            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to toggle confirmation for order', $e->getMessage());
            return false;
        }
    }

    public function setWhstappMsgAsSent($status = true): bool
    {
        /** @var User */
        $loggedInUser = Auth::user();
        if ($loggedInUser && !$loggedInUser->can('update', $this)) {
            return false;
        }

        try {
            $this->is_whatsapp_sent = $status;
            $this->save();

            AppLog::info('Whatsapp message sent', loggable: $this);

            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to send whatsapp messsage', $e->getMessage());
            return false;
        }
    }

    public static function checkStatusConsistency(array $orderIds)
    {
        $result = DB::table('orders')
            ->selectRaw(
                "
                GROUP_CONCAT(DISTINCT status) AS statuses,
                COUNT(DISTINCT status) AS status_count
            ",
            )
            ->whereIn('id', $orderIds)
            ->first();

        // Check if there is only one unique status
        if ($result->status_count == 1) {
            return $result->statuses; // Return the single status
        } else {
            return false; // Return false if statuses are different
        }
    }

    public static function checkRemainingToPayConsistency(array $orderIds)
    {
        $orders = Order::whereIn('id', $orderIds)->get();

        // Check if all orders have remaining_to_pay greater than zero
        $allHaveRemainingToPay = $orders->every(function ($order) {
            if ($order->isOpenToPay()) {
                return $order->remaining_to_pay > 0;
            }
        });

        return $allHaveRemainingToPay;
    }

    public static function checkInHouseEligibility(array $orderIds)
    {
        $orders = Order::whereIn('id', $orderIds)->get();
        /** @var User */
        $user = Auth::user();
        $allEligible = $orders->every(function ($order) use ($user) {
            return $user->can('updateDeliveryInfo', $order);
        });

        return $allEligible;
    }

    public function createPayment($amount, $paymentMethod, $paymentDate, $isTakeFromBalance = false)
    {
        return DB::transaction(function () use ($amount, $paymentMethod, $paymentDate, $isTakeFromBalance) {
            try {
                /** @var User */
                $loggedInUser = Auth::user();
                if ($loggedInUser && !$loggedInUser->can('pay', $this)) {
                    return false;
                }

                // Check if customer exists for the order
                $customer = $this->customer;
                if (!$customer) {
                    throw new Exception('Order does not have an associated customer.');
                }

                // Step 1: Adjust customer balance if specified
                if ($isTakeFromBalance) {
                    if ($customer->balance < $amount) {
                        throw new Exception('Insufficient balance.');
                    }
                    $customer->balance -= $amount;
                    $customer->save();
                }

                $new_type_balance = CustomerPayment::calculateNewBalance($amount, $paymentMethod);

                // Step 2: Create the payment record
                $payment = CustomerPayment::create([
                    'customer_id' => $customer->id,
                    'order_id' => $this->id,
                    'amount' => $amount,
                    'payment_method' => $paymentMethod,
                    'type_balance' => $new_type_balance,
                    'payment_date' => $paymentDate,
                    'created_by' => $loggedInUser->id,
                ]);

                // Step 3: Conditionally create a balance transaction log
                if ($isTakeFromBalance) {
                    BalanceTransaction::create([
                        'customer_id' => $customer->id,
                        'payment_id' => $payment->id,
                        'order_id' => $this->id,
                        'amount' => $amount,
                        'balance' => $customer->balance,
                        'description' => 'Deducted from balance',
                        'created_by' => $loggedInUser->id,
                    ]);
                }

                // Step 4: Check if the payment amount matches the order total and mark as paid if so
                if ($amount == $this->total_amount) {
                    $this->is_paid = true;
                    $this->save();
                }

                // Logging the successful payment creation
                AppLog::info('Payment created for order', loggable: $this);
                return $payment;
            } catch (QueryException $e) {
                if ($e->getCode() === '40001') {
                    // Deadlock error code in MySQL
                    AppLog::error('Deadlock encountered', loggable: $this);
                }
                // Log the error
                report($e);
                AppLog::error('Failed creating payment for order', $e->getMessage(), loggable: $this);
                return false;
            }
        });
    }

    public function setAsPaidFromBalance()
    {
        return DB::transaction(function () {
            try {
                /** @var User */
                $loggedInUser = Auth::user();
                if ($loggedInUser && !$loggedInUser->can('pay', $this)) {
                    return false;
                }

                // Check if customer exists for the order
                $customer = $this->customer;
                if (!$customer) {
                    throw new Exception('Order does not have an associated customer.');
                }

                // Step 1: Check if customer has sufficient balance and deduct if necessary
                if ($customer->balance >= $this->total_amount) {
                    // Deduct the balance if sufficient
                    $customer->balance -= $this->total_amount;
                    $customer->save();

                    // Step 2: Log the balance transaction (negative deduction)
                    BalanceTransaction::create([
                        'customer_id' => $customer->id,
                        'amount' => -$this->total_amount, // Deducting from the balance
                        'balance' => -$customer->balance,
                        'description' => 'Payment for order #' . $this->id . ' from balance',
                        'created_by' => $loggedInUser->id,
                    ]);

                    // Step 3: Mark order as paid
                    $this->is_paid = true;
                    $this->save();

                    // Step 4: Log the action
                    AppLog::info('Order marked as paid from balance', loggable: $this);

                    return true;
                } else {
                    // Log the message if there's insufficient balance but don't deduct
                    AppLog::warning("Customer {$customer->name} has insufficient balance for order #{$this->id}");
                    return false;
                }
            } catch (QueryException $e) {
                if ($e->getCode() === '40001') {
                    AppLog::error('Deadlock encountered', loggable: $this);
                }
                // Log the error
                report($e);
                AppLog::error('Failed to set order as paid from balance', $e->getMessage(), loggable: $this);
                return false;
            }
        });
    }

    public static function bulkSetAsPaid(array $orderIds, $paymentDate, $paymentMethod = null, $deductFromBalance = false)
    {
        $errorMessages = [];

        try {
            DB::transaction(function () use ($orderIds, $paymentMethod, $paymentDate, $deductFromBalance, &$errorMessages) {
                foreach ($orderIds as $orderId) {
                    $order = Order::find($orderId);

                    if (!$order) {
                        $errorMessages[] = "Order ID {$orderId} not found.";
                        continue;
                    }

                    $result = $order->setAsPaid($paymentDate, $paymentMethod, $deductFromBalance);

                    if ($result === false) {
                        $errorMessages[] = "Failed to mark Order ID {$orderId} as paid.";
                    }
                }

                if (!empty($errorMessages)) {
                    throw new Exception(implode(', ', $errorMessages));
                }
            });

            // If no errors, return true
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to set orders as paid in bulk process: ' . $e->getMessage());
            return $errorMessages;
        }
    }

    public function setAsPaid($paymentDate, $paymentMethod = null, $deductFromBalance = false, $migrated = false)
    {
        return DB::transaction(function () use ($paymentMethod, $paymentDate, $deductFromBalance, $migrated) {
            try {
                /** @var User */
                $loggedInUser = Auth::user();
                if ($loggedInUser && !$loggedInUser->can('pay', $this)) {
                    throw new Exception('User does not have permission to mark this order as paid.');
                }

                if ($this->is_paid) {
                    throw new Exception("Order ID {$this->id} is already marked as paid.");
                }

                $customer = $this->customer;
                if (!$customer) {
                    throw new Exception("Order ID {$this->id} does not have an associated customer.");
                }

                if ($deductFromBalance) {
                    $deductedAmount = min($customer->balance, $this->remaining_to_pay);
                    if ($deductedAmount !== 0) {
                        $customer->balance -= $deductedAmount;
                        $customer->save();
                        // Log the balance deduction
                        BalanceTransaction::create([
                            'customer_id' => $customer->id,
                            'order_id' => $this->id,
                            'amount' => -$deductedAmount,
                            'balance' => $customer->balance,
                            'description' => 'Payment deducted from balance',
                            'created_by' => $migrated ? 1 : $loggedInUser->id,
                        ]);
                    }
                } elseif ($paymentMethod == CustomerPayment::PYMT_DEBIT) {
                    return $this->setAsDebit();
                } else {
                    $remainingAmount = $this->remaining_to_pay;
                    $new_type_balance = CustomerPayment::calculateNewBalance($remainingAmount, $paymentMethod);
                    if ($remainingAmount > 0) {
                        CustomerPayment::create([
                            'customer_id' => $customer->id,
                            'order_id' => $this->id,
                            'amount' => $remainingAmount,
                            'type_balance' => $new_type_balance,
                            'payment_method' => $paymentMethod,
                            'payment_date' => $paymentDate,
                            'created_by' => $migrated ? 1 : $loggedInUser->id,
                        ]);
                    }
                }
                $this->refresh();
                // Mark the order as paid if fully settled
                if ($this->remaining_to_pay == 0) {
                    $this->is_paid = true;
                    $this->save();
                    if ($this->is_in_delivery && $this->is_delivered) {
                        $this->is_delivered = true;
                        $this->save();
                        $this->setStatus(self::STATUS_DONE, true);
                    }
                }

                // Log successful payment
                AppLog::info("Order {$this->order_number} marked as paid", loggable: $this);

                return true;
            } catch (Exception $e) {
                report($e);
                AppLog::error('Failed to set order as paid: ' . $e->getMessage(), loggable: $this);
                return false;
            }
        });
    }

    /**
     * Add given quantities of products to the order, with optional combo ID.
     *
     * @param array $products Array of products with 'product_id', 'quantity', 'price', and optional 'combo_id'.
     * @param string|null $note Optional note for adding products.
     * @return bool True if successful, false otherwise.
     */
    public function addProducts(array $products, ?string $note = null): bool
    {
        /** @var User */
        $loggedInUser = Auth::user();

        if ($this->driver_id && !$loggedInUser->can('overrideDriverLimits', $this)) {
            $totalExtraWeight = 0;
            foreach ($products as $product) {
                $productObject = Product::findOrFail($product['product_id']);
                $totalExtraWeight += $product['quantity'] * $productObject->weight;
            }
            $driver = Driver::findOrFail($this->driver_id);
            if (!$driver->checkProductsLimits($totalExtraWeight, $this->delivery_date)) {
                return throw new Exception('Driver limits exceeded', self::DRIVER_LIMITS_EXCEEDED_CODE);
            }
        }

        DB::beginTransaction();

        try {
            if (!$this->is_new) {
                return false;
            }

            foreach ($products as $product) {
                // Skip if quantity is invalid
                if (empty($product['quantity']) || $product['quantity'] <= 0) {
                    continue;
                }

                $orderProduct = $this->products()->where('product_id', $product['product_id'])->whereNull('combo_id')->first();

                if ($orderProduct && !$product['combo_id']) {
                    // Update the existing order product with the additional quantity and updated
                    $orderProduct->quantity = $product['quantity'];
                    $orderProduct->price = $product['price']; // Update price if provided
                    $orderProduct->combo_id = $product['combo_id'] ?? $orderProduct->combo_id; // Only update if combo_id is provided
                    $orderProduct->save();
                } else {
                    // Add new product entry to the order_products table
                    OrderProduct::create([
                        'order_id' => $this->id,
                        'product_id' => $product['product_id'],
                        'quantity' => $product['quantity'],
                        'price' => $product['price'],
                        'combo_id' => $product['combo_id'] ?? null,
                        'note' => $note,
                    ]);
                }

                // Update the inventory quantity for the product if an inventory record exists
                $inventory = Product::find($product['product_id'])->inventory;
                if ($inventory) {
                    $inventory->commitQuantity($product['quantity'], 'Added to order #' . $this->order_number);
                }
            }

            DB::commit();
            $this->refreshTotalAmount();

            AppLog::info('Products added', loggable: $this);
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            AppLog::error('Failed to add products to order', $e->getMessage());
            return false;
        }
    }

    /**
     * Cancel and remove all products from the order, adjusting inventory and logging each removal.
     *
     * @param string|null $reason Optional reason for the cancellation.
     * @return bool True if all products were canceled successfully, false otherwise.
     */
    public function cancelAllProducts(?string $reason = null): bool
    {
        DB::beginTransaction();

        try {
            foreach ($this->products as $orderProduct) {
                $productId = $orderProduct->product_id;
                $quantityToRemove = $orderProduct->quantity;

                // Remove or delete the product from the order
                $orderProduct->delete();

                // Update the inventory quantity for the product
                $inventory = $orderProduct->product->inventory;
                if ($inventory) {
                    if ($this->is_new) {
                        $inventory->commitQuantity(-$quantityToRemove, 'Returned from order #' . $this->order_number);
                    } else {
                        $inventory->addTransaction($quantityToRemove, 'Returned from order #' . $this->order_number);
                    }
                }

                // Log the removal in the order_removed_products table
                OrderRemovedProduct::updateOrCreate(
                    [
                        'order_id' => $this->id,
                        'product_id' => $productId,
                    ],
                    [
                        'quantity' => $quantityToRemove,
                        'price' => $orderProduct->price,
                        'reason' => $reason,
                    ],
                );
            }
            $this->refreshTotalAmount();
            DB::commit();

            AppLog::info('All products canceled', loggable: $this);
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            AppLog::error('Failed to cancel all products for order', $e->getMessage());
            return false;
        }
    }

    /**
     * Cancel and remove given quantities of order products, and log removals.
     *
     * @param array $products Array of products with 'id' and 'quantity' to cancel.
     * @param string|null $reason Reason for the removal (optional).
     * @return bool True if successful, false otherwise.
     */
    public function cancelProducts(array $products, ?string $reason = null, ?string $returnPaymentMethod = null, bool $returnShippingAmount = false): bool
    {
        DB::beginTransaction();

        try {
            if ($returnShippingAmount) {
                $cancelledProductsTotalAmount = $this->delivery_amount;
                $this->delivery_amount = 0;
                $this->save();
            } else {
                $cancelledProductsTotalAmount = 0;
            }

            foreach ($products as $product) {
                // Skip if return_quantity is 0 or invalid
                if (empty($product['return_quantity']) || $product['return_quantity'] <= 0) {
                    continue;
                }

                $orderProduct = OrderProduct::find($product['order_product_id']);

                if (!$orderProduct || $orderProduct->quantity < $product['quantity']) {
                    throw new Exception('Insufficient quantity or invalid product ID.');
                }

                // Calculate the remaining quantity and update or delete the product from order
                $remainingQuantity = $orderProduct->quantity - $product['return_quantity'];

                if ($remainingQuantity > 0) {
                    $orderProduct->quantity = $remainingQuantity;
                    $orderProduct->save();
                } else {
                    $orderProduct->delete();
                }

                // Update the inventory quantity for the product
                if ($product['isReturnToStock']) {
                    if ($orderProduct->inventory) {
                        if ($this->is_new) {
                            $orderProduct->inventory->commitQuantity(-$product['return_quantity'], 'Returned from order #' . $this->order_number);
                        } else {
                            $orderProduct->inventory->addTransaction($product['return_quantity'], 'Returned from order #' . $this->order_number);
                        }
                    }
                } else {
                    if ($orderProduct->inventory) {
                        $orderProduct->inventory->removeCommit($product['return_quantity'], 'Removed from order #' . $this->order_number . '. Not returned to stock');
                    }
                }

                // Check if the product has already been cancelled and update the record if it exists
                $existingRemovedProduct = OrderRemovedProduct::where('order_id', $this->id)->where('product_id', $orderProduct->product_id)->first();

                if ($existingRemovedProduct) {
                    // Update the existing record with the new return quantity and reason
                    $existingRemovedProduct->quantity += $product['return_quantity'];
                    $existingRemovedProduct->reason = $reason; // Update the reason if needed
                    $existingRemovedProduct->save();
                } else {
                    // Add entry to order_removed_products
                    OrderRemovedProduct::create([
                        'order_id' => $this->id,
                        'product_id' => $orderProduct->product_id,
                        'quantity' => $product['return_quantity'],
                        'price' => $orderProduct->price,
                        'reason' => $reason,
                    ]);
                }

                $cancelledProductsTotalAmount += $product['return_quantity'] * $product['price'];
            }

            $totalReturnedAmount = min($cancelledProductsTotalAmount, $this->total_paid);

            if ($totalReturnedAmount > 0) {
                if ($returnPaymentMethod) {
                    $new_type_balance = CustomerPayment::calculateNewBalance(-$totalReturnedAmount, $returnPaymentMethod);
                    CustomerPayment::create([
                        'customer_id' => $this->customer->id,
                        'order_id' => $this->id,
                        'amount' => -$totalReturnedAmount,
                        'type_balance' => $new_type_balance,
                        'payment_method' => $returnPaymentMethod,
                        'payment_date' => Carbon::now(),
                        'created_by' => Auth::id(),
                    ]);
                } else {
                    BalanceTransaction::create([
                        'customer_id' => $this->customer->id,
                        'order_id' => $this->id,
                        'amount' => $totalReturnedAmount,
                        'balance' => $this->customer->balance + $totalReturnedAmount,
                        'description' => 'Payment returned to balance',
                        'created_by' => Auth::id(),
                    ]);
                }
            }

            DB::commit();
            $this->refreshTotalAmount();

            AppLog::info('Products returned', loggable: $this);
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            AppLog::error('Failed to return products for order', $e->getMessage(), loggable: $this);
            return false;
        }
    }

    private function refreshTotalAmount()
    {
        $this->load('products');
        $this->total_amount = round($this->total_items_price) + $this->delivery_amount - $this->discount_amount;
        $this->save();
    }

    // File Path: /app/Models/Order.php

    public function generateWhatsAppMessage()
    {
        $message = <<<EOD
        عزيزي/عزيزتي {$this->customer_name}،

        شكراً لطلبك مع جينوين جيانت!

        إجمالي السعر: {$this->total_amount} جنيه

        الاوردر:
        EOD;

        foreach ($this->products as $product) {
            $message .= "\n• {$product->product->name}: {$product->quantity} ";
        }

        $deliveryDate = $this->convertDayToArabic($this->delivery_date->format('l')) . ' ' . $this->delivery_date->format('d/m/Y');
        $message .= "\n\nتـاريخ توصيل الطلـب: {$deliveryDate}";

        $message .= "\n\nتفاصيل المندوب:\n";
        if (!empty($this->driver->user->full_name)) {
            $message .= "الاسم: {$this->driver->user->full_name}\n";
        }
        if (!empty($this->driver->user->phone)) {
            $message .= "رقم الهاتف: {$this->driver->user->phone}\n";
        }

        if (!empty($this->driver->start_time) && !empty($this->driver->end_time)) {
            $message .= 'موعد التسليم: من ' . Carbon::parse($this->driver->start_time)->format('h:i A') . ' إلى ' . Carbon::parse($this->driver->end_time)->format('h:i A') . "\n";
        }

        $message .= <<<EOD

        لأي استفسار، لا تتردد في الاتصال بنا. شكراً لتعاملك معنا!
        EOD;

        $encodedMessage = urlencode($message);
        $phoneNumber = str_replace(' ', '', $this->customer_phone);
        if (Str::startsWith($phoneNumber, '01')) {
            $phoneNumber = '+2' . $phoneNumber;
        }

        return "https://wa.me/{$phoneNumber}?text={$encodedMessage}";
    }

    function convertDayToArabic(string $day): string
    {
        // Mapping of English weekdays to Arabic
        $daysMapping = [
            'Monday' => 'الاثنين',
            'Tuesday' => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday' => 'الخميس',
            'Friday' => 'الجمعة',
            'Saturday' => 'السبت',
            'Sunday' => 'الأحد',
        ];

        // Normalize the input to ensure proper mapping
        $day = ucfirst(strtolower(trim($day)));

        // Return the Arabic day or an error message for invalid input
        return $daysMapping[$day] ?? 'Invalid day input';
    }

    /**
     * Update the note for the order.
     *
     * @param  string $note
     * @return bool
     */
    public function updateNote(?string $note = null): bool
    {
        /** @var User */
        $loggedInUser = Auth::user();
        if ($loggedInUser && !$loggedInUser->can('updateOrderNote', $this)) {
            return false;
        }

        try {
            $this->note = empty($note) ? null : $note;
            $this->save();

            AppLog::info('Note updated', loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to update note for order', $e->getMessage());
            return false;
        }
    }

    public function updateDriverNote(?string $note = null): bool
    {
        /** @var User */
        $loggedInUser = Auth::user();
        if ($loggedInUser && !$loggedInUser->can('updateDriverNote', $this)) {
            return false;
        }

        try {
            $this->driver_note = empty($note) ? null : $note;
            $this->save();

            AppLog::info('Driver Note updated', loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to update driver note for order', $e->getMessage());
            return false;
        }
    }

    public function toggleIsDelivered(): bool
    {
        /** @var User */
        $loggedInUser = Auth::user();
        if ($loggedInUser && !$loggedInUser->can('update', $this)) {
            return false;
        }

        try {
            $this->is_delivered = !$this->is_delivered;
            $this->save();

            AppLog::info('Order delivery status toggled', loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to toggle delivery status for order', $e->getMessage());
            return false;
        }
    }

    public function moveToPosition(?int $newPosition = null)
    {
        return DB::transaction(function () use ($newPosition) {
            if ($newPosition == null || $newPosition <= 0) {
                $this->driver_order = null;
                return $this->save();
            }

            $this->driver_order = $newPosition;
            $this->save();

            $dayOrderedOrders = self::where('driver_id', $this->driver_id)->whereDate('delivery_date', $this->delivery_date)->whereNotNull('driver_order')->orderBy('driver_order')->whereNot('id', $this->id)->get();

            foreach ($dayOrderedOrders as $index => $or) {
                $or->driver_order = $index + 1 + ($newPosition <= $index + 1 ? 1 : 0);
                $or->save();
            }

            $dayOrderedOrders = self::where('driver_id', $this->driver_id)->whereDate('delivery_date', $this->delivery_date)->whereNotNull('driver_order')->orderBy('driver_order')->get();

            foreach ($dayOrderedOrders as $index => $or) {
                $or->driver_order = $index + 1;
                $or->save();
            }

            return true;
        });
    }

    public function updateNoOfBags(?int $bags_count = null)
    {
        /** @var User */
        $loggedInUser = Auth::user();

        // Check if the user has permission to update the order
        if ($loggedInUser && !$loggedInUser->can('update', $this)) {
            return false;
        }

        $this->no_of_bags = $bags_count ?? 0;
        $this->save();
        AppLog::info("Number of bags set to $this->no_of_bags", loggable: $this);
        return true;
    }

    public function updateDriverPaymentType(?string $paymentType = null): bool
    {
        /** @var User */
        $loggedInUser = Auth::user();

        // Check if the user has permission to update the order
        if ($loggedInUser && !$loggedInUser->can('update', $this)) {
            return false;
        }

        try {
            // Validate the payment type
            if ($paymentType && !in_array($paymentType, CustomerPayment::PAYMENT_METHODS, true)) {
                throw new Exception("Invalid payment type: {$paymentType}");
            }

            // Update the driver_payment_type field
            $this->driver_payment_type = $paymentType;
            $this->save();

            AppLog::info('Driver payment type updated', loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to update driver payment type for order', $e->getMessage());
            return false;
        }
    }

    /**
     * @param  string $customerName
     * @param  string $shippingAddress
     * @param  string $customerPhone
     * @param  int    $zoneId
     * @return bool
     */
    public function updateShippingDetails(string $customerName, string $shippingAddress, string $customerPhone, int $zoneId): bool
    {
        /** @var User */
        $loggedInUser = Auth::user();
        if ($loggedInUser && !$loggedInUser->can('update', $this)) {
            return false;
        }

        try {
            $zone = Zone::findOrFail($zoneId);

            $this->customer_name = $customerName;
            $this->shipping_address = $shippingAddress;
            $this->customer_phone = $customerPhone;
            $this->zone_id = $zoneId;
            $this->delivery_amount = $zone->delivery_rate;

            $this->save();

            $this->refreshTotalAmount();

            AppLog::info('Shipping details updated.', loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to update shipping details for order', $e->getMessage());
            return false;
        }
    }

    private static function generateNextOrderNumber(): string
    {
        $latestOrder = self::orderBy('id', 'desc')->withTrashed()->first();

        // Determine the next order number based on the latest order
        if ($latestOrder) {
            $latestOrderNumber = (int) $latestOrder->order_number; // Cast to int for increment
            $nextOrderNumber = $latestOrderNumber + 1; // Increment by 1
        } else {
            $nextOrderNumber = 1; // Start from 1 if no orders exist
        }

        return str_pad($nextOrderNumber, 6, '0', STR_PAD_LEFT); // Format as a 4-digit number (e.g., 000001)
    }

    public function addComment(string $comment): void
    {
        AppLog::comment($comment, $desc = null, loggable: $this);
    }

    public function deleteOrder(): bool
    {
        /** @var User */
        $loggedInUser = Auth::user();
        if ($loggedInUser && !$loggedInUser->can('delete', $this)) {
            return false;
        }
        DB::beginTransaction();
        try {
            foreach ($this->products as $orderProduct) {
                $quantityToRemove = $orderProduct->quantity;

                // Remove or delete the product from the order
                $orderProduct->delete();

                // Update the inventory quantity for the product
                $inventory = $orderProduct->product->inventory;
                if ($inventory) {
                    if ($this->is_new) {
                        $inventory->commitQuantity(-$quantityToRemove, 'Returned from deleted order #' . $this->order_number);
                    } else {
                        $inventory->addTransaction($quantityToRemove, 'Returned from deleted order #' . $this->order_number);
                    }
                }
            }
            $orderNumber = $this->order_number;
            $this->delete();
            DB::commit();

            AppLog::info('Order #' . $orderNumber . ' deleted successfuly');
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            AppLog::error('Failed to delete order', $e->getMessage());
            return false;
        }
    }

    public static function printPrepareDoc(Carbon $day)
    {
        $template = IOFactory::load(storage_path('import/inventory_report.xlsx'));
        if (!$template) {
            throw new Exception('Failed to read template file');
        }
        $newFile = $template->copy();
        $activeSheet = $newFile->getActiveSheet();

        $todayShifts = Driver::hasOrdersOn([$day])->get();
        $i = 2;
        foreach ($todayShifts as $s) {
            $orders = Order::with('products', 'products.product')->search(deliveryDates: [$day], driverId: $s->id)->openOrders()->get();
            $activeSheet->getCell("A$i")->setValue($s->shift_title);

            $activeSheet
                ->getStyle("A$i")
                ->getBorders()
                ->getOutline()
                ->setBorderStyle(Border::BORDER_THIN)
                ->setColor(new Color('00000000'));

            $activeSheet
                ->getStyle("A$i")
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('D1DBF0');

            foreach ($orders as $o) {
                $order_details_text = '';
                foreach ($o->products as $product) {
                    $order_details_text .= "• {$product->product->name}: {$product->quantity} \n";
                }
                $activeSheet
                    ->getStyle("A$i")
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('D1DBF0');
                $activeSheet->getCell("B$i")->setValue($o->customer->name);
                $activeSheet
                    ->getStyle("B$i")
                    ->getBorders()
                    ->getOutline()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->setColor(new Color('00000000'));

                $activeSheet->getCell("C$i")->setValue($order_details_text);
                $activeSheet
                    ->getStyle("C$i")
                    ->getBorders()
                    ->getOutline()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->setColor(new Color('00000000'));
                $activeSheet
                    ->getStyle("C$i")
                    ->getAlignment()
                    ->setWrapText(true);

                $activeSheet->getRowDimension(1)->setRowHeight(30);

                $activeSheet->getCell("D$i")->setValue($o->products->count());
                $activeSheet
                    ->getStyle("D$i")
                    ->getBorders()
                    ->getOutline()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->setColor(new Color('00000000'));
                $i++;
            }
        }

        $writer = new Xlsx($newFile);
        $file_path = "downloads/inventory_{$day->format('Md')}.xlsx";
        $public_file_path = storage_path($file_path);
        $writer->save($public_file_path);

        return response()->download($public_file_path)->deleteFileAfterSend(true);
    }

    public function calculateStartDeliveryCrDriver(): bool
    {
        try {

            $driver = $this->driver;
            if (!$driver || !$driver->user) {
                return false;
            }
            $driverUser = $driver->user;
            $driverUserId = $driverUser->id;

            $existingTransaction = BalanceTransaction::where('transactionable_id', $driverUserId)
                ->where('transactionable_type', $driverUser->getMorphClass())
                ->where('description', "بداية توصيل الأوردرات عن يوم {$this->delivery_date->format('d/m/Y')}")
                ->exists();

            if (!$existingTransaction) {
                $description = "بداية توصيل الأوردرات عن يوم {$this->delivery_date->format('d/m/Y')}";

                BalanceTransaction::createBalanceTransaction($driverUser, $driver->user->driver_day_fees, $description);

                return true;
            }

            return true;
        } catch (Exception $e) {
            // Log and handle the exception
            AppLog::error('Failed to credit driver for delivery', $e->getMessage());
            report($e);
            return false;
        }
    }

    public function checkOrderPayment($closeIfDelivered = true)
    {

        /** @var User */
        $user = Auth::user();
        if (!$user && !$user->can('resetStatus', $this)) {
            return false;
        }

        if ($this->remaining_to_pay == 0 && !$this->is_paid) {
            $this->is_paid = 1;
            $this->save();
        }

        if ($closeIfDelivered && $this->is_paid && (($this->is_confirmed && $this->is_ready) || $this->is_in_delivery || $this->is_delivered)) {
            $this->is_delivered = true;
            $this->save();
            $this->setStatus(self::STATUS_DONE, true);
        }

        return true;
    }

    public function creditDriverPerOrder($is2x = false): bool
    {
        try {

            if ($this->is_debit) {
                return true;
            }

            $driver = $this->driver;

            if (!$driver || !$driver->user) {
                return false;
            }

            $driverUser = $driver->user;

            if ($is2x) {
                $desc = "توصيل أوردر (x2) للعميل {$this->customer_name} يوم {$this->delivery_date->format('d/m/Y')}";
            } else {
                $desc = "توصيل أوردر للعميل {$this->customer_name} يوم {$this->delivery_date->format('d/m/Y')}";
            }

            BalanceTransaction::createBalanceTransaction($driverUser, $this->zone->driver_order_rate, $desc, $this->id);

            return true;
        } catch (Exception $e) {
            AppLog::error("Failed to credit driver for order #{$this->id}", $e->getMessage());
            report($e);
            return false;
        }
    }

    public function driverHasOrdersInAnotherShift()
    {
        if (!$this->driver || !$this->driver->user) {
            return false;
        }

        $driverUserId = $this->driver->user_id;

        return self::whereHas('driver', function (Builder $query) use ($driverUserId) {
            $query->where('user_id', $driverUserId);
        })
            ->whereDate('delivery_date', $this->delivery_date)
            ->whereIn('status', [self::STATUS_IN_DELIVERY, self::STATUS_DONE, self::STATUS_RETURNED])
            ->where('driver_id', '!=', $this->driver_id)
            ->first();
    }

    public function creditDriverForReturnedShift(): bool
    {
        try {
            $driver = $this->driver;

            if (!$driver || !$driver->user) {
                return false;
            }

            $driverUser = $driver->user;

            $description = "رجوع يوم توصيل عن يوم {$this->delivery_date->format('d/m/Y')}";

            $returnedTrans = BalanceTransaction::where('transactionable_id', $driverUser->id)->where('transactionable_type', User::MORPH_TYPE)->where('description', $description)->get();

            foreach ($returnedTrans as $transaction) {
                if ($transaction->order_id && $transaction->order->driver_id === $this->driver_id) {
                    return true;
                }
            }

            BalanceTransaction::createBalanceTransaction($driverUser, $this->zone->driver_return_rate, $description, $this->id);

            return true;
        } catch (Exception $e) {
            AppLog::error("Failed to credit driver for order #{$this->id}", $e->getMessage());
            report($e);
            return false;
        }
    }


    public function debitDriverPerOrder(): bool
    {
        try {
            $driver = $this->driver;

            if (!$driver || !$driver->user) {
                return false;
            }

            $driverUser = $driver->user;

            // Fetch the existing balance transaction for this order and user
            $existingTransaction = BalanceTransaction::where('transactionable_id', $driverUser->id)->where('transactionable_type', User::MORPH_TYPE)->where('order_id', $this->id)->first();

            if (!$existingTransaction) {
                AppLog::error("No existing balance transaction found for order #{$this->id} and user #{$driverUser->id}");
                return false;
            }

            // Reverse the transaction with the negated amount
            BalanceTransaction::createBalanceTransaction(
                $driverUser,
                -1 * $existingTransaction->amount, // Use the negated amount from the existing transaction
                "إلغاء رصيد توصيل الأوردر للعميل {$this->customer_name} بتاريخ {$this->delivery_date->format('d/m/Y')}",
                $this->id,
            );

            return true;
        } catch (Exception $e) {
            AppLog::error("Failed to debit driver for order #{$this->id}", $e->getMessage());
            report($e);
            return false;
        }
    }

    public static function exportReport($searchText, $zone_id = null, $driver_id = null, ?Carbon $created_from = null, ?Carbon $created_to = null, ?Carbon $delivery_from = null, ?Carbon $delivery_to = null, $creator_id = null, $status = null)
    {
        $orders = self::report($searchText, $zone_id, $driver_id, $created_from, $created_to, $delivery_from, $delivery_to, $creator_id, $status)->get();

        $template = IOFactory::load(resource_path('import/orders_report.xlsx'));
        if (!$template) {
            throw new Exception('Failed to read template file');
        }
        $newFile = $template->copy();
        $activeSheet = $newFile->getActiveSheet();

        $i = 2;

        /** @var User */
        foreach ($orders as $o) {
            $activeSheet->getCell('A' . $i)->setValue($o);
            $i++;
        }

        $writer = new Xlsx($newFile);
        $file_path = "'/downloads/orders_export.xlsx";
        $public_file_path = storage_path($file_path);
        $writer->save($public_file_path);

        return response()->download($public_file_path)->deleteFileAfterSend(true);
    }

    // Scopes
    public function scopeReport($query, $searchText, $zone_ids = null, $driver_id = null, ?Carbon $created_from = null, ?Carbon $created_to = null, ?Carbon $delivery_from = null, ?Carbon $delivery_to = null, $creator_id = null, $status = null)
    {
        return $query
            ->notCancelledOrReturned()
            ->select('orders.*')
            ->when($searchText || $status, fn($q) => $q->search(searchText: $searchText, status: $status))
            ->when(count($zone_ids), fn($q) => $q->whereIn('orders.zone_id', $zone_ids))
            ->when($driver_id, fn($q) => $q->where('orders.driver_id', $driver_id))
            ->when($created_from, fn($q) => $q->where('orders.created_at', '>=', $created_from->format('Y-m-d 00:00:00')))
            ->when($created_to, fn($q) => $q->where('orders.created_at', '<=', $created_to->format('Y-m-d 23:59:59')))
            ->when($delivery_from, fn($q) => $q->where('orders.delivery_date', '>=', $delivery_from->format('Y-m-d 00:00:00')))
            ->when($delivery_to, fn($q) => $q->where('orders.delivery_date', '<=', $delivery_to->format('Y-m-d 23:59:59')))
            ->when($creator_id, fn($q) => $q->where('orders.created_by', $creator_id));
    }


    public function scopeWithTotalQuantity(Builder $query)
    {
        $query->withCount([
            'products as total_quantity' => function ($query) {
                $query->select(DB::raw('SUM(quantity)'));
            },
        ]);
    }

    public function getTotalItemsAttribute()
    {
        return $this->products->sum('quantity');
    }

    public function getTotalItemsPriceAttribute()
    {
        return $this->products->sum(function ($product) {
            return $product->price * $product->quantity;
        });
    }

    public function getRemainingToPayAttribute()
    {
        $this->loadMissing(['payments', 'balanceTransactions']);
        $totalPayments = $this->payments->sum('amount');
        $totalBalanceTransactions = $this->balanceTransactions->sum(function ($transaction) {
            return abs($transaction->amount);
        });

        // Step 3: Calculate the remaining amount by subtracting from total_amount
        $remainingAmount = round($this->total_amount - ($totalPayments + $totalBalanceTransactions));

        return $remainingAmount > 0 ? $remainingAmount : 0;
    }

    public function getTotalPaidAttribute()
    {
        $this->loadMissing(['payments', 'balanceTransactions']);

        $totalPayments = $this->payments->sum('amount');
        $totalBalanceTransactions = $this->balanceTransactions->sum(function ($transaction) {
            return abs($transaction->amount);
        });

        // Return the total amount paid
        $totalPaid = $totalPayments + $totalBalanceTransactions;

        return $totalPaid;
    }

    public function getCanChangeDriverAttribute(): bool
    {
        return $this->status === self::STATUS_NEW || $this->status === self::STATUS_READY || $this->status === self::STATUS_IN_DELIVERY;
    }

    public function getInHouseAttribute(): bool
    {
        return $this->status === self::STATUS_NEW || $this->status === self::STATUS_READY;
    }

    public function getIsNewAttribute(): bool
    {
        return $this->status === self::STATUS_NEW;
    }

    public function getIsReadyAttribute(): bool
    {
        return $this->status === self::STATUS_READY;
    }

    public function getIsInDeliveryAttribute(): bool
    {
        return $this->status === self::STATUS_IN_DELIVERY;
    }

    public function getIsDoneAttribute(): bool
    {
        return $this->status === self::STATUS_DONE;
    }

    public function areAllProductsReady(): bool
    {
        return $this->products()->where('is_ready', false)->doesntExist();
    }

    public function areAllProductsAvailable(): bool
    {
        $res = true;
        foreach ($this->products as $orderProduct) {
            $res &= ($orderProduct->product->inventory->on_hand - $orderProduct->quantity) >= 0;
        }
        return $res;
    }

    public function isOpenToPay()
    {
        $openStatuses = [self::STATUS_NEW, self::STATUS_READY, self::STATUS_IN_DELIVERY, self::STATUS_DONE];
        return !$this->is_paid && in_array($this->status, $openStatuses);
    }

    public function isPartlyPaid()
    {
        $hasPayments = $this->payments()->exists();
        $hasBalanceTransactions = $this->balanceTransactions()->exists();

        return ($hasPayments || $hasBalanceTransactions) && ($this->remaining_to_pay > 0 && $this->remaining_to_pay < $this->total_amount);
    }

    public function scopeShift(Builder $query, $driverId, $day)
    {

        return $query->search(deliveryDates: [$day], driverId: $driverId)
            ->confirmed()->withTotalQuantity()->notCancelledOrders()
            ->orderByRaw('driver_order IS NULL, driver_order ASC')->sortByZone();
    }

    public function scopeSearch(Builder $query, ?string $searchText = null, array $deliveryDates = [], ?string $status = null, ?int $zoneId = null, ?int $driverId = null, ?bool $isPaid = null, $skipUserCheck = false, array $zoneIds = []): Builder
    {
        if (!joined($query, 'zones')) {
            $query->join('zones', 'zones.id', '=', 'orders.zone_id');
        }

        // if (!$skipUserCheck && Auth::user()->is_sales) {
        //     $query->where('created_by', Auth::id());
        // }

        return $query
            ->select('orders.*')
            ->when($searchText, function ($query, $searchText) {
                $words = explode(' ', $searchText);
                foreach ($words as $w) {
                    $query->where(function ($q) use ($w) {
                        $q->where('order_number', 'like', '%' . $w . '%')
                            ->orWhere('customer_name', 'like', '%' . $w . '%')
                            ->orWhere('zones.name', 'like', '%' . $w . '%')
                            ->orWhere('customer_phone', 'like', '%' . $w . '%');
                    });
                }
            })
            ->when(!empty($deliveryDates), function ($query) use ($deliveryDates) {
                $query->whereIn('delivery_date', $deliveryDates);
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($zoneId, function ($query, $zoneId) {
                $query->where('zone_id', $zoneId);
            })
            ->when(count($zoneIds), function ($query) use ($zoneIds) {
                $query->whereIn('orders.zone_id', $zoneIds);
            })
            ->when($driverId, function ($query, $driverId) {
                $query->where('driver_id', $driverId);
            })
            ->when(!is_null($isPaid), function ($query) use ($isPaid) {
                $query->where('is_paid', $isPaid);
            });
    }

    public function scopeNotCancelledOrders(Builder $query): Builder
    {
        return $query->whereNot('status', self::STATUS_CANCELLED);
    }

    public function scopeOpenOrders(Builder $query): Builder
    {
        return $query->where(function (Builder $query) {
            $query->whereNotIn('status', [self::STATUS_DONE, self::STATUS_RETURNED, self::STATUS_CANCELLED])
                ->orWhere(function (Builder $query) {
                    $query->whereNotIn('status', [self::STATUS_RETURNED, self::STATUS_CANCELLED])->where('is_paid', false);
                });
        });
    }

    public function scopeCancelledOrders(Builder $query): Builder
    {
        return $query
            ->where(function (Builder $query) {
                $query->whereIn('status', [self::STATUS_RETURNED, self::STATUS_CANCELLED])->where(function (Builder $query) {
                    $query->where('status', '!=', self::STATUS_DONE)->orWhere('is_paid', true);
                });
            })
            ->orderByDesc('delivery_date');
    }

    public function scopeDoneOrders(Builder $query): Builder
    {
        return $query
            ->where(function (Builder $query) {
                $query->where('status', self::STATUS_DONE)->where(function (Builder $query) {
                    $query->where('status', '!=', self::STATUS_DONE)->orWhere('is_paid', true);
                });
            })
            ->orderByDesc('delivery_date');
    }

    public function scopeDeliveryBetween(Builder $query, Carbon $from, Carbon $to): Builder
    {
        return $query->where('delivery_date', '>=', $from->format('Y-m-d 00:00:00'))->where('delivery_date', '<=', $to->format('Y-m-d 00:00:00'));
    }

    public function scopeNotCancelledOrReturned(Builder $query): Builder
    {
        return $query->whereNotIn('status', [self::STATUS_RETURNED, self::STATUS_CANCELLED]);
    }

    public function scopeNotDebitOrders(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('orders.is_debit', false);
        });
    }

    public function scopeDebitOrders(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('orders.is_debit', 1)->whereNot('is_paid');
        });
    }

    public function scopePastDeliveryDate(Builder $query): Builder
    {
        return $query->whereDate('delivery_date', '<', Carbon::today());
    }

    public function scopeSortByDeliveryDate(Builder $query): Builder
    {
        return $query->orderBy('delivery_date');
    }

    public function scopeNotPaid(Builder $query): Builder
    {
        return $query->where('is_paid', false);
    }

    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('orders.is_confirmed', true);
    }

    private function getEndOfCustomWeek(\Carbon\Carbon $startOfWeek): \Carbon\Carbon
    {
        // Check the day of the month for the start of the week and determine the correct end date
        $endOfWeek = $startOfWeek->copy();

        if ($startOfWeek->day == 1) {
            $endOfWeek->addDays(6);
        } elseif ($startOfWeek->day == 8) {
            $endOfWeek->addDays(6);
        } elseif ($startOfWeek->day == 15) {
            $endOfWeek->addDays(6);
        } elseif ($startOfWeek->day == 22) {
            $endOfWeek->endOfMonth();
        }

        // Default case: return the last day of the month
        return $endOfWeek;
    }

    public function scopeSortByZone(Builder $query, string $direction = 'asc'): Builder
    {
        if (!joined($query, 'zones')) {
            $query->join('zones', 'zones.id', '=', 'orders.zone_id');
        }

        return $query->orderBy('zones.name', $direction);
    }

    public function scopeDailyTotals($query, $year, $month)
    {
        return $query
            ->confirmed()
            ->selectRaw('DATE(o1.delivery_date) as day')
            ->selectRaw('DAYNAME(o1.delivery_date) as dayName')
            ->selectRaw('COUNT(o1.id) as orders_count')
            ->selectRaw('SUM(o1.total_amount) as total_amount')
            ->selectRaw(
                'SUM(
                                (SELECT SUM(order_products.quantity * products.weight)
                                FROM order_products
                                JOIN products ON order_products.product_id = products.id
                                WHERE o1.id = order_products.order_id
                                AND order_products.deleted_at is null )
                            ) AS total_weightsss',
            )

            ->join('orders as o1', 'o1.id', '=', 'orders.id')
            ->whereYear('o1.delivery_date', $year)
            ->whereMonth('o1.delivery_date', $month)
            ->whereIn('o1.status', Order::OK_STATUSES)
            ->groupBy('day')
            ->orderBy('day', 'ASC');
    }

    public function scopeMonthlyTotals($query, $year)
    {
        return $query
            ->confirmed()
            ->selectRaw('MONTH(o1.delivery_date) as month')
            ->selectRaw('COUNT(o1.id) as total_orders')
            ->selectRaw('SUM(o1.total_amount) as monthly_total_amount')
            ->selectRaw(
                'SUM((
                SELECT SUM(order_products.quantity * products.weight)
                FROM order_products
                JOIN products ON order_products.product_id = products.id
                WHERE o1.id = order_products.order_id
                AND order_products.deleted_at is null
            )) as monthly_total_weight',
            )
            ->join('orders as o1', 'o1.id', '=', 'orders.id')
            ->whereYear('o1.delivery_date', $year)
            ->whereIn('o1.status', Order::OK_STATUSES)
            ->groupBy('month')
            ->orderBy('month', 'ASC');
    }

    public function scopeWeeklyZoneReport($query, $year, $month, $searchText = null, $zoneIds = [])
    {
        $query
            ->selectRaw('zones.name as zone_name')
            ->selectRaw(
                'CASE
                    WHEN DAY(orders.delivery_date) BETWEEN 1 AND 7 THEN 1
                    WHEN DAY(orders.delivery_date) BETWEEN 8 AND 14 THEN 2
                    WHEN DAY(orders.delivery_date) BETWEEN 15 AND 21 THEN 3
                    ELSE 4 END AS week',
            )
            ->selectRaw('COUNT(orders.id) as total_orders')
            ->join('zones', 'zones.id', '=', 'orders.zone_id')
            ->whereYear('orders.delivery_date', $year)
            ->whereMonth('orders.delivery_date', $month)
            ->whereIn('orders.status', Order::OK_STATUSES);

        if (!empty($zoneIds)) {
            $query->whereIn('zones.id', $zoneIds);
        }

        if (!empty($searchText)) {
            $query->where('zones.name', 'LIKE', '%' . $searchText . '%');
        }

        return $query->groupBy('zones.name', 'week')->orderBy('zones.name')->orderBy('week');
    }

    public function scopeProductTotals($query, $startDate = null, $endDate = null)
    {
        $query = $query->join('order_products', 'orders.id', '=', 'order_products.order_id')
            ->join('products', 'order_products.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.name',
                'products.weight',
                DB::raw('SUM(order_products.quantity) as total_quantity'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count')
            )
            ->whereNull('order_products.deleted_at')
            ->whereIn('orders.status', Order::OK_STATUSES)
            ->where('orders.is_confirmed', true)
            ->groupBy('products.id', 'products.name', 'products.weight');

        if ($startDate) {
            $query->where('orders.delivery_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('orders.delivery_date', '<=', $endDate);
        }

        return $query;
    }

    public function scopeUserPerformanceReport($query, $year, $month)
    {
        return $query->selectRaw('CONCAT(users.first_name, " ", users.last_name) as user_name')->selectRaw('DAY(orders.created_at) as day')->selectRaw('COUNT(orders.id) as total_orders')->selectRaw('SUM(orders.total_amount) as total_amount')->join('users', 'users.id', '=', 'orders.created_by')->whereYear('orders.created_at', $year)->whereMonth('orders.created_at', $month)->whereIn('orders.status', Order::OK_STATUSES)->groupBy('users.id', 'day')->orderBy('day', 'ASC');
    }

    public function getTotalWeightAttribute()
    {
        return $this->products()->join('products', 'order_products.product_id', '=', 'products.id')->selectRaw('SUM(products.weight * order_products.quantity) as total_weight')->whereNull('order_products.deleted_at')->value('total_weight') ?? 0;
    }

    public function getValidLocationUrlAttribute()
    {
        $locationUrl = $this->location_url ?? $this->customer->location_url;
        if (filter_var($locationUrl, FILTER_VALIDATE_URL)) {
            return $locationUrl;
        }
        return null;
    }

    public static function getTotalDebit()
    {
        return self::selectRaw('SUM(total_amount) as total_debit')
            ->debitOrders()
            ->openOrders()
            ->get()->first()?->total_debit;
    }

    public static function getTotalZonesForOrders($orders)
    {
        return self::whereIn('id', $orders->pluck('id'))->distinct('zone_id')->count('zone_id');
    }

    public function scopeWithCancelledReadyProducts(Builder $query): Builder
    {
        return $query->where('status', 'cancelled')->whereHas('products', function ($q) {
            $q->withTrashed()->where('is_ready', true)->whereNotNull('deleted_at'); // Filter for soft-deleted products
        });
    }

    // relations
    public function products(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(CustomerPayment::class, 'order_id')->where('customer_id', function ($query) {
            $query->select('customer_id')
                ->from('orders')
                ->whereColumn('orders.id', 'customer_payments.order_id')
                ->limit(1);
        });
    }

    public function balanceTransactions(): HasMany
    {
        return $this->hasMany(BalanceTransaction::class)
            ->where('customer_id', function ($query) {
                $query->select('customer_id')
                    ->from('orders')
                    ->whereColumn('orders.id', 'balance_transactions.order_id')
                    ->limit(1);
            });
    }

    public function removedProducts(): HasMany
    {
        return $this->hasMany(OrderRemovedProduct::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(AppLog::class, 'loggable_id')->where('loggable_type', self::MORPH_TYPE);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
