<?php

namespace App\Livewire\Orders;

use App\Models\Customers\Zone;
use App\Models\Orders\Order;
use App\Models\Payments\CustomerPayment;
use App\Models\Users\Driver;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;


class CancelledOrderIndex extends Component
{
    use AlertFrontEnd, WithPagination;
    public $page_title = '• Cancelled Orders';

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

    #[Url]
    public $driver;
    public $AmountToCollect;
    public $Edited_driverId;
    public $Edited_driverId_sec;

    #[Url]
    public $zone;
    public $Edited_zoneId;
    public $Edited_zoneId_sec;

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

    public function ProcceedBulkPayment()
    {
        $res = Order::bulkSetAsPaid($this->selectedOrders, Carbon::now(), $this->isOpenPayAlert, false);
        if ($res === true) {
            $this->errorMessages = [];
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

    public function openFilteryZone()
    {
        $this->Edited_zoneId_sec = true;
        $this->Edited_zoneId = $this->zone?->id;
    }

    public function closeFilteryZone()
    {
        $this->Edited_zoneId_sec = false;
        $this->Edited_zoneId = null;
    }

    public function setFilterZone()
    {
        $this->zone = Zone::findOrFail($this->Edited_zoneId);
        $this->closeFilteryZone();
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
        $this->selectedOrders = Order::CancelledOrders()->pluck('id')->toArray();
    }

    public function undoSelectAllOrders()
    {
        $this->selectedAllOrders = false;
        $this->selectedOrders = $this->fetched_orders_IDs;
    }

    public function mount()
    {
        if (count($this->deliveryDate)) {
            foreach ($this->deliveryDate as $i => $d) {
                $this->deliveryDate[$i] = Carbon::parse($d);
            }
        }
    }

    public function render()
    {
        $orders = Order::search(searchText: $this->search, deliveryDates: $this->deliveryDate, driverId: $this->driver?->id, zoneId: $this->zone?->id)->CancelledOrders()->withTotalQuantity()->paginate(50);

        $totalZones = Order::getTotalZonesForOrders($orders);
        $ordersCount = count($orders);

        $DRIVERS = Driver::all();
        $ZONES = Zone::all();
        $STATUSES = Order::STATUSES;
        $drivers = Driver::all();
        $PAYMENT_METHODS = CustomerPayment::PAYMENT_METHODS;

        return view('livewire.orders.closed-order-index', [
            'orders' => $orders,
            'drivers' => $drivers,
            'STATUSES' => $STATUSES,
            'DRIVERS' => $DRIVERS,
            'ZONES' => $ZONES,
            'totalZones' => $totalZones,
            'ordersCount' => $ordersCount,
            'PAYMENT_METHODS' => $PAYMENT_METHODS,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'ordersCancelled' => 'active']);
    }
}
