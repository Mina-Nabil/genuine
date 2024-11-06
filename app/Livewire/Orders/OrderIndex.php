<?php

namespace App\Livewire\Orders;

use App\Models\Orders\Order;
use App\Models\Users\Driver;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
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
    public $selectedOrdersStatus;
    public $selectedAllOrders = false;

    //bulk actions
    public $setDriverSection  = false;
    public $driverId;
    public $setDeliveryDateSection  = false;
    public $bulkDeliveryDate;
    public $availableBulkStatuses =  [];

    public function setBulkStatus($status){
        $res = Order::setBulkStatus($this->selectedOrders,$status);
        if ($res) {
            $this->resetPage();
            $this->alertSuccess('Status changed!');
        }else{
            $this->alertFailed();
        }
    }

    public function openSetDeliveryDate(){
        $this->setDeliveryDateSection = true;
    }

    public function updatedSelectedOrders(){
        $res = Order::checkStatusConsistency($this->selectedOrders);
        if ($res) {
            $this->availableBulkStatuses = Order::getNextStatuses($res);
        }else{
            $this->availableBulkStatuses = [];
        }
    }

    public function closeSetDeliveryDate(){
        $this->reset(['setDeliveryDateSection' ,'bulkDeliveryDate' ]);
    }

    public function setDeliveryDate(){
        $this->validate([
            'bulkDeliveryDate' => 'required|date',
        ]);

        $res = Order::setDeliveryDateForOrders($this->selectedOrders,Carbon::parse($this->bulkDeliveryDate));

        if ($res) {
            $this->resetPage();
            $this->closeSetDeliveryDate();
            $this->alertSuccess('Delivery date set!');
        }else{
            $this->alertFailed();
        }
    }

    
    public function openSetDriver(){
        $this->setDriverSection = true;
    }

    public function closeSetDriver(){
        $this->reset(['setDriverSection' ,'driverId' ]);
    }


    public function setDriver(){
        $this->validate([
            'driverId' => 'required|exists:drivers,id',
        ]);
        $res = Order::assignDriverToOrders($this->selectedOrders,$this->driverId);

        if ($res) {
            $this->resetPage();
            $this->closeSetDriver();
            $this->alertSuccess('Driver set!');
        }else{
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
        $drivers = Driver::all();
        $orders = Order::withTotalQuantity()->paginate(50);
        $this->fetched_orders_IDs = $orders->pluck('id')->toArray();
        return view('livewire.orders.order-index',[
            'orders' => $orders,
            'drivers' => $drivers
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'orders' => 'active']);
    }
}
