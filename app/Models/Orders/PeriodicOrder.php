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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class PeriodicOrder extends Model
{
    use HasFactory, SoftDeletes;

    const MORPH_TYPE = 'periodic_order';

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

    public function updateShippingDetails(string $customerName, string $shippingAddress, string $locationUrl = null, string $customerPhone, int $zoneId): bool
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
            $this->location_url = $locationUrl;
            $this->customer_phone = $customerPhone;
            $this->zone_id = $zoneId;

            $this->save();

            AppLog::info('Shipping details updated.', loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to update shipping details for order', $e->getMessage());
            return false;
        }
    }

    public function updatePeriodicDetails(string $periodicOption, ?int $orderDay = null): bool
    {
        /** @var User */
        $loggedInUser = Auth::user();

        if ($loggedInUser && !$loggedInUser->can('update', $this)) {
            return false;
        }

        try {
            if (!in_array($periodicOption, self::PERIODIC_OPTIONS)) {
                throw new Exception('Invalid periodic option provided.');
            }

            $this->periodic_option = $periodicOption;
            $this->order_day = $orderDay;
            $this->save();

            AppLog::info('Periodic details updated.', loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e); // Log the exception for debugging
            AppLog::error('Failed to update periodic details', ['error' => $e->getMessage()]);
            return false;
        }
    }

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
                    PeriodicOrderProduct::create([
                        'periodic_order_id' => $this->id,
                        'product_id' => $product['product_id'],
                        'quantity' => $product['quantity'],
                        'price' => $product['price'],
                        'combo_id' => $product['combo_id'] ?? null,
                        'note' => $note,
                    ]);
                }
            }

            DB::commit();

            AppLog::info('Products added', loggable: $this);
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            AppLog::error('Failed to add products to order', $e->getMessage());
            return false;
        }
    }

    public function addComment(string $comment): void
    {
        AppLog::comment($comment, $desc = null, loggable: $this);
    }

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
            if (!$orderName) {
                $lastOrder = PeriodicOrder::where('customer_id', $customerId)->orderBy('created_at', 'desc')->first();

                if ($lastOrder) {
                    $lastOrderName = $lastOrder->order_name;
                    $lastLetter = substr($lastOrderName, -1);

                    if (ctype_alpha($lastLetter)) {
                        $newLetter = chr(ord($lastLetter) + 1);
                    } else {
                        $newLetter = 'A';
                    }
                    $orderName = substr($lastOrderName, 0, -1) . $newLetter;
                } else {
                    $orderName = 'PO' . $customerId . '-A';
                }
            }

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

    public function getTitleAttribute()
    {
        if ($this->order_name) {
            return $this->order_name;
        } else {
            $orderIndex = $this->customer->periodicOrders()->orderBy('created_at', 'asc')->get()->search(fn($order) => $order->id === $this->id) + 1;

            return 'PO' . $this->customer->id . '-' . $orderIndex;
        }
    }

    public function scopeSearch(Builder $query, string $searchText = null, int $zoneId = null): Builder
    {
        return $query
            ->when($searchText, function ($query, $searchText) {
                $query->where(function ($q) use ($searchText) {
                    $q->where('order_name', 'like', '%' . $searchText . '%')
                        ->orWhere('customer_name', 'like', '%' . $searchText . '%')
                        ->orWhere('customer_phone', 'like', '%' . $searchText . '%')
                        ->orWhere('shipping_address', 'like', '%' . $searchText . '%');
                });
            })
            ->when($zoneId, function ($query, $zoneId) {
                $query->where('zone_id', $zoneId);
            });
    }

    /**
     * Relations.
     */

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(AppLog::class, 'loggable_id')->where('loggable_type', self::MORPH_TYPE);
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
