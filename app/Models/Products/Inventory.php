<?php

namespace App\Models\Products;

use App\Models\Users\AppLog;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Inventory extends Model
{
    use HasFactory;

    const MORPH_TYPE = 'inventory';

    protected $fillable = ['on_hand', 'committed', 'available', 'inventoryable_id', 'inventoryable_type'];

    /**
     * Add a transaction and update inventory based on quantity (positive for addition, negative for subtraction).
     *
     * @param int $quantity (positive for addition, negative for subtraction)
     * @param string|null $remarks
     * @return \App\Models\Transaction
     * @throws \Exception
     */
    public function addTransaction($quantity, $remarks = null)
    {
        /** @var User */
        $user = Auth::user();
        if (!$user->can('update', $this)) {
            return false;
        }

        try {
            // Record before quantity
            $beforeOnHand = $this->on_hand;

            // Update the stock based on positive or negative quantity
            if ($quantity > 0) {
                // Add to stock
                $this->on_hand += $quantity;
            } elseif ($quantity < 0) {
                // Subtract from stock
                $subtractionQty = abs($quantity); // get absolute value
                if ($subtractionQty > $this->on_hand) {
                    throw new \Exception('Not enough stock to subtract.');
                }
                $this->on_hand -= $subtractionQty;
            } else {
                throw new \Exception('Quantity cannot be zero.');
            }

            // Update the 'available' stock after adjusting 'on_hand'
            $this->available = $this->on_hand - $this->committed;

            // Save the inventory changes
            $this->save();

            // Create transaction log
            $transaction = Transaction::create([
                'inventory_id' => $this->id,
                'quantity' => $quantity, // Can be positive or negative
                'before' => $beforeOnHand,
                'after' => $beforeOnHand + $quantity,
                'remarks' => $remarks,
                'user_id' => Auth::user()->id,
            ]);

            // Log the action in AppLog
            AppLog::info('Transaction created.', loggable: $this->inventoryable);

            return $transaction;
        } catch (\Exception $e) {
            // Log error to AppLog
            AppLog::error('Inventory Update Failed', $e->getMessage(), loggable: $this->inventoryable);
            return $e;
        }
    }

    /**
     * Update on-hand inventory to a new value and adjust available stock.
     *
     * @param int $newOnHand The new value for on-hand inventory.
     * @param string|null $remarks Optional remarks for the transaction.
     * @return \App\Models\Transaction
     * @throws \Exception
     */
    public function updateOnHandWithNewValue($newOnHand, $remarks = null)
    {
        /** @var User */
        $user = Auth::user();
        if (!$user->can('update', $this)) {
            return false;
        }

        try {
            // Record the old quantity before updating
            $beforeAvailable = $this->available;
            $beforeOnHand = $this->on_hand;

            // Update the 'on_hand' inventory to the new value
            $this->on_hand = $newOnHand;

            // Update the 'available' stock after adjusting 'on_hand'
            $this->available = $this->on_hand - $this->committed;

            // Save the inventory changes
            $this->save();

            // Record the after value for available stock
            $afterAvailable = $this->available;

            // Create transaction log to track the change
            $transaction = Transaction::create([
                'inventory_id' => $this->id,
                'quantity' => $newOnHand - $beforeOnHand, // This reflects the difference (positive or negative)
                'before' => $beforeAvailable,
                'after' => $afterAvailable,
                'remarks' => $remarks,
                'user_id' => Auth::user()->id,
            ]);

            // Log the action in AppLog
            AppLog::info('Inventory updated with new on_hand value.', loggable: $this->inventoryable);

            return $transaction;
        } catch (\Exception $e) {
            report($e);
            AppLog::error('Inventory Update Failed', $e->getMessage(), loggable: $this->inventoryable);
            return $e;
        }
    }

    /**
     * Permanently remove committed quantity, reducing both committed and on_hand stock.
     *
     * @param int $quantity The amount to reduce from committed and on_hand stock.
     * @param string|null $remarks Optional remarks for the transaction.
     * @return \App\Models\Transaction|bool Transaction object on success, false on failure.
     * @throws \Exception
     */
    public function removeCommit($quantity, $remarks = null)
    {
        /** @var User */
        $user = Auth::user();
        if (!$user->can('update', $this)) {
            return false;
        }

        try {
            // Ensure quantity is positive for removing from committed and on_hand
            if ($quantity <= 0) {
                throw new \Exception('Quantity must be a positive number.');
            }

            // Check if sufficient committed stock is available to remove
            if ($quantity > $this->committed) {
                throw new \Exception('Not enough committed stock to remove.');
            }

            // Record before quantities for committed and on_hand
            $beforeCommitted = $this->committed;
            $beforeOnHand = $this->on_hand;

            // Decrease the committed quantity and the on_hand quantity
            $this->committed -= $quantity;
            // $this->on_hand -= $quantity;

            // Update available stock
            $this->available = $this->on_hand - $this->committed;

            // Save the updated stock values
            $this->save();

            // Record after values for committed and on_hand
            $afterCommitted = $this->committed;
            $afterOnHand = $this->on_hand;

            // Log the transaction
            $transaction = Transaction::create([
                'inventory_id' => $this->id,
                'quantity' => -$quantity,
                'before' => $this->available,
                'after' => $this->available,
                'remarks' => $remarks,
                'user_id' => Auth::user()->id,
            ]);

            // Log the action in AppLog
            AppLog::info('Inventory remove commit transaction created.', loggable: $this->inventoryable);

            return $transaction;
        } catch (Exception $e) {
            // Log error to AppLog
            AppLog::error('Inventory Remove Commit Failed', $e->getMessage(), loggable: $this->inventoryable);
            return false;
        }
    }

    /**
     * Fulfill a committed quantity by reducing it from committed and on-hand stock.
     *
     * @param int $quantity The quantity to fulfill.
     * @return \App\Models\Transaction|bool Transaction object on success, false on failure.
     * @throws \Exception
     */
    public function fulfillCommit($quantity)
    {
        /** @var User */
        $user = Auth::user();
        if (!$user->can('update', $this)) {
            return false;
        }

        try {
            DB::transaction(function () use ($quantity) {
                // Ensure the quantity is positive
                if ($quantity <= 0) {
                    throw new \Exception('Quantity must be a positive number.');
                }

                // Check if sufficient committed stock is available
                if ($this->on_hand - $quantity < 0) {
                    throw new \Exception('Not enough committed stock to fulfill.');
                }

                // Reduce committed and on-hand quantities
                $this->committed -= $quantity;
                $this->on_hand -= $quantity;

                // Update available stock
                $this->available = $this->on_hand - $this->committed;

                // Save the inventory changes
                $this->save();

                // Log the action in AppLog
            });
            AppLog::info('Inventory fulfill commit', loggable: $this->inventoryable);
            return true;
        } catch (\Exception $e) {
            report($e);
            AppLog::error('Inventory Fulfill Commit Failed', $e->getMessage(), loggable: $this->inventoryable);
            return false;
        }
    }

    public function unfulfillCommit($quantity)
    {
        /** @var User */
        $user = Auth::user();
        if (!$user->can('update', $this)) {
            return false;
        }

        try {
            DB::transaction(function () use ($quantity) {
                // Ensure the quantity is positive
                if ($quantity <= 0) {
                    throw new \Exception('Quantity must be a positive number.');
                }

                // Increase committed and on-hand quantities
                $this->committed += $quantity;
                $this->on_hand += $quantity;

                // Update available stock
                $this->available = $this->on_hand - $this->committed;

                // Save the inventory changes
                $this->save();

                // Log the action in AppLog
            });
            AppLog::info('Inventory unfulfill commit', loggable: $this->inventoryable);
            return true;
        } catch (\Exception $e) {
            report($e);
            AppLog::error('Inventory Unfulfill Commit Failed', $e->getMessage(), loggable: $this->inventoryable);
            return false;
        }
    }

    /**
     * Commit or uncommit inventory and update available stock.
     *
     * @param int $quantity (positive to commit, negative to uncommit)
     * @param string|null $remarks
     * @return \App\Models\Transaction
     * @throws \Exception
     */
    public function commitQuantity($quantity, $remarks = null)
    {
        /** @var User */
        $user = Auth::user();
        if (!$user->can('update', $this)) {
            return false;
        }

        try {
            // Record before quantities
            $beforeAvailable = $this->available;

            // Handle positive and negative quantities
            if ($quantity > 0) {
                // Commit stock (increase committed, reduce available, and reduce on_hand)
                // if ($quantity > $this->available) {
                //     throw new \Exception('Not enough available stock to commit.');
                // }
                $this->committed += $quantity;
                // $this->on_hand -= $quantity; // Reduce on_hand since it's being committed
            } elseif ($quantity < 0) {
                // Uncommit stock (decrease committed, increase available, and increase on_hand)
                $uncommitQty = abs($quantity); // convert negative quantity to positive for comparison
                if ($uncommitQty > $this->committed) {
                    throw new \Exception('Not enough committed stock to uncommit.');
                }
                $this->committed -= $uncommitQty;
            } else {
                throw new \Exception('Quantity cannot be zero.');
            }

            // Update the 'available' stock after adjusting 'committed' and 'on_hand'
            $this->available = $this->on_hand - $this->committed;

            // Save the inventory changes
            $this->save();

            // Record after quantities
            $afterAvailable = $this->available;

            // Create transaction log
            $transaction = Transaction::create([
                'inventory_id' => $this->id,
                'quantity' => -$quantity, // Can be positive or negative
                'before' => $beforeAvailable,
                'after' => $afterAvailable,
                'remarks' => $remarks,
                'user_id' => Auth::user()->id,
            ]);

            // Log the action in AppLog
            AppLog::info('Inventory commit transaction created.', loggable: $this->inventoryable);

            return $transaction;
        } catch (Exception $e) {
            // Log error to AppLog
            AppLog::error('Inventory Commit Failed', $e->getMessage(), loggable: $this->inventoryable);
            return false;
        }
    }

    /**
     * Initialize the quantity for a product or any inventoryable item.
     *
     * @param Model $inventoryable
     * @param int $initial_quantity
     * @return Inventory|null
     */
    public static function initializeQuantity($inventoryable, $initial_quantity)
    {
        try {
            $inventory = new self();
            $inventory->inventoryable()->associate($inventoryable); // Associate with the polymorphic model
            $inventory->on_hand = $initial_quantity; // Set initial on hand quantity
            $inventory->committed = 0; // No committed quantity initially
            $inventory->available = $initial_quantity; // All quantity is available initially
            $inventory->save(); // Save the inventory record

            // Log the action
            AppLog::info('Initial quantity set for inventoryable ID ' . $inventoryable->id, loggable: $inventory);

            return $inventory; // Return the newly created inventory record
        } catch (Exception $e) {
            // Log error to AppLog
            report($e);
            AppLog::error('Failed to initialize quantity for inventoryable', $e->getMessage());
            return null; // Indicate failure
        }
    }

    /**
     * Remove a transaction and update inventory based on quantity (positive for addition, negative for subtraction).
     *
     * @param int $quantity (positive for addition, negative for subtraction)
     * @param string|null $remarks
     * @return \App\Models\Transaction
     * @throws \Exception
     */
    public function removeQuantity($quantity, $remarks = null)
    {
        /** @var User */
        $user = Auth::user();
        if (!$user->can('update', $this)) {
            return false;
        }

        try {
            // Record before quantity
            $beforeAvailable = $this->available;
            $beforeOnHand = $this->on_hand;

            // Update the stock based on positive or negative quantity
            if ($quantity > 0) {
                // Subtract from stock
                if ($quantity > $this->on_hand) {
                    throw new \Exception('Not enough stock to subtract.');
                }
                $this->on_hand -= $quantity;
            } elseif ($quantity < 0) {
                // Add to stock
                $this->on_hand += abs($quantity);
            } else {
                throw new \Exception('Quantity cannot be zero.');
            }

            // Update the 'available' stock after adjusting 'on_hand'
            $this->available = $this->on_hand - $this->committed;

            // Save the inventory changes
            $this->save();

            // Record after quantity
            $afterAvailable = $this->available;

            // Create transaction log
            $transaction = Transaction::create([
                'inventory_id' => $this->id,
                'quantity' => -$quantity, // Can be positive or negative
                'before' => $beforeAvailable,
                'after' => $afterAvailable,
                'remarks' => $remarks,
                'user_id' => Auth::user()->id,
                'created_at' => now()->format('Y-m-d H:i')
            ]);

            // Log the action in AppLog
            AppLog::info('Qyanity '.$quantity.' removed.', loggable: $this->inventoryable);

            return $transaction;
        } catch (\Exception $e) {
            // Log error to AppLog
            AppLog::error('Inventory Update Failed', $e->getMessage(), loggable: $this->inventoryable);
            return $e;
        }
    }

    public function scopeSearch($query, $searchTerm = null)
    {
        if (!is_null($searchTerm)) {
            return $query->whereHas('inventoryable', function ($query) use ($searchTerm) {
                if (method_exists($query->getModel(), 'scopeSearch')) {
                    $query->search($searchTerm); // Calls Product::scopeSearch
                }
            });
        }

        return $query;
    }

    public function scopeSortBy($query, $column = null, $direction = 'asc')
    {
        // Ensure the column is not null and exists in the table
        if ($column && in_array($column, \Illuminate\Support\Facades\Schema::getColumnListing($this->getTable()))) {
            return $query->orderBy($column, $direction);
        }

        // If column is not valid, just return the query without sorting
        return $query;
    }

    /**
     * Get the unavailable inventory dynamically.
     *
     * @return int
     */
    public function getUnavailableAttribute()
    {
        return $this->on_hand - $this->available;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Define the polymorphic relationship with the parent model (product or others).
     */
    public function inventoryable()
    {
        return $this->morphTo();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
