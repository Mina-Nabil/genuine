<?php

namespace App\Livewire\Reports;

use App\Models\Customers\Zone;
use App\Models\Orders\Order;
use App\Models\Payments\CustomerPayment;
use App\Models\Users\Driver;
use App\Models\Users\User;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class OrderReport extends Component
{
    use AlertFrontEnd, WithPagination;
    public $page_title = '• Orders Report';

    public $fetched_orders_IDs;
    public $search;
    public $selectAll = false; //to select all in the page
    public $selectedOrders = [];
    public $selectedOrdersStatus;
    public $selectedAllOrders = false;

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

    #[Url]
    public $zone;
    public $Edited_zoneId;
    public $Edited_zoneId_sec;

    public $creator;
    public $Edited_creatorId_sec = false;
    public $Edited_creatorId;

    public $creation_date_from;
    public $creation_date_to;
    public $edited_creation_date_from;
    public $edited_creation_date_to;
    public $Edited_creation_date_from_sec = false;

    public $delivery_date_from;
    public $delivery_date_to;
    public $edited_delivery_date_from;
    public $edited_delivery_date_to;
    public $Edited_delivery_date_from_sec = false;

    public function openFilterCreationDate()
    {
        $this->Edited_creation_date_from_sec = true;
        $this->edited_creation_date_from = $this->creation_date_from;
        $this->edited_creation_date_to = $this->creation_date_to;
    }

    public function closeFilterCreationDate()
    {
        $this->Edited_creation_date_from_sec = false;
        $this->edited_creation_date_from = null;
        $this->edited_creation_date_to = null;
    }

    public function setFilterCreationDate()
    {
        $this->creation_date_from = $this->edited_creation_date_from;
        $this->creation_date_to = $this->edited_creation_date_to;
        $this->closeFilterCreationDate();
    }

    public function clearFilterCreationDates(){
        $this->reset(['creation_date_from' , 'creation_date_to']);
    }

    public function openFilterDeliveryDate()
    {
        $this->Edited_delivery_date_from_sec = true;
        $this->edited_delivery_date_from = $this->delivery_date_from;
        $this->edited_delivery_date_to = $this->delivery_date_to;
    }

    public function closeFilterDeliveryDate()
    {
        $this->Edited_delivery_date_from_sec = false;
        $this->edited_delivery_date_from = null;
        $this->edited_delivery_date_to = null;
    }

    public function setFilterDeliveryDate()
    {
        $this->delivery_date_from = $this->edited_delivery_date_from;
        $this->delivery_date_to = $this->edited_delivery_date_to;
        $this->closeFilterDeliveryDate();
    }

    public function clearFilterDeliveryDates(){
        $this->reset(['delivery_date_from' , 'delivery_date_to']);
    }

    public function openFilteryCreator()
    {
        $this->Edited_creatorId_sec = true;
        $this->Edited_creatorId = $this->creator?->id;
    }

    public function closeFilterCreator()
    {
        $this->Edited_creatorId_sec = false;
        $this->Edited_creatorId = null;
    }

    public function setFilterCreator()
    {
        $this->creator = User::findOrFail($this->Edited_creatorId);
        $this->closeFilterCreator();
    }

    public function resetStatuses(){
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
        $this->selectedOrders = Order::OpenOrders()->pluck('id')->toArray();
    }

    public function undoSelectAllOrders()
    {
        $this->selectedAllOrders = false;
        $this->selectedOrders = $this->fetched_orders_IDs;
    }


    public function render()
    {
        $orders = Order::Report(
            searchText: $this->search,
            zone_id: $this->zone?->id,
            driver_id: $this->driver?->id,
            created_from: $this->creation_date_from ? Carbon::parse($this->creation_date_from) : null,
            created_to: $this->creation_date_from ? Carbon::parse($this->creation_date_to) : null,
            delivery_from: $this->delivery_date_from ? Carbon::parse($this->delivery_date_from) : null,
            delivery_to: $this->delivery_date_to ? Carbon::parse($this->delivery_date_to) : null,
            creator_id: $this->creator?->id,
            status: $this->status,
            )
            ->sortByDeliveryDate()
            ->paginate(50);

        $totalWeight = 0;
        foreach ($orders as $order) {
            $totalWeight = $totalWeight + $order->total_weight;
        }

        $totalZones = Order::getTotalZonesForOrders($orders);
        $ordersCount = count($orders);

        $DRIVERS = Driver::all();
        $ZONES = Zone::all();
        $STATUSES = Order::STATUSES;
        $drivers = Driver::all();
        $users = User::all();

        $this->fetched_orders_IDs = $orders->pluck('id')->toArray();

        return view('livewire.reports.order-report', [
            'orders' => $orders,
            'drivers' => $drivers,
            'STATUSES' => $STATUSES,
            'DRIVERS' => $DRIVERS,
            'ZONES' => $ZONES,
            'totalWeight' => $totalWeight,
            'totalZones' => $totalZones,
            'ordersCount' => $ordersCount,
            'users' => $users
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'ordersReport' => 'active']);
    }
}