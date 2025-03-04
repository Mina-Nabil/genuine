<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use App\Models\Orders\Order;
use App\Models\Payments\CustomerPayment;
use App\Models\RouteNav;
use App\Models\Users\Driver;
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
    public $zone;

    #[Url]
    public $driverId;
    public $driver;

    #[Url]
    public $deliveryDate;
    // public $Edited_deliveryDate;
    // public $Edited_deliveryDate_sec;
    // public $selectedDeliveryDates = [];

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
    public $showBagsId;
    public $expandedId;
    public $noOfBags = [];

    public $startLocation;
    public $endLocation;

    public $showRoutePlanModal = false;
    public $selectedStartLocation = 'current';
    public $selectedDestination = '';

    public function updateNoOfBags($id)
    {
        $res = Order::findOrFail($id)->updateNoOfBags(!is_numeric($this->noOfBags[$id]) ? 0 : $this->noOfBags[$id]);
        if ($res) {
            $this->alertSuccess('Order Changed');
            $this->showBagsId = null;
        } else {
            $this->alertFailed();
        }
    }

    public function setExpandedId($id = null)
    {
        $this->expandedId = $id;
    }

    public function showDriverOrder($id)
    {
        $this->showDriverOrderId = $id;
    }

    public function showBags($id)
    {
        $this->showBagsId = $id;
    }

    public function setDriverOrder($id)
    {
        $res = Order::findOrFail($id)->moveToPosition(!is_numeric($this->driverOrder) ? NULL : $this->driverOrder);
        if ($res) {
            $this->alertSuccess('Order Changed');
            $this->showDriverOrderId = null;
        } else {
            $this->alertFailed();
        }
        $this->driverOrder = null;
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

    public function mount()
    {

        if ($this->deliveryDate) {
            // $this->deliveryDate = Carbon::parse($this->deliveryDate);
        } else {
            $this->deliveryDate = Carbon::today()->format('Y-m-d');
        }

        if (Auth::user()->is_driver) {
            $this->driver = Driver::getDriverWithMostOrders($this->deliveryDate, Auth::id());
            $this->driverId = $this->driver->id;
            if (!$this->driver) $this->driver = Driver::byUserID(Auth::id())->first();
        } else {
            if ($this->driverId) {
                $this->driver = Driver::find($this->driverId);
            } else {
                $this->driver = Driver::first();
                $this->driverId = $this->driver->id;
            }
        }
        $orders = Order::shift($this->driver?->id, $this->deliveryDate)->paginate(50);
        foreach ($orders as $order) {
            $this->noOfBags[$order->id] = $order->no_of_bags;
        }
    }

    public function getRoute()
    {
        if($this->startLocation == 'factory' || !$this->startLocation){
            $this->startLocation = env('FACTORY_LOCATION');
        }

        if($this->endLocation == 'factory' || !$this->endLocation){
            $this->endLocation = env('FACTORY_LOCATION');
        }

        $route = RouteNav::getBestRoute($this->driverId, Carbon::parse($this->deliveryDate), $this->startLocation, $this->endLocation);
     
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
        $this->driverId = $this->Edited_driverId;
        $this->closeFilteryDriver();
    }

    public function openRoutePlanModal()
    {
        $this->showRoutePlanModal = true;
    }

    public function closeRoutePlanModal()
    {
        $this->showRoutePlanModal = false;
        $this->selectedStartLocation = 'current';
        $this->selectedDestination = '';
    }

    public function planRoute()
    {
        $this->validate([
            'selectedStartLocation' => 'required',
            'selectedDestination' => 'required',
        ]);

        // Get start location
        $startLocation = match($this->selectedStartLocation) {
            'current' => $this->startLocation, // Current location from the existing input
            'home1' => $this->driver->user->home_location_url_1,
            'home2' => $this->driver->user->home_location_url_2,
            default => throw new \Exception('Invalid start location'),
        };

        // Get destination
        $destination = match($this->selectedDestination) {
            'home1' => $this->driver->user->home_location_url_1,
            'home2' => $this->driver->user->home_location_url_2,
            default => $this->selectedDestination, // Order location URL
        };

        // Call the existing getRoute method
        $this->startLocation = $startLocation;
        $this->endLocation = $destination;
        $this->getRoute();

        $this->closeRoutePlanModal();
    }

    public function render()
    {
        $orders = Order::shift($this->driverId, $this->deliveryDate)->get();
        $routes = RouteNav::where('driver_id', $this->driverId)->where('day', $this->deliveryDate)->first();

        $totalZones = Order::getTotalZonesForOrders($orders);
        $PAYMENT_METHODS = CustomerPayment::PAYMENT_METHODS;

        $this->collectedFromPaymentTypes = [];
        foreach ($PAYMENT_METHODS as $PAYMENT_METHOD) {
            $this->collectedFromPaymentTypes[$PAYMENT_METHOD] = 0; // Initialize each payment type with 0
        }

        foreach ($orders as $order) {
            if (isset($this->collectedFromPaymentTypes[$order->driver_payment_type])) {
                $this->collectedFromPaymentTypes[$order->driver_payment_type] += $order->total_amount;
            }
        }

        // dd($this->collectedFromPaymentTypes);

        return view('livewire.orders.order-driver-shift', [
            'orders' => $orders,
            'totalZones' => $totalZones,
            'PAYMENT_METHODS' => $PAYMENT_METHODS,
            'routes' => $routes
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'driverShift' => 'active']);
    }
}
