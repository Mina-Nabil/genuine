<?php

namespace App\Livewire\Orders;

use App\Models\Orders\Order;
use App\Models\Orders\OrderProduct;
use App\Models\Users\Driver;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;


class OrderInventory extends Component
{
    use WithPagination, AlertFrontEnd;
    public $page_title = '• Orders • Inventory';
    public $search;
    public $status;
    public $driver;
    public $zone;

    public $selectedOrders = [];
    public $selectedOrderProducts = [];

    #[Url]
    public $driverRadio;
    public $Edited_driverId;
    public $Edited_driverId_sec;

    #[Url]
    public $deliveryDate = [];
    public $Edited_deliveryDate;
    public $Edited_deliveryDate_sec;
    public $selectedDeliveryDates = [];

    public $resetStatusOrderID;

    public $noOfBags = [];

    public function updateNoOfBags($id)
    {
        Order::findOrFail($id)->updateNoOfBags(!is_numeric($this->noOfBags[$id]) ? 0 : $this->noOfBags[$id]);
    }

    public function resetStatus($id)
    {
        $this->resetStatusOrderID = $id;
    }

    public function dismissResetStatus()
    {
        $this->resetStatusOrderID = null;
    }

    public function confirmResetStatus()
    {
        $res = Order::findOrFail($this->resetStatusOrderID)->resetStatus();
        if ($res) {
            $this->alertSuccess('Order updated');
            $this->dismissResetStatus();
        } else {
            $this->alertFailed();
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

    public function updatedDriverRadio($value)
    {
        $this->driver = Driver::findOrFail($value);
    }

    public function removeSelectedDate($index)
    {
        unset($this->selectedDeliveryDates[$index]);
        $this->selectedDeliveryDates = array_values($this->selectedDeliveryDates); // Reset array keys
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

    public function clearProperty(string $propertyName)
    {
        // Check if the property exists before attempting to clear it
        if (property_exists($this, $propertyName)) {
            $this->$propertyName = null;
        }
    }

    public function printInventoryShift()
    {
        if (count($this->deliveryDate))
            return Order::printPrepareDoc($this->deliveryDate[0]);
    }

    public function setAsInDelivery($order_id)
    {
        $res = Order::findOrFail($order_id)->setStatus(Order::STATUS_IN_DELIVERY);
        if ($res) {
            $this->alertSuccess('Order delivered');
        } else {
            $this->alertFailed();
        }
    }

    public function setAllReady($order_id)
    {
        $order = Order::findOrFail($order_id);
        $this->authorize('update', $order);

        /** @var OrderProduct */
        foreach ($order->products as $p) {
            $p->setAsReady();
        }
        $this->alertSuccess('Available products are pulled from Stock');

    }

    public function toggleDeletedReady($id)
    {
        $orderProduct = OrderProduct::withTrashed()->findOrFail($id);
        $res = $orderProduct->toggleDeletedProductReady();

        if ($res) {
            $this->alertSuccess('Product swtiched');
        } else {
            $this->alertFailed();
        }
    }

    public function mount()
    {
        $this->authorize('viewOrderInventory', Order::class);

        if (count($this->deliveryDate)) {
            foreach ($this->deliveryDate as $i => $d) {
                $this->deliveryDate[$i] = Carbon::parse($d);
            }
        } else {
            $this->deliveryDate = [Carbon::today()];
        }
        
        if ($this->driverRadio)
            $this->driver = Driver::findOrFail($this->driverRadio);

        $orders = Order::search(searchText: $this->search, deliveryDates: $this->deliveryDate, status: $this->status, driverId: $this->driver?->id, zoneId: $this->zone?->id)->withTotalQuantity()->openOrders()->paginate(50);
        foreach ($orders as $order) {
            $this->noOfBags[$order->id] = $order->no_of_bags;
        }
    }



    public function render()
    {
        $DRIVERS = Driver::all();
        $orders = Order::search(searchText: $this->search, deliveryDates: $this->deliveryDate, status: $this->status, driverId: $this->driver?->id, zoneId: $this->zone?->id)->withTotalQuantity()->openOrders()->get();
        foreach ($orders as $order) {
            $this->noOfBags[$order->id] = $order->no_of_bags;
        }

        $todayShifts = Driver::hasOrdersOn($this->deliveryDate)->get();

        $cancelledOrders = Order::search(
            searchText: $this->search,
            status: $this->status,
            driverId: $this->driver?->id,
            zoneId: $this->zone?->id
        )
            ->withCancelledReadyProducts()
            ->with(['products' => function ($query) {
                $query->withTrashed(); // Include trashed products in the eager loading
            }])
            ->get();

        return view('livewire.orders.order-inventory', [
            'orders' => $orders,
            'todayShifts' => $todayShifts,
            'DRIVERS' => $DRIVERS,
            'cancelledOrders' => $cancelledOrders
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'ordersInventory' => 'active']);
    }
}
