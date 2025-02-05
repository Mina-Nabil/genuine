<?php

namespace App\Livewire\Orders;

use App\Models\Customers\Zone;
use App\Models\Orders\Order;
use App\Models\Payments\CustomerPayment;
use App\Models\Users\Driver;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class OrderIndex extends Component
{
    use AlertFrontEnd, WithPagination;
    public $page_title = '• Orders';


    public $drivers;
    public $STATUSES;
    public $DRIVERS;
    public $saved_zones;
    public $PAYMENT_METHODS;

    public $fetched_orders_IDs = [];
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
    public $AmountToCollect;
    public $Edited_driverId;
    public $Edited_driverId_sec;

    public $setZoneSection = false;
    public $zones = [];
    public $Edited_Zone;
    public $Edited_Zone_sec;
    public $selectedZones = [];
    public $selectedZonesNames = [];

    #[Url]
    public $deliveryDate = [];
    public $Edited_deliveryDate;
    public $Edited_deliveryDate_sec;
    public $selectedDeliveryDates = [];

    public $AvailableToPay = false;
    public $AvailableToSetDriver = false;

    public $isOpenPayAlert = null; //carry payment method
    public $errorMessages = [];

    public function openPayOrders($paymentMethod)
    {
        $this->isOpenPayAlert = $paymentMethod;
    }
    public function closePayOrders($paymentMethod)
    {
        $this->isOpenPayAlert = null;
    }

    public function resetStatuses()
    {
        $res = Order::resetBulkStatus($this->selectedOrders);
        if ($res) {
            $this->resetPage();
            $this->selectedOrders = [];
            $this->selectAll = false;
            $this->alertSuccess('Status changed!');
        } else {
            $this->alertFailed();
        }
    }

    public function ProcceedBulkPayment()
    {
        $res = Order::bulkSetAsPaid($this->selectedOrders, Carbon::now(), $this->isOpenPayAlert, false);
        if ($res === true) {
            $this->errorMessages = [];
            $this->selectedOrders = [];
            $this->reset('AvailableToPay', 'isOpenPayAlert');
            $this->alertSuccess('Paid Successfuly!');
        } else {
            $this->errorMessages = $res;
        }
    }

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
        if (count($this->selectedDeliveryDates) > 1) {
            unset($this->selectedDeliveryDates[$index]);
            $this->selectedDeliveryDates = array_values($this->selectedDeliveryDates); // Reset array keys
        }
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

    public function openFilteryStatus()
    {
        $this->Edited_status_sec = true;
        $this->Edited_status = $this->status;
    }

    public function closeFilteryStatus()
    {
        $this->Edited_status_sec = false;
        $this->Edited_status = null;
    }

    public function setFilterStatus()
    {
        $this->status = $this->Edited_status;
        $this->closeFilteryStatus();
    }

    public function openFilteryDriver()
    {
        $this->Edited_driverId_sec = true;
        $this->Edited_driverId = $this->driver?->id;
    }

    public function closeFilteryDriver()
    {
        $this->Edited_driverId_sec = false;
        $this->Edited_driverId = null;
    }

    public function setFilterDriver()
    {
        $this->driver = Driver::findOrFail($this->Edited_driverId);
        $this->closeFilteryDriver();
    }

    public function clearZones()
    {
        $this->zones = [];
    }

    public function updatedEditedZone($value)
    {
        foreach ($this->selectedZones as $z) {
            if ($z === $value) {
                return;
            }
        }
        $this->selectedZones[] = $value;
        $this->selectedZonesNames[] = Zone::find($value)->name;
        $this->Edited_Zone = null;
    }

    public function openZoneSec()
    {
        $this->Edited_Zone_sec = true;

        foreach ($this->zones as $zone) {
            $this->selectedZones[] = $zone;
        }
    }

    public function closeZoneSec()
    {
        $this->Edited_Zone_sec = false;
        $this->Edited_Zone = null;
        $this->selectedZones = [];
    }

    public function setZones()
    {
        $this->zones = $this->selectedZones;
        $this->closeZoneSec();
    }

    public function removeSelectedZone($index)
    {
        if (count($this->selectedZones)) {
            unset($this->selectedZones[$index]);
            unset($this->selectedZonesNames[$index]);
            $this->selectedZones = array_values($this->selectedZones); // Reset array keys
            $this->selectedZonesNames = array_values($this->selectedZonesNames); // Reset array keys
        }
    }


    public function mount()
    {
        if (count($this->deliveryDate)) {
            foreach ($this->deliveryDate as $i => $d) {
                $this->deliveryDate[$i] = Carbon::parse($d);
            }
        }

        $this->DRIVERS = Driver::all();
        $this->saved_zones = Zone::all();
        $this->STATUSES = Order::STATUSES;
        $this->drivers = Driver::all();
        $this->PAYMENT_METHODS = CustomerPayment::PAYMENT_METHODS_WITH_DEBIT;
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
            $this->selectedOrders = [];
            $this->selectAll = false;
            $this->alertSuccess('Status changed!');
        } else {
            $this->alertFailed();
        }
    }

    public function setBulkAsConfirmed()
    {
        $res = Order::setBulkConfirmed($this->selectedOrders, isConfirmed: true);
        if ($res) {
            $this->resetPage();
            $this->selectedOrders = [];
            $this->selectAll = false;
            $this->alertSuccess('Confirmation changed!');
        } else {
            $this->alertFailed();
        }
    }

    public function setBulkAsNotConfirmed()
    {
        $res = Order::setBulkConfirmed($this->selectedOrders, isConfirmed: false);
        if ($res) {
            $this->resetPage();
            $this->selectedOrders = [];
            $this->selectAll = false;
            $this->alertSuccess('Confirmation changed!');
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
        $this->AvailableToPay = Order::checkRemainingToPayConsistency($this->selectedOrders);
        $this->AvailableToSetDriver = Order::checkInHouseEligibility($this->selectedOrders);
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
            $this->selectedOrders = [];
            $this->selectAll = false;
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
            $this->selectedOrders = [];
            $this->selectAll = false;
            $this->alertSuccess('Driver set!');
        } else {
            $this->alertFailed();
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedOrders = $this->fetched_orders_IDs;
            $this->updatedSelectedOrders();
        } else {
            $this->selectedOrders = [];
        }
    }

    public function unselectAllOrders()
    {
        $this->selectedOrders = [];
        $this->updatedSelectedOrders();
    }

    public function render()
    {
        $orders = Order::search(searchText: $this->search, deliveryDates: $this->deliveryDate, status: $this->status, driverId: $this->driver?->id, zoneIds: $this->zones)->OpenOrders()
            ->with('customer', 'zone', 'driver', 'creator')
            // ->sortByDeliveryDate()
            ->notDebitOrders()
            ->paginate(20);

        $totalWeight = 0;
        foreach ($orders as $order) {
            $totalWeight = $totalWeight + $order->total_weight;
        }

        $totalZones = Order::getTotalZonesForOrders($orders);
        $ordersCount = count($orders);



        $this->fetched_orders_IDs = $orders->pluck('id')->toArray();
        return view('livewire.orders.order-index', [
            'orders' => $orders,
            'totalWeight' => $totalWeight,
            'totalZones' => $totalZones,
            'ordersCount' => $ordersCount,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'orders' => 'active']);
    }
}
