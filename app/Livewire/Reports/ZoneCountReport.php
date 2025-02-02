<?php

namespace App\Livewire\Reports;

use App\Models\Customers\Customer;
use Carbon\Carbon;
use Livewire\Component;

class ZoneCountReport extends Component
{
    public $searchText;
    public $creation_date_from;
    public $creation_date_to;
    public $edited_creation_date_from;
    public $edited_creation_date_to;
    public $Edited_creation_date_from_sec = false;

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
    
    public function mount()
    {
        $this->creation_date_from = now()->startOfMonth()->toDateString(); 
        $this->creation_date_to = now()->toDateString();
    }

    public function setFilterCreationDate()
    {
        $this->creation_date_from = $this->edited_creation_date_from;
        $this->creation_date_to = $this->edited_creation_date_to;
        $this->closeFilterCreationDate();
    }

    public function clearFilterCreationDates()
    {
        $this->reset(['creation_date_from', 'creation_date_to']);
    }

    public function render()
    {
        $zones = Customer::ZonesCountReport(Carbon::parse($this->creation_date_from),Carbon::parse($this->creation_date_to),$this->searchText)->paginate(50);
        return view('livewire.reports.zone-count-report',[
            'zones' => $zones
        ])->layout('layouts.app', ['page_title' => 'New Customers Report', 'zoneCountReport' => 'active']);
    }
}
