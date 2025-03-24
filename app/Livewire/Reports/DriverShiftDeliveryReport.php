<?php

namespace App\Livewire\Reports;

use App\Models\Orders\Order;
use App\Models\Users\Driver;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Url;

class DriverShiftDeliveryReport extends Component
{
    public $page_title = '• Reports • Driver Shift Delivery';
    
    #[Url]
    public $deliveryDate = [];
    public $Edited_deliveryDate;
    public $Edited_deliveryDate_sec;
    public $selectedDeliveryDates = [];

    #[Url]
    public $driverRadio;
    public $driver;

    public function clearProperty(string $propertyName)
    {
        // Check if the property exists before attempting to clear it
        if (property_exists($this, $propertyName)) {
            $this->$propertyName = null;
            // If clearing driver property, also clear the radio selection
            if ($propertyName === 'driver') {
                $this->driverRadio = null;
            }
        }
    }

    public function updatedDriverRadio($value)
    {
        if($value == 'all') {
            $this->driver = null;
        } else {
            $this->driver = Driver::findOrFail($value);
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
        $this->selectedDeliveryDates = array_values($this->selectedDeliveryDates);
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
        if (count($this->deliveryDate)) {
            foreach ($this->deliveryDate as $i => $d) {
                $this->deliveryDate[$i] = Carbon::parse($d);
            }
        } else {
            $this->deliveryDate = [Carbon::today()];
        }
        
        if ($this->driverRadio)
            $this->driver = Driver::findOrFail($this->driverRadio);
    }

    public function render()
    {
        $drivers = Driver::hasOrdersOn($this->deliveryDate)->get();
        
        $driverTotals = [];
        foreach ($drivers as $driver) {
            if ($this->driver && $this->driver->id !== $driver->id) {
                continue;
            }

            $orders = Order::search(
                deliveryDates: $this->deliveryDate,
                driverId: $driver->id
            )->with(['products.product'])->get();

            $productTotals = [];
            foreach ($orders as $order) {
                foreach ($order->products as $orderProduct) {
                    $productId = $orderProduct->product->id;
                    if (!isset($productTotals[$productId])) {
                        $productTotals[$productId] = [
                            'name' => $orderProduct->product->name,
                            'quantity' => 0,
                            'weight' => 0
                        ];
                    }
                    $productTotals[$productId]['quantity'] += $orderProduct->quantity;
                    $productTotals[$productId]['weight'] += $orderProduct->quantity * ($orderProduct->product->weight / 1000);
                }
            }

            // Sort products by total weight in descending order
            uasort($productTotals, function($a, $b) {
                return $b['weight'] <=> $a['weight'];
            });

            $driverTotals[$driver->id] = [
                'driver' => $driver,
                'orders' => $orders,
                'product_totals' => $productTotals,
                'total_weight' => $orders->sum('total_weight') / 1000,
                'total_items' => $orders->sum('total_items')
            ];
        }

        return view('livewire.reports.driver-shift-delivery-report', [
            'driverTotals' => $driverTotals,
            'todayShifts' => $drivers
        ])->layout('layouts.app', ['page_title' => $this->page_title , 'driverShiftDeliveryReport' => 'active']);
    }
} 