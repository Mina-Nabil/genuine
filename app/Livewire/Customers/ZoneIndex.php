<?php

namespace App\Livewire\Customers;

use App\Models\Customers\Zone;
use Livewire\Component;
use App\Traits\AlertFrontEnd;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ZoneIndex extends Component
{

    use WithFileUploads, AlertFrontEnd, WithPagination;
    public $page_title = '• Zones';

    public $fetched_zones_IDs;
    public $search;
    public $selectAll = false; //to select all in the page
    public $selectedZones = [];
    public $newZoneSection = false;
    public $selectedAllZones = false;//to select all zones

    public $name;
    public $deliveryRate;

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedZones = $this->fetched_zones_IDs;
        } else {
            $this->selectedZones = [];
        }
    }

    public function selectAllZones(){
        $this->selectedAllZones = true;
        $this->selectedZones = Zone::pluck('id')->toArray();
    }

    public function undoSelectAllZones(){
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
        $this->reset(['name', 'deliveryRate',  'newZoneSection']);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function addNewZone()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'deliveryRate' => 'required|integer',

        ]);

        $res = Zone::newZone($this->name,$this->deliveryRate);

        if($res){
            $this->alertSuccess('Zone added!');
            $this->closeNewZoneSec();
        }else{
            $this->alertFailed();
        }
    }

    public function render()
    {
        $zones = Zone::when($this->search, fn($q) => $q->search($this->search))->paginate(30);
        $this->fetched_zones_IDs = $zones->pluck('id')->toArray();
        return view('livewire.customers.zone-index',[
            'zones' => $zones
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'zones' => 'active']);
    }
}
