<?php

namespace App\Livewire\Orders;

use App\Models\Orders\Order;
use App\Models\Orders\OrderProduct;
use App\Models\Users\Driver;
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

    public $Edited_driverId;
    public $Edited_driverId_sec;

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

    public function openFilteryDriver(){
        $this->Edited_driverId_sec = true;
        $this->Edited_driverId = $this->driver?->id;
    }

    public function closeFilteryDriver(){
        $this->Edited_driverId_sec = false;
        $this->Edited_driverId = null;
    }

    public function setFilterDriver(){
        $this->driver = Driver::findOrFail($this->Edited_driverId);
        $this->closeFilteryDriver();
    }

    public function clearProperty(string $propertyName)
    {
        // Check if the property exists before attempting to clear it
        if (property_exists($this, $propertyName)) {
            $this->$propertyName = null;
        }
    }

    public function setAsInDelivery($order_id){
        $res = Order::findOrFail($order_id)->setStatus(Order::STATUS_IN_DELIVERY);
        if ($res) {
            $this->alertSuccess('Order delivered');
        }else{
            $this->alertFailed();
        }
    }

    public function toggleReady($id){
        $orderProduct = OrderProduct::findOrFail($id);
        $this->authorize('update',$orderProduct->order);
        $res = $orderProduct->toggleReady();

        if ($res) {
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
        $DRIVERS = Driver::all();
        $orders = Order::search(searchText: $this->search, deliveryDate: $this->deliveryDate,status:$this->status,driverId: $this->driver?->id ,zoneId:$this->zone?->id)->withTotalQuantity()->paginate(50);
        return view('livewire.orders.order-inventory',[
            'orders' => $orders,
            'DRIVERS' => $DRIVERS,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'ordersInventory' => 'active']);
    }
}
