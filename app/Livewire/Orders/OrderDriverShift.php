<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use App\Models\Orders\Order;
use App\Models\Orders\OrderProduct;
use App\Models\Users\Driver;
use App\Models\Users\User;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class OrderDriverShift extends Component
{
    use WithPagination, AlertFrontEnd;
    public $page_title = '• Driver Shift';
    public $search;
    public $status;
    public $driver;
    public $zone;

    public $deliveryDate;
    public $Edited_deliveryDate;
    public $Edited_deliveryDate_sec;

    public $setDriverSection = false;
    public $Edited_driverId;
    public $Edited_driverId_sec;
    public $DRIVERS;

    public $editedOrderNote;
    public $editedOrderNoteSec;
    public $editedDriverNote;
    public $editedDriverNoteSec;

    public function openEditOrderNote($order_id){
        $this->editedOrderNoteSec = $order_id;
        $this->editedOrderNote = Order::findOrFail($order_id)->note;
    }

    public function closeEditOrderNote(){
        $this->reset(['editedOrderNoteSec','editedOrderNote']);
    }

    public function  EditOrderNote(){
        $this->validate([
            'editedOrderNote' => 'nullable|string|max:255',
        ]);

        $res = Order::findOrFail($this->editedOrderNoteSec)->updateNote($this->editedOrderNote);

        if ($res) {
            $this->closeEditOrderNote();
            $this->alertSuccess('Note updated');
        }else{
            $this->alertFailed();
        }
    }

    public function openEditDriverNote($order_id){
        $this->editedDriverNoteSec = $order_id;
        $this->editedDriverNote = Order::findOrFail($order_id)->driver_note;
    }

    public function closeEditDriverNote(){
        $this->reset(['editedDriverNoteSec','editedDriverNote']);
    }

    public function  EditDriverNote(){
        $this->validate([
            'editedDriverNote' => 'nullable|string|max:255',
        ]);

        $res = Order::findOrFail($this->editedDriverNoteSec)->updateDriverNote($this->editedDriverNote);

        if ($res) {
            $this->closeEditDriverNote();
            $this->alertSuccess('Note updated');
        }else{
            $this->alertFailed();
        }
    }

    

    protected $queryString = ['deliveryDate'];

    public function openFilteryDeliveryDate()
    {
        $this->Edited_deliveryDate_sec = true;
        $this->Edited_deliveryDate = $this->deliveryDate?->toDateString();
    }

    public function closeFilteryDeliveryDate()
    {
        $this->Edited_deliveryDate_sec = false;
        $this->Edited_deliveryDate = null;
    }

    public function setFilteryDeliveryDate()
    {
        $this->deliveryDate = Carbon::parse($this->Edited_deliveryDate);
        $this->closeFilteryDeliveryDate();
    }

    public function mount()
    {
        $this->deliveryDate = Carbon::today();
        // dd(auth()->id());
        if (Auth::user()->type === User::TYPE_DRIVER) {
            $this->driver = Driver::getDriverWithMostOrders($this->deliveryDate, auth()->id());
        } else {
            $this->driver = Driver::getDriverWithMostOrders($this->deliveryDate);
        }
    }

    public function ChangeDriverShift($id)
    {
        $shiftsIDS = Auth::user()->drivers()->pluck('id')->toArray();

        if (in_array($id, $shiftsIDS)) {
            $this->driver = Driver::findOrFail($id);
        }
    }

    public function openFilteryDriver(){
        $this->Edited_driverId_sec = true;
        $this->Edited_driverId = $this->driver?->id;
        $this->DRIVERS = Driver::all();
    }

    public function closeFilteryDriver(){
        $this->Edited_driverId_sec = false;
        $this->Edited_driverId = null;
        $this->DRIVERS = null;
    }

    public function setFilterDriver(){
        $this->driver = Driver::findOrFail($this->Edited_driverId);
        $this->closeFilteryDriver();
    }

    public function render()
    {
        $orders = Order::search(searchText: $this->search, deliveryDate: $this->deliveryDate, status: $this->status, driverId: $this->driver?->id, zoneId: $this->zone?->id)->confirmed()->openOrders()->withTotalQuantity()->sortByZone()->paginate(50);

        $totalZones = Order::getTotalZonesForOrders($orders);

        return view('livewire.orders.order-driver-shift', [
            'orders' => $orders,
            'totalZones' => $totalZones,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'driverShift' => 'active']);
    }
}
