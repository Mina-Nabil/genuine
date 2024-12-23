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
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Locale;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    const MORPH_TYPE = 'order';
    const currency = 'EGP';

    protected $casts = [
        'delivery_date' => 'date',
    ];

    protected $fillable = ['order_number', 'customer_id', 'customer_name', 'shipping_address', 'location_url', 'customer_phone', 'status', 'zone_id', 'driver_id', 'periodic_option', 'total_amount', 'delivery_amount', 'discount_amount', 'delivery_date', 'is_paid', 'is_confirmed', 'note', 'driver_note', 'created_by', 'is_delivered', 'driver_payment_type', 'driver_order'];

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
                $order->save();
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
                if (!$loggedInUser || !$loggedInUser->can('update', $order)) {
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

    public function resetStatus(): bool
    {
        DB::beginTransaction();

        try {
            if ($this->status === self::STATUS_READY || $this->status === self::STATUS_IN_DELIVERY) {
                foreach ($this->products as $product) {
                    $product->product->inventory->unfulfillCommit($product->quantity);
                    $product->is_ready = false;
                    $product->save();
                }

                $this->status = self::STATUS_NEW;
                $this->save();

                DB::commit();
                return true;
            } else {
                DB::rollBack();
                return false;
            }
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
            // Get the current status and check the allowed next statuses
            $currentStatus = $this->status;
            $allowedNextStatuses = self::getNextStatuses($currentStatus);

            // If the new status is not allowed, throw an exception
            if (!in_array($newStatus, $allowedNextStatuses, true) && !$skipCheck) {
                throw new Exception("Order ID {$this->id} cannot transition from {$currentStatus} to {$newStatus}");
            }

            if ($currentStatus === self::STATUS_NEW && $newStatus === self::STATUS_READY) {
                foreach ($this->products as $product) {
                    $product->product->inventory->fulfillCommit($product->quantity);
                    $product->is_ready = true;
                    $product->save();
                }
            }

            // If the new status is returned or cancelled, handle product cancellation
            if ($newStatus === self::STATUS_RETURNED || $newStatus === self::STATUS_CANCELLED) {
                $this->cancelAllProducts();
            }

            // Update the status
            $this->status = $newStatus;
            $this->save();

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
    public static function newOrder(int $customerId, string $customerName, string $shippingAddress, string $customerPhone, int $zoneId, $locationURL = null, int $driverId = null, float $totalAmount = 0, float $deliveryAmount = 0, float $discountAmount = 0, Carbon $deliveryDate = null, string $note = null, array $products, $detuctFromBalance = false, $migrated = false): Order|bool
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
            $order->total_amount = $totalAmount;
            $order->delivery_amount = $deliveryAmount;
            $order->discount_amount = $discountAmount;
            $order->delivery_date = $deliveryDate;
            $order->is_paid = false;
            $order->note = $note;
            $order->created_by = $migrated ? 1 : $loggedInUser->id;

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
                $order->setStatus(self::STATUS_DONE, true);
            }
            return $order;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to create new order', $e->getMessage());
            return false;
        }
    }

    public function assignDriverToOrder(int $driverId = null): bool
    {
        try {
            if ($this->status !== self::STATUS_NEW && $this->status !== self::STATUS_READY) {
                return false;
            }
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

        try {
            // Update the delivery_date for each order in the array
            foreach ($orderIds as $orderId) {
                $order = self::find($orderId);

                // Proceed if the order exists
                if ($order) {
                    $order->delivery_date = $deliveryDate;
                    if ($order->delivery_date !== $deliveryDate) {
                        $order->driver_order = null;
                    }
                    $order->save();
                    AppLog::info('Delivery date set for order', loggable: $order);
                }
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            report($e); // Log the exception
            AppLog::error('Failed to set delivery date to orders', $e->getMessage());
            return false;
        }
    }

    public function updateDeliveryDate(Carbon $deliveryDate = null): bool
    {
        /** @var User */
        $loggedInUser = Auth::user();
        if ($loggedInUser && !$loggedInUser->can('update', $this)) {
            return false;
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

    public function toggleConfirmation(): bool
    {
        /** @var User */
        $loggedInUser = Auth::user();
        if ($loggedInUser && !$loggedInUser->can('update', $this)) {
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

        $allEligible = $orders->every(function ($order) {
            return $order->in_house;
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
                        'customer_payment_id' => $payment->id,
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
    public function addProducts(array $products, string $note = null): bool
    {
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

                $orderProduct = $this->products()
                    ->where('product_id', $product['product_id'])
                    ->whereNull('combo_id')
                    ->first();

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
    public function cancelAllProducts(string $reason = null): bool
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
    public function cancelProducts(array $products, string $reason = null, string $returnPaymentMethod = null, bool $returnShippingAmount = false): bool
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

                $orderProduct = $this->products()
                    ->where('product_id', $product['product_id'])
                    ->first();

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
                $existingRemovedProduct = OrderRemovedProduct::where('order_id', $this->id)
                    ->where('product_id', $orderProduct->product_id)
                    ->first();

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
            AppLog::error('Failed to return products for order', $e->getMessage());
            return false;
        }
    }

    private function refreshTotalAmount()
    {
        $this->load('products');
        $this->total_amount = $this->total_items_price + $this->delivery_amount - $this->discount_amount;
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
    public function updateNote(string $note = null): bool
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

    public function updateDriverNote(string $note = null): bool
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


    public function moveToPosition(int $newPosition = null)
    {
        return DB::transaction(function () use ($newPosition) {

            if ($newPosition == null) {
                $this->driver_order = NULL;
                $this->save();
            }

            if ($newPosition <= 0) {
                $newPosition = 1;
            }

            if ($this->driver_order === $newPosition) {
                return true;
            }
            $this->driver_order = $newPosition;
            $this->save();

            $dayOrderedOrders = self::where('driver_id', $this->driver_id)
                ->whereDate('delivery_date', $this->delivery_date)
                ->whereNotNull('driver_order')
                ->orderBy('driver_order')
                ->whereNot('id', $this->id)
                ->get();


            foreach ($dayOrderedOrders as $index => $or) {
                $or->driver_order = ($index + ($newPosition <= $index ? 1 : 0)) + 1;
                $or->save();
            }

            return true;
        });
    }

    public function updateNoOfBags(int $bags_count = null)
    {
        /** @var User */
        $loggedInUser = Auth::user();

        // Check if the user has permission to update the order
        if ($loggedInUser && !$loggedInUser->can('update', $this)) {
            return false;
        }

        $this->no_of_bags = $bags_count;
        $this->save();
        $this->addComment("Number of bags set to $bags_count");
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
        $remainingAmount = $this->total_amount - ($totalPayments + $totalBalanceTransactions);

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

    public function getInHouseAttribute(): bool
    {
        return $this->status === self::STATUS_NEW || $this->status === self::STATUS_READY;
    }

    public function getIsNewAttribute(): bool
    {
        return $this->status === self::STATUS_NEW;
    }

    public function areAllProductsReady(): bool
    {
        return $this->products()->where('is_ready', false)->doesntExist();
    }

    public function isOpenToPay()
    {
        $openStatuses = [self::STATUS_NEW, self::STATUS_READY, self::STATUS_IN_DELIVERY];
        return !$this->is_paid && in_array($this->status, $openStatuses);
    }

    public function isPartlyPaid()
    {
        $hasPayments = $this->payments()->exists();
        $hasBalanceTransactions = $this->balanceTransactions()->exists();

        return ($hasPayments || $hasBalanceTransactions) && ($this->remaining_to_pay > 0 && $this->remaining_to_pay < $this->total_amount);
    }

    public function scopeSearch(Builder $query, string $searchText = null, array $deliveryDates = [], string $status = null, int $zoneId = null, int $driverId = null, bool $isPaid = null): Builder
    {
        if (!joined($query, 'zones')) {
            $query->join('zones', 'zones.id', '=', 'orders.zone_id');
        }

        if (Auth::user()->is_sales) {
            $query->where('created_by', Auth::id());
        }

        return $query
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
            ->when($driverId, function ($query, $driverId) {
                $query->where('driver_id', $driverId);
            })
            ->when(!is_null($isPaid), function ($query) use ($isPaid) {
                $query->where('is_paid', $isPaid);
            });
    }

    public function scopeOpenOrders(Builder $query): Builder
    {
        return $query->where(function (Builder $query) {
            $query->whereNotIn('status', [self::STATUS_DONE, self::STATUS_RETURNED, self::STATUS_CANCELLED])->orWhere(function (Builder $query) {
                $query->whereNotIn('status', [self::STATUS_RETURNED, self::STATUS_CANCELLED])->where('is_paid', false);
            });
        });
    }

    public function scopeClosedOrders(Builder $query): Builder
    {
        return $query->where(function (Builder $query) {
            $query->whereIn('status', [self::STATUS_DONE, self::STATUS_RETURNED, self::STATUS_CANCELLED])->where(function (Builder $query) {
                $query->where('status', '!=', self::STATUS_DONE)->orWhere('is_paid', true);
            });
        });
    }

    public function scopePastDeliveryDate(Builder $query): Builder
    {
        return $query->whereDate('delivery_date', '<', Carbon::today());
    }

    public function scopeNotPaid(Builder $query): Builder
    {
        return $query->where('is_paid', false);
    }

    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('is_confirmed', true);
    }

    public function scopeWeeklyWeightByCustomer(Builder $query, int $zoneId, int $weekCount, string $startMonth): array
    {
        $startDate = \Carbon\Carbon::parse($startMonth)->startOfMonth();
        $endDate = \Carbon\Carbon::now()->addWeeks(1);

        $current = $startDate
            ->copy()
            ->addDays(($weekCount - 1) * 7)
            ->startOfDay();

        $weeks = [];
        while ($current <= $endDate) {
            $weeks[] = $current->format('Y-m-d');

            if ($current->day == 22) {
                $current = $current->copy()->addMonth()->startOfMonth();
            } else {
                $current->addWeek();
            }
        }

        $orders = $query
            ->where('zone_id', $zoneId)
            ->whereBetween('delivery_date', [$startDate, $endDate])
            ->with(['products.product', 'customer'])
            ->get();

        $customerWeights = [];

        $groupedOrders = $orders->groupBy(function ($order) use ($weeks) {
            foreach ($weeks as $week) {
                $startOfWeek = \Carbon\Carbon::parse($week);
                $endOfWeek = $this->getEndOfCustomWeek($startOfWeek);

                if ($order->delivery_date >= $startOfWeek && $order->delivery_date <= $endOfWeek) {
                    return $week;
                }
            }
            return null;
        });

        foreach ($groupedOrders as $week => $ordersInWeek) {
            foreach ($ordersInWeek as $order) {
                $customer = $order->customer;

                if (!$customer) {
                    continue;
                }

                $customerName = $customer->name;
                $monthlyWeightTarget = $customer->monthly_weight_target;
                $customer_id = $customer->id;

                $totalWeight = $order->products->sum(function ($orderProduct) {
                    return ($orderProduct->product ? $orderProduct->product->weight : 0) * $orderProduct->quantity;
                });

                if (!isset($customerWeights[$customerName])) {
                    $customerWeights[$customerName] = [
                        'monthly_weight_target' => $monthlyWeightTarget,
                        'last_order_id' => $customer->orders()->latest()->first()->id,
                        'customer_id' => $customer_id,
                        'default_periodic_order' => $customer->periodicOrders()->default()->first(),
                        'weekly_weights' => [],
                    ];
                }

                $customerWeights[$customerName]['weekly_weights'][$week] = ($customerWeights[$customerName]['weekly_weights'][$week] ?? 0) + $totalWeight;
            }
        }

        foreach ($weeks as $week) {
            foreach ($customerWeights as $customerName => &$weights) {
                $weights['weekly_weights'][$week] = $weights['weekly_weights'][$week] ?? 0;
            }
        }

        return [
            'weeks' => $weeks,
            'customerWeights' => $customerWeights,
        ];
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

        return $query->orderBy('zones.name', $direction)->select('orders.*');
    }

    public function getTotalWeightAttribute()
    {
        return $this->products()->join('products', 'order_products.product_id', '=', 'products.id')->selectRaw('SUM(products.weight * order_products.quantity) as total_weight')->value('total_weight') ?? 0;
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
        return $this->hasMany(CustomerPayment::class);
    }

    public function balanceTransactions(): HasMany
    {
        return $this->hasMany(BalanceTransaction::class);
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
