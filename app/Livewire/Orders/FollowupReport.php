<?php

namespace App\Livewire\Orders;

use App\Models\Customers\Customer;
use App\Models\Customers\Zone;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class FollowupReport extends Component
{
    use WithPagination;

    public $page_title = '• Follow-up Report';
    protected $paginationTheme = 'bootstrap';

    public $year; // Current year
    public $years = [];
    public $selectedWeek = 1;
    public $weeksToSelect = [1, 2, 3, 4];
    public $months = [];
    public $selectedMonth;

    public $setZoneSection = false;
    public $zones = [];
    public $Edited_Zone;
    public $Edited_Zone_sec;
    public $selectedZones = [];
    public $selectedZonesNames = [];

    // public $Edited_deliveryDate;
    // public $Edited_deliveryDate_sec;
    // public $selectedDeliveryDates = [];

    public $search;
    public $searchZoneText;


    public function reorderLastOrder($last_order_id)
    {
        $lastOrderid = $last_order_id;
        $this->dispatch('openNewTab', ['url' => route('orders.create', ['order_id' => $lastOrderid])]);
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
        if (count($this->selectedZones) > 1) {
            unset($this->selectedZones[$index]);
            $this->selectedZones = array_values($this->selectedZones); // Reset array keys
        }
    }


    public function mount()
    {
        $this->years = range($this->year - 4, $this->year);
        $this->months = array_map(function ($month) {
            return sprintf('%02d', $month);
        }, range(1, 12));

        $start = Carbon::now()->subMonth();

        $this->year = $start->format('Y');
        $this->selectedMonth = $start->format('m');
    }

    public function setMonth($month)
    {
        $this->selectedMonth = $month;
    }

    public function setYear($year)
    {
        $this->year = $year;
    }

    public function setWeek($week)
    {
        $this->selectedWeek = $week;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function render()
    {
        $startDate = Carbon::createFromDate($this->year, $this->selectedMonth);
        $end = Carbon::now();

        $zones = Zone::all();

        $customers = Customer::search($this->search)->ByZones($this->zones)->paginate(30);
        /** @var Customer */
        foreach ($customers as $c) {
            $startTmp = $startDate->clone();
            while ($startTmp->isBefore($end)) {
                $c->appendKGTotal($startTmp, $startTmp->addWeek());
            }
        }
        // Log::info($customers->links());
        return view('livewire.orders.followup-report', [
            'customers'     =>  $customers,
            'start_week'    =>  $startDate,
            'end_week'      =>  $end,
            'saved_zones' => $zones
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'followupReport' => 'active']);
    }
}


// $weeks = [$startDate->copy()->startOfMonth()->addWeeks()->subDays(1)->format('Y-m-d'), $startDate->copy()->startOfMonth()->addWeeks(2)->subDays(1)->format('Y-m-d'), $startDate->copy()->startOfMonth()->addWeeks(3)->subDays(1)->format('Y-m-d'), $startDate->copy()->endOfMonth()->format('Y-m-d')];
