<?php

namespace App\Livewire\Orders;

use App\Models\Orders\Order;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class OrderInventory extends Component
{
    use WithPagination , AlertFrontEnd;
    public $page_title = '• Orders • Inventory';
    public $search;
    public $deliveryDate;
    public $status;
    public $driver;
    public $zone;

    public $selectedOrders = [];
    public $selectedOrderProducts = [];

    public function mount()
    {
        $this->deliveryDate = Carbon::tomorrow();
    }

    public function render()
    {
        $orders = Order::search(searchText: $this->search, deliveryDate: $this->deliveryDate,status:$this->status,driverId: $this->driver?->id ,zoneId:$this->zone?->id)->withTotalQuantity()->paginate(50);
        return view('livewire.orders.order-inventory',[
            'orders' => $orders
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'orders' => 'active']);
    }
}
