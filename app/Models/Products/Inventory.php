<?php

namespace App\Models\Products;

use App\Models\Users\AppLog;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'on_hand', 'committed', 'available'];

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
            $beforeAvailable = $this->available;
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

            // Record after quantity
            $afterAvailable = $this->available;

            // Create transaction log
            $transaction = Transaction::create([
                'inventory_id' => $this->id,
                'quantity' => $quantity, // Can be positive or negative
                'before' => $beforeAvailable,
                'after' => $afterAvailable,
                'remarks' => $remarks,
                'user_id' => Auth::user()->id,
            ]);

            // Log the action in AppLog
            AppLog::info('Transaction created.', loggable: $this->product);

            return $transaction;
        } catch (\Exception $e) {
            // Log error to AppLog
            AppLog::error('Inventory Update Failed', $e->getMessage(), loggable: $this->product);
            // return false;
            return $e;

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
                if ($quantity > $this->available) {
                    throw new \Exception('Not enough available stock to commit.');
                }
                $this->committed += $quantity;
                $this->on_hand -= $quantity; // Reduce on_hand since it's being committed
            } elseif ($quantity < 0) {
                // Uncommit stock (decrease committed, increase available, and increase on_hand)
                $uncommitQty = abs($quantity); // convert negative quantity to positive for comparison
                if ($uncommitQty > $this->committed) {
                    throw new \Exception('Not enough committed stock to uncommit.');
                }
                $this->committed -= $uncommitQty;
                $this->on_hand += $uncommitQty; // Increase on_hand since stock is being uncommitted
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
                'quantity' => $quantity, // Can be positive or negative
                'before' => $beforeAvailable,
                'after' => $afterAvailable,
                'remarks' => $remarks,
                'user_id' => Auth::user()->id,
            ]);

            // Log the action in AppLog
            AppLog::info('Inventory commit transaction created.', loggable: $this->product);

            return $transaction;
        } catch (Exception $e) {
            // Log error to AppLog
            AppLog::error('Inventory Commit Failed', $e->getMessage(), loggable: $this->product);
            return false;
        }
    }

    // File: app/Models/Products/Inventory.php

    public static function initializeQuantity($product_id, $initial_quantity)
    {
        try {
            $inventory = new self();
            $inventory->product_id = $product_id; // Associate with the product
            $inventory->on_hand = $initial_quantity; // Set initial on hand quantity
            $inventory->committed = 0; // No committed quantity initially
            $inventory->available = $initial_quantity; // All quantity is available initially
            $inventory->save(); // Save the inventory record

            // Log the action
            AppLog::info('Initial quantity set for product ID ' . $product_id, loggable: $inventory);

            return $inventory; // Return the newly created inventory record
        } catch (Exception $e) {
            // Log error to AppLog
            AppLog::error('Failed to initialize quantity for product ID ' . $product_id, $e->getMessage());
            return null; // Indicate failure
        }
    }

    /**
     * Get the available inventory dynamically.
     *
     * @return int
     */

    /**
     * Get the unavailable inventory dynamically.
     *
     * @return int
     */
    public function getUnavailableAttribute()
    {
        return $this->on_hand - $this->available;
    }

    /**
     * Define the one-to-one relationship with the Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
