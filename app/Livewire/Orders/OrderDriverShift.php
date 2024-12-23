<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use App\Models\Orders\Order;
use App\Models\Payments\CustomerPayment;
use App\Models\Users\Driver;
use App\Models\Users\User;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class OrderDriverShift extends Component
{
    use WithPagination, AlertFrontEnd;
    public $page_title = '• Driver Shift';
    public $search;
    public $status;
    public $driver;
    public $zone;

    public $deliveryDate = [];
    public $Edited_deliveryDate;
    public $Edited_deliveryDate_sec;
    public $selectedDeliveryDates = [];

    public $setDriverSection = false;
    public $Edited_driverId;
    public $Edited_driverId_sec;
    public $DRIVERS;

    public $editedOrderNote;
    public $editedOrderNoteSec;
    public $editedDriverNote;
    public $editedDriverNoteSec;

    public $driverOrder;
    public $showDriverOrderId;

    public function showDriverOrder($id){
        $this->showDriverOrderId = $id;
    }

    public function setDriverOrder($id){
        $res = Order::findOrFail($id)->moveToPosition($this->driverOrder);
        if ($res) {
            $this->alertSuccess('Order Changed');
            $this->showDriverOrderId = null;
        } else {
            $this->alertFailed();
        }
    }
    //collected
    public $collectedFromPaymentTypes = [];

    public function setDriverPaymentType($orderId, $method = null)
    {
        $res = Order::findOrFail($orderId)->updateDriverPaymentType($method ?? null);
        if ($res) {
            $this->alertSuccess('updated!');
        } else {
            $this->alertFailed();
        }
    }


    public function openEditOrderNote($order_id)
    {
        $this->editedOrderNoteSec = $order_id;
        $this->editedOrderNote = Order::findOrFail($order_id)->note;
    }

    public function closeEditOrderNote()
    {
        $this->reset(['editedOrderNoteSec', 'editedOrderNote']);
    }

    public function  EditOrderNote()
    {
        $this->validate([
            'editedOrderNote' => 'nullable|string|max:255',
        ]);

        $res = Order::findOrFail($this->editedOrderNoteSec)->updateNote($this->editedOrderNote);

        if ($res) {
            $this->closeEditOrderNote();
            $this->alertSuccess('Note updated');
        } else {
            $this->alertFailed();
        }
    }

    public function openEditDriverNote($order_id)
    {
        $this->editedDriverNoteSec = $order_id;
        $this->editedDriverNote = Order::findOrFail($order_id)->driver_note;
    }

    public function closeEditDriverNote()
    {
        $this->reset(['editedDriverNoteSec', 'editedDriverNote']);
    }

    public function  EditDriverNote()
    {
        $this->validate([
            'editedDriverNote' => 'nullable|string|max:255',
        ]);

        $res = Order::findOrFail($this->editedDriverNoteSec)->updateDriverNote($this->editedDriverNote);

        if ($res) {
            $this->closeEditDriverNote();
            $this->alertSuccess('Note updated');
        } else {
            $this->alertFailed();
        }
    }

    public function toggleIsDelivered($id)
    {
        $res = Order::findOrFail($id)->toggleIsDelivered();
        if ($res) {
            $this->alertSuccess('updated!');
        } else {
            $this->alertFailed();
        }
    }

    protected $queryString = ['deliveryDate'];

    public function updatedEditedDeliveryDate($value)
    {
        foreach ($this->selectedDeliveryDates as $date) {
            if ($date->toDateString() === $value) {
                return;
            }
        }
        $this->selectedDeliveryDates[] = Carbon::parse($value);
        $this->Edited_deliveryDate = null;
    }

    public function removeSelectedDate($index)
    {
        unset($this->selectedDeliveryDates[$index]);
        $this->selectedDeliveryDates = array_values($this->selectedDeliveryDates); // Reset array keys
    }

    public function clearDeliveryDate()
    {
        $this->deliveryDate = [];
    }

    public function openFilteryDeliveryDate()
    {
        $this->Edited_deliveryDate_sec = true;

        foreach ($this->deliveryDate as $date) {
            $this->selectedDeliveryDates[] = $date;
        }
    }

    public function closeFilteryDeliveryDate()
    {
        $this->Edited_deliveryDate_sec = false;
        $this->Edited_deliveryDate = null;
        $this->selectedDeliveryDates = [];
    }

    public function setFilteryDeliveryDate()
    {

        $this->deliveryDate = $this->selectedDeliveryDates;
        $this->closeFilteryDeliveryDate();
    }

    public function mount()
    {
        $this->deliveryDate = [Carbon::today()];
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

    public function openFilteryDriver()
    {
        $this->Edited_driverId_sec = true;
        $this->Edited_driverId = $this->driver?->id;
        $this->DRIVERS = Driver::all();
    }

    public function closeFilteryDriver()
    {
        $this->Edited_driverId_sec = false;
        $this->Edited_driverId = null;
        $this->DRIVERS = null;
    }

    public function setFilterDriver()
    {
        $this->driver = Driver::findOrFail($this->Edited_driverId);
        $this->closeFilteryDriver();
    }

    public function render()
    {
        $orders = Order::search(searchText: $this->search, deliveryDates: $this->deliveryDate, status: $this->status, driverId: $this->driver?->id, zoneId: $this->zone?->id)->confirmed()->openOrders()->withTotalQuantity()->orderByRaw('driver_order IS NULL, driver_order ASC')->sortByZone()->paginate(50);

        $totalZones = Order::getTotalZonesForOrders($orders);
        $PAYMENT_METHODS = CustomerPayment::PAYMENT_METHODS;

        $this->collectedFromPaymentTypes = [];
        foreach ($PAYMENT_METHODS as $PAYMENT_METHOD) {
            $this->collectedFromPaymentTypes[$PAYMENT_METHOD] = 0; // Initialize each payment type with 0
        }

        foreach ($orders as $order) {
            if (isset($this->collectedFromPaymentTypes[$order->driver_payment_type])) {
                $this->collectedFromPaymentTypes[$order->driver_payment_type] += $order->remaining_to_pay;
            }
        }

        // dd($this->collectedFromPaymentTypes);

        return view('livewire.orders.order-driver-shift', [
            'orders' => $orders,
            'totalZones' => $totalZones,
            'PAYMENT_METHODS' => $PAYMENT_METHODS
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'driverShift' => 'active']);
    }
}
