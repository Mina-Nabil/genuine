<?php

namespace App\Livewire\Orders;

use App\Models\Orders\PeriodicOrder;
use Livewire\Component;
use Livewire\WithPagination;

class PeriodicOrderIndex extends Component
{
    use WithPagination;
    public $search;

    public $fetched_orders_IDs;
    public $selectAll = false; //to select all in the page
    public $selectedOrders = [];
    public $selectedOrdersStatus;
    public $selectedAllOrders = false;
    

    public function render()
    {
        
        $orders = PeriodicOrder::withTotalQuantity()->paginate(30);
        $this->fetched_orders_IDs = $orders->pluck('id')->toArray();

        return view('livewire.orders.periodic-order-index',[
            'orders' => $orders,
        ]);
    }
}
