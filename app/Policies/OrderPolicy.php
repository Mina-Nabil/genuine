<?php

namespace App\Policies;

use App\Models\Orders\Order;
use App\Models\Users\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class OrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_admin || $user->is_sales || $user->is_driver;
    }

    public function viewSalesReports(User $user): bool
    {
        return $user->is_admin || $user->is_sales;
    }

    public function viewReports(User $user): bool
    {
        return $user->is_admin;
    }

    public function viewOrderInventory(User $user): bool
    {
        return $user->is_admin || $user->is_inventory;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        return true;
    }

    public function resetStatus(User $user, Order $order): bool
    {
        return $user->is_admin;
    }

    public function canChangeDriver(User $user, Order $order): bool
    {
        return $order->is_new || $order->is_ready || ($user->is_admin && $order->is_in_delivery);
    }

    public function updateDeliveryInfo(User $user, Order $order): bool
    {
        return $order->is_new || $order->is_ready;
    }

    public function updateCustomer(User $user, Order $order): bool
    {
        return $user->is_admin && ($order->payments->count() === 0 && $order->balanceTransactions->count() === 0 && ($order->is_new || $order->is_ready || $order->is_in_delivery) && $order->is_paid === 0);
    }

    public function rescheduleOrder(User $user, Order $order): bool
    {
        return $order->is_in_delivery && $user->is_admin;
    }

    public function updateConfirm(User $user, Order $order): bool
    {
        return ($user->is_sales && $order->driver_id != null) || $user->is_admin;
    }

    public function updateDeliveryPrice(User $user, Order $order): bool
    {
        return $user->is_admin;
    }

    public function cancelOrder(User $user, Order $order = null): bool
    {
        return $user->is_admin;
    }

    public function updateDiscount(User $user, Order $order): bool
    {
        return $user->id == 1;
    }

    public function updateInventoryInfo(User $user, Order $order = null): bool
    {
        return $user->is_admin || $user->is_inventory;
    }

    public function updateDriverNote(User $user, Order $order): bool
    {
        return $user->is_driver;
    }

    public function updateOrderNote(User $user, Order $order): bool
    {
        return !$user->is_driver;
    }

    public function pay(User $user, Order $order): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Order $order): bool
    {
        return true;
    }

    /**
     * Determine whether the user can return products.
     */
    public function returnProducts(User $user, Order $order): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $order): bool
    {
        if ((Auth::id() === 1 || Auth::id() === 2) && $order->is_new) {
            foreach ($order->products as $product) {
                if (!$product->is_ready) {
                    continue;
                } else {
                    return false;
                }
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Order $order): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        return true;
    }
}
