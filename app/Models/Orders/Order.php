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

class Order extends Model
{
    use HasFactory, SoftDeletes;

    const MORPH_TYPE = 'order';

    protected $casts = [
        'delivery_date' => 'date',
    ];

    protected $fillable = ['order_number', 'customer_id', 'customer_name', 'shipping_address', 'location_url', 'customer_phone', 'status', 'zone_id', 'driver_id', 'periodic_option', 'total_amount', 'delivery_amount', 'discount_amount', 'delivery_date', 'is_paid', 'note', 'created_by'];

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
                $currentStatus = $order->status;
                $allowedNextStatuses = self::getNextStatuses($currentStatus);

                // If the new status is not allowed, throw an exception
                if (!in_array($newStatus, $allowedNextStatuses, true)) {
                    throw new Exception("Order ID {$order->id} cannot transition from {$currentStatus} to {$newStatus}");
                }

                if ($newStatus === self::STATUS_RETURNED || $newStatus === self::STATUS_CANCELLED) {
                    $order->cancelAllProducts();
                }

                // Update the status
                $order->status = $newStatus;
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

    public function setStatus(string $newStatus): bool
    {
        DB::beginTransaction();

        try {
            // Get the current status and check the allowed next statuses
            $currentStatus = $this->status;
            $allowedNextStatuses = self::getNextStatuses($currentStatus);

            // If the new status is not allowed, throw an exception
            if (!in_array($newStatus, $allowedNextStatuses, true)) {
                throw new Exception("Order ID {$this->id} cannot transition from {$currentStatus} to {$newStatus}");
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
    public static function newOrder(int $customerId, string $customerName, string $shippingAddress, string $customerPhone, int $zoneId, $locationURL = null, int $driverId = null, float $totalAmount = 0, float $deliveryAmount = 0, float $discountAmount = 0, Carbon $deliveryDate = null, string $note = null, array $products, $detuctFromBalance = false): Order|bool
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
            $order->created_by = $loggedInUser->id;

            $order->save();

            foreach ($products as $product) {
                $orderProduct = $order->products()->create([
                    'product_id' => $product['id'],
                    'combo_id' => $product['combo_id'],
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                ]);
                $orderProduct->product->inventory->commitQuantity($product['quantity'], 'Order: #' . $order->order_number . ' committed');
            }

            if ($detuctFromBalance) {
                $order->setAsPaid(Carbon::now(), deductFromBalance: true);
            }

            AppLog::info('Order Created successfuly', loggable: $order);
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
            $this->save();

            AppLog::info('Delivery date updated', loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to update delivery date for order', $e->getMessage());
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

    public function setAsPaid($paymentDate, $paymentMethod = null, $deductFromBalance = false)
    {
        return DB::transaction(function () use ($paymentMethod, $paymentDate, $deductFromBalance) {
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
                            'created_by' => $loggedInUser->id,
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
                            'created_by' => $loggedInUser->id,
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
                    ->first();

                if ($orderProduct) {
                    // Update the existing order product with the additional quantity and updated
                    $orderProduct->quantity += $product['quantity'];
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
        if ($loggedInUser && !$loggedInUser->can('update', $this)) {
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

    public function addComment(string $comment): void
    {
        AppLog::comment($comment, $desc = null, loggable: $this);
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

    public function scopeSearch(Builder $query, string $searchText = null, string $deliveryDate = null, string $status = null, int $zoneId = null, int $driverId = null, bool $isPaid = null): Builder
    {
        return $query
            ->when($searchText, function ($query, $searchText) {
                $query->where(function ($q) use ($searchText) {
                    $q->where('order_number', 'like', '%' . $searchText . '%')
                        ->orWhere('customer_name', 'like', '%' . $searchText . '%')
                        ->orWhere('customer_phone', 'like', '%' . $searchText . '%');
                });
            })
            ->when($deliveryDate, function ($query, $deliveryDate) {
                $query->whereDate('delivery_date', $deliveryDate);
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

    public function scopeWeeklyWeightByCustomer(Builder $query, int $zoneId, int $weekCount, string $startMonth): array
    {
        // Start from the first day of the start month
        $startDate = \Carbon\Carbon::parse($startMonth)->startOfMonth();
        // Get the current date as the end date
        $endDate = \Carbon\Carbon::now()->addWeeks(2);

        // Calculate the start of the week based on the given `weekCount`
        $current = $startDate
            ->copy()
            ->addDays(($weekCount - 1) * 7) // Add the correct number of days to get the correct start day (1st, 8th, 15th, 22nd)
            ->startOfDay(); // Ensure it's the start of the day for the given date

        // Initialize the weeks array to hold the weeks we want to display
        $weeks = [];

        // Loop through the weeks until the current date (end date)
        while ($current <= $endDate) {
            $weeks[] = $current->format('Y-m-d');

            // Check if the current week is on the 22nd, if so, start the next week from the 1st of the next month
            if ($current->day == 22) {
                $current = $current->copy()->addMonth()->startOfMonth(); // Move to the 1st day of the next month
            } else {
                $current->addWeek(); // Move to the next week if it's not the 22nd
            }
        }
        // dd($weeks);
        // Fetch orders within the specified zone and date range
        $orders = $query
            ->where('zone_id', $zoneId)
            ->whereBetween('delivery_date', [$startDate, $endDate])
            ->with(['products.product', 'customer'])
            ->get();

        $customerWeights = [];

        $groupedOrders = $orders->groupBy(function ($order) use ($weeks) {
            // Find the corresponding week for each order based on its delivery date
            foreach ($weeks as $week) {
                $startOfWeek = \Carbon\Carbon::parse($week);

                // Determine the end of the week based on custom week pattern (1st, 8th, 15th, 22nd, end of month)
                $endOfWeek = $this->getEndOfCustomWeek($startOfWeek);

                // Check if the order's delivery date is within the week
                if ($order->delivery_date >= $startOfWeek && $order->delivery_date <= $endOfWeek) {
                    return $week;
                }
            }
            return null; // In case there's no match, though this should not happen
        });

        // Collect weekly weights by customer
        foreach ($groupedOrders as $week => $ordersInWeek) {
            foreach ($ordersInWeek as $order) {
                $customerName = $order->customer->name;

                // Calculate total weight for this order
                $totalWeight = $order->products->sum(function ($orderProduct) {
                    return $orderProduct->product->weight * $orderProduct->quantity;
                });

                // Add to customer's weekly total
                $customerWeights[$customerName][$week] = ($customerWeights[$customerName][$week] ?? 0) + $totalWeight;
            }
        }

        // Ensure that the 4th week includes any remaining days
        $lastWeek = end($weeks); // The last week in the weeks array (should be the 4th week)
        $lastWeekEndDate = \Carbon\Carbon::parse($lastWeek)->endOfWeek(); // Get the end date of the last week

        // If the last week's end date is before the month's end, update it to include the entire month
        if ($lastWeekEndDate < $endDate) {
            $weeks[count($weeks) - 1] = \Carbon\Carbon::parse($startDate)->endOfMonth()->format('Y-m-d');
        }

        // Ensure all weeks are present for each customer, even with zero weight
        foreach ($weeks as $week) {
            foreach ($customerWeights as $customerName => $weights) {
                $customerWeights[$customerName][$week] = $weights[$week] ?? 0;
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
        $day = $startOfWeek->day;

        if ($day == 1) {
            return $startOfWeek->copy()->addDays(6); // Ends on the 7th
        } elseif ($day == 8) {
            return $startOfWeek->copy()->addDays(6); // Ends on the 14th
        } elseif ($day == 15) {
            return $startOfWeek->copy()->addDays(6); // Ends on the 21st
        } elseif ($day == 22) {
            return $startOfWeek->copy()->addDays(6); // Ends on the 28th
        }

        // Default case: return the last day of the month
        return $startOfWeek->copy()->endOfMonth();
    }

    public function scopeSortByZone(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->join('zones', 'orders.zone_id', '=', 'zones.id')->orderBy('zones.name', $direction)->select('orders.*');
    }

    public function getTotalWeightAttribute()
    {
        return $this->products()->join('products', 'order_products.product_id', '=', 'products.id')->selectRaw('SUM(products.weight * order_products.quantity) as total_weight')->value('total_weight') ?? 0;
    }

    public static function getTotalZonesForOrders($orders)
    {
        return self::whereIn('id', $orders->pluck('id'))->distinct('zone_id')->count('zone_id');
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
