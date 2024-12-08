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
        return true;
    }

    public function viewReports(User $user): bool
    {
        return $user->is_admin;
    }

    public function viewOrderInventory(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        return true;
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
        if (Auth::id() === 1 && $order->is_new) {
            foreach ($order->products as $product) {
                if (!$product->is_ready) continue; else return false;
            }
            return true;
        }else{
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
