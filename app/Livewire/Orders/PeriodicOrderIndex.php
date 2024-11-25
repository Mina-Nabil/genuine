<?php

namespace App\Livewire\Orders;

use App\Models\Orders\PeriodicOrder;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\WithPagination;

class PeriodicOrderIndex extends Component
{
    use WithPagination , AlertFrontEnd;
    public $search;

    public $fetched_orders_IDs;
    public $selectAll = false; //to select all in the page
    public $selectedOrders = [];
    public $selectedOrdersStatus;
    public $selectedAllOrders = false;
    
    public function mount(){
        $this->authorize('viewAny',PeriodicOrder::class);
    }

    public function render()
    {
        
        $orders = PeriodicOrder::search($this->search)->withTotalQuantity()->paginate(30);
        $this->fetched_orders_IDs = $orders->pluck('id')->toArray();

        return view('livewire.orders.periodic-order-index',[
            'orders' => $orders,
        ])->layout('layouts.app', ['page_title' => 'Periodic Orders', 'ordersPeriodic' => 'active']);
    }
}
