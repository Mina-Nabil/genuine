<?php

namespace App\Livewire\Customers;

use App\Models\Customers\Zone;
use Livewire\Component;
use App\Traits\AlertFrontEnd;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ZoneIndex extends Component
{
    use WithFileUploads, AlertFrontEnd, WithPagination, AuthorizesRequests;
    public $page_title = '• Zones';

    public $fetched_zones_IDs;
    public $search;
    public $selectAll = false; //to select all in the page
    public $selectedZones = [];
    public $newZoneSection = false;
    public $selectedAllZones = false; //to select all zones

    public $name;
    public $deliveryRate;
    public $orderRate;
    public $returnRate;

    public $updatedZone; //to edit model

    public function updateThisZone($id)
    {
        $this->updatedZone = Zone::findOrFail($id);
        $this->authorize('update', $this->updatedZone);
        $this->name = $this->updatedZone->name;
        $this->deliveryRate = $this->updatedZone->delivery_rate;
        $this->orderRate = $this->updatedZone->driver_order_rate;
        $this->returnRate = $this->updatedZone->driver_return_rate;
    }

    public function closeUpdateZoneSec()
    {
        $this->reset(['updatedZone', 'name', 'deliveryRate', 'orderRate', 'returnRate']);
    }

    public function updateZone()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'deliveryRate' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/', // Ensure up to 2 decimal places
            'orderRate' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/', // Ensure up to 2 decimal places
            'returnRate' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/', // Ensure up to 2 decimal places
        ]);

        $res = $this->updatedZone->editInfo($this->name, $this->deliveryRate, $this->orderRate, $this->returnRate);

        if ($res) {
            $this->alertSuccess('Zone updated!');
            $this->closeUpdateZoneSec();
        } else {
            $this->alertFailed();
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedZones = $this->fetched_zones_IDs;
        } else {
            $this->selectedZones = [];
        }
    }

    public function selectAllZones()
    {
        $this->selectedAllZones = true;
        $this->selectedZones = Zone::pluck('id')->toArray();
    }

    public function undoSelectAllZones()
    {
        $this->selectedAllZones = false;
        $this->selectedZones = $this->fetched_zones_IDs;
    }

    ///// Frontend Hnadling
    public function openNewZoneSec()
    {
        $this->newZoneSection = true;
    }

    public function closeNewZoneSec()
    {
        $this->reset(['name', 'deliveryRate', 'newZoneSection', 'orderRate', 'returnRate']);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function addNewZone()
    {
        $this->authorize('create', Zone::class);

        $this->validate([
            'name' => 'required|string|max:255',
            'deliveryRate' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/', // Ensure up to 2 decimal places
            'orderRate' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/', // Ensure up to 2 decimal places
            'returnRate' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/', // Ensure up to 2 decimal places
        ]);

        $res = Zone::newZone($this->name, $this->deliveryRate, $this->orderRate, $this->returnRate);

        if ($res) {
            $this->alertSuccess('Zone added!');
            $this->closeNewZoneSec();
        } else {
            $this->alertFailed();
        }
    }

    public function render()
    {
        $zones = Zone::when($this->search, fn($q) => $q->search($this->search))->paginate(30);
        $this->fetched_zones_IDs = $zones->pluck('id')->toArray();
        return view('livewire.customers.zone-index', [
            'zones' => $zones,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'zones' => 'active']);
    }
}
