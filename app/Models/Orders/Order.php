<?php

namespace App\Models\Orders;

use App\Models\Customers\Customer;
use App\Models\Customers\Zone;
use App\Models\Products\Product;
use App\Models\Users\AppLog;
use App\Models\Users\Driver;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    const MORPH_TYPE = 'order';

    protected $casts = [
        'delivery_date' => 'date',
    ];

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
                $orderProduct = $order->products()->create([
                    'product_id' => $product['id'],
                    'combo_id' => $product['combo_id'],
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                ]);
                $orderProduct->product->inventory->commitQuantity($product['quantity'], 'Order: #' . $order->order_number . ' committed');
            }

            AppLog::info('Order Created successfuly', loggable: $order);
            return $order;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to create new order', $e->getMessage());
            return false;
        }
    }

    /**
     * Bulk assign a driver to multiple orders.
     *
     * @param array $orderIds Array of order IDs.
     * @param int $driverId Driver ID to assign to the orders.
     * @return bool True if successful, false otherwise.
     */
    public static function assignDriverToOrders(array $orderIds, int $driverId): bool
    {
        DB::beginTransaction();

        try {
            // Update the driver_id for each order in the array
            foreach ($orderIds as $orderId) {
                $order = self::find($orderId);

                // Proceed if the order exists
                if ($order) {
                    $order->driver_id = $driverId;
                    $order->save();
                    AppLog::info('Order Assigned to driver', loggable: $order);
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
                    $inventory->commitQuantity(-$quantityToRemove, 'Canceled from order #' . $this->order_number);
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
    public function cancelProducts(array $products, string $reason = null): bool
    {
        DB::beginTransaction();

        try {
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
                if ($orderProduct->inventory) {
                    $orderProduct->inventory->commitQuantity(-$product['return_quantity'], 'Removed from order #' . $this->order_number);
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

    // relations
    public function products()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function removedProducts()
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

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
