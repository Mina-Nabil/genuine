<?php

namespace App\Livewire\Reports;

use App\Models\Customers\Zone;
use App\Models\Orders\Order;
use Carbon\Carbon;
use Livewire\Component;

class WeeklyTotalsReport extends Component
{
    public $page_title = '• Weekly Totals Report';

    public $searchTerm;

    public $fromDate;
    public $toDate;

    public $setZoneSection = false;
    public $zones = [];
    public $Edited_Zone;
    public $Edited_Zone_sec;
    public $selectedZones = [];
    public $selectedZonesNames = [];

    public function mount()
    {
        $this->fromDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->toDate = Carbon::now()->endOfMonth()->format('Y-m-d');
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

    public function render()
    {
        $saved_zones = Zone::all();
        $zoneReports = Order::weeklyZoneReport($this->fromDate, $this->toDate, $this->searchTerm, $this->zones)->get();
        $groupedZoneReports = $zoneReports->groupBy('zone_name');
        $daysInRange = Carbon::parse($this->fromDate)->diffInDays(Carbon::parse($this->toDate)) + 1;
        $weekCount = max(1, (int) ceil($daysInRange / 7));

        return view('livewire.reports.weekly-totals-report',[
            'groupedZoneReports' => $groupedZoneReports,
            'saved_zones' => $saved_zones,
            'weekCount' => $weekCount,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'weeklyTotalsReport' => 'active']);
    }
}
