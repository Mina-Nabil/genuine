<?php

namespace App\Livewire\Orders;

use App\Models\Orders\Order;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\WithPagination;

class OrderIndex extends Component
{
    use  AlertFrontEnd, WithPagination;
    public $page_title = '• Orders';

    public $fetched_orders_IDs;
    public $search;
    public $selectAll = false; //to select all in the page
    public $selectedOrders = [];
    public $selectedAllOrders = false;

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedOrders = $this->fetched_orders_IDs;
        } else {
            $this->selectedOrders = [];
        }
    }

    public function selectAllOrders()
    {
        $this->selectedAllOrders = true;
        $this->selectedOrders = Order::pluck('id')->toArray();
    }

    public function undoSelectAllOrders()
    {
        $this->selectedAllOrders = false;
        $this->selectedOrders = $this->fetched_orders_IDs;
    }

    public function render()
    {
        $orders = Order::withTotalQuantity()->paginate(50);
        $this->fetched_orders_IDs = $orders->pluck('id')->toArray();
        return view('livewire.orders.order-index',[
            'orders' => $orders
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'orders' => 'active']);
    }
}
