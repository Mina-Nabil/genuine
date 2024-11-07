<?php

namespace App\Livewire\Orders;

use App\Models\Customers\Zone;
use App\Models\Orders\Order;
use App\Models\Users\Driver;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class OrderIndex extends Component
{
    use AlertFrontEnd, WithPagination;
    public $page_title = '• Orders';

    public $fetched_orders_IDs;
    public $search;
    public $selectAll = false; //to select all in the page
    public $selectedOrders = [];
    public $selectedOrdersStatus;
    public $selectedAllOrders = false;

    //bulk actions
    public $setDriverSection = false;
    public $driverId;
    public $setDeliveryDateSection = false;
    public $bulkDeliveryDate;
    public $availableBulkStatuses = [];

    //Filter
    #[Url]
    public $status;
    public $Edited_status;
    public $Edited_status_sec;

    #[Url]
    public $driver;
    public $Edited_driverId;
    public $Edited_driverId_sec;

    #[Url]
    public $zone;
    public $Edited_zoneId;
    public $Edited_zoneId_sec;

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

    public function openFilteryStatus(){
        $this->Edited_status_sec = true;
        $this->Edited_status = $this->status;
    }

    public function closeFilteryStatus(){
        $this->Edited_status_sec = false;
        $this->Edited_status = null;
    }

    public function setFilterStatus(){
        $this->status = $this->Edited_status;
        $this->closeFilteryStatus();
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

    public function openFilteryZone(){
        $this->Edited_zoneId_sec = true;
        $this->Edited_zoneId = $this->zone?->id;
    }

    public function closeFilteryZone(){
        $this->Edited_zoneId_sec = false;
        $this->Edited_zoneId = null;
    }

    public function setFilterZone(){
        $this->zone = Zone::findOrFail($this->Edited_zoneId);
        $this->closeFilteryZone();
    }

    public function mount()
    {
        $this->deliveryDate = Carbon::tomorrow();

    }

    public function clearProperty(string $propertyName)
    {
        // Check if the property exists before attempting to clear it
        if (property_exists($this, $propertyName)) {
            $this->$propertyName = null;
        }
    }



    public function setBulkStatus($status)
    {
        $res = Order::setBulkStatus($this->selectedOrders, $status);
        if ($res) {
            $this->resetPage();
            $this->alertSuccess('Status changed!');
        } else {
            $this->alertFailed();
        }
    }

    public function openSetDeliveryDate()
    {
        $this->setDeliveryDateSection = true;
    }

    public function updatedSelectedOrders()
    {
        $res = Order::checkStatusConsistency($this->selectedOrders);
        if ($res) {
            $this->availableBulkStatuses = Order::getNextStatuses($res);
        } else {
            $this->availableBulkStatuses = [];
        }
    }

    public function closeSetDeliveryDate()
    {
        $this->reset(['setDeliveryDateSection', 'bulkDeliveryDate']);
    }

    public function setDeliveryDate()
    {
        $this->validate([
            'bulkDeliveryDate' => 'required|date',
        ]);

        $res = Order::setDeliveryDateForOrders($this->selectedOrders, Carbon::parse($this->bulkDeliveryDate));

        if ($res) {
            $this->resetPage();
            $this->closeSetDeliveryDate();
            $this->alertSuccess('Delivery date set!');
        } else {
            $this->alertFailed();
        }
    }

    public function openSetDriver()
    {
        $this->setDriverSection = true;
    }

    public function closeSetDriver()
    {
        $this->reset(['setDriverSection', 'driverId']);
    }

    public function setDriver()
    {
        $this->validate([
            'driverId' => 'required|exists:drivers,id',
        ]);
        $res = Order::assignDriverToOrders($this->selectedOrders, $this->driverId);

        if ($res) {
            $this->resetPage();
            $this->closeSetDriver();
            $this->alertSuccess('Driver set!');
        } else {
            $this->alertFailed();
        }
    }

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
        $orders = Order::search(searchText: $this->search, deliveryDate: $this->deliveryDate,status:$this->status,driverId: $this->driver?->id ,zoneId:$this->zone?->id)->withTotalQuantity()->paginate(50);

        $totalWeight = 0;
        foreach($orders as $order){
            $totalWeight = $totalWeight + $order->total_weight;
        }

        $totalZones = Order::getTotalZonesForOrders($orders);
        $ordersCount = count($orders);

        $DRIVERS = Driver::all();
        $ZONES = Zone::all();
        $STATUSES = Order::STATUSES; 
        $drivers = Driver::all();
        
        $this->fetched_orders_IDs = $orders->pluck('id')->toArray();
        return view('livewire.orders.order-index', [
            'orders' => $orders,
            'drivers' => $drivers,
            'STATUSES' => $STATUSES,
            'DRIVERS' => $DRIVERS,
            'ZONES' => $ZONES,
            'totalWeight' => $totalWeight,
            'totalZones' => $totalZones,
            'ordersCount' => $ordersCount
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'orders' => 'active']);
    }
}
