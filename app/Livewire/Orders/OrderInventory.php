<?php

namespace App\Livewire\Orders;

use App\Models\Orders\Order;
use App\Models\Orders\OrderProduct;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;


class OrderInventory extends Component
{
    use WithPagination , AlertFrontEnd;
    public $page_title = '• Orders • Inventory';
    public $search;
    public $status;
    public $driver;
    public $zone;

    public $selectedOrders = [];
    public $selectedOrderProducts = [];

    #[Url]
    public $deliveryDate;
    public $Edited_deliveryDate;
    public $Edited_deliveryDate_sec;

    public function openFilteryDeliveryDate(){
        $this->Edited_deliveryDate_sec = true;
        $this->Edited_deliveryDate = $this->deliveryDate?->toDateString();
    }

    public function closeFilteryDeliveryDate(){
        $this->Edited_deliveryDate_sec = false;
        $this->Edited_deliveryDate = null;
    }

    public function setFilteryDeliveryDate(){
        $this->deliveryDate = Carbon::parse($this->Edited_deliveryDate);
        $this->closeFilteryDeliveryDate();
    }

    public function toggleReady($id){
        $orderProduct = OrderProduct::findOrFail($id);
        $this->authorize('update',$orderProduct->order);
        $res = $orderProduct->toggleReady();

        if ($res) {
            $this->mount();
            $this->alertSuccess('Product swtiched');
        }else{
            $this->alertFailed();
        }
    }

    public function mount()
    {
        $this->authorize('viewOrderInventory',Order::class);
        $this->deliveryDate = Carbon::tomorrow();
    }

    public function render()
    {
        $orders = Order::search(searchText: $this->search, deliveryDate: $this->deliveryDate,status:$this->status,driverId: $this->driver?->id ,zoneId:$this->zone?->id)->withTotalQuantity()->paginate(50);
        return view('livewire.orders.order-inventory',[
            'orders' => $orders
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'ordersInventory' => 'active']);
    }
}
