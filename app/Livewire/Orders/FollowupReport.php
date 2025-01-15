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
    public $is_ordered = false;

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
            unset($this->selectedZonesNames[$index]);
            $this->selectedZones = array_values($this->selectedZones); // Reset array keys
            $this->selectedZonesNames = array_values($this->selectedZonesNames); // Reset array keys
        }
    }


    public function mount()
    {
        $start = Carbon::now()->subMonth();

        $this->year = $start->format('Y');
        $this->selectedMonth = $start->format('m');
        $this->selectedWeek = min(4, $start->weeksInMonth);

        $this->years = range($this->year - 4, $this->year + 1);
        $this->months = array_map(function ($month) {
            return sprintf('%02d', $month);
        }, range(1, 12));
    }

    public function setMonth($month)
    {
        $this->selectedMonth = $month;
    }

    public function setYear($year)
    {
        $this->year = $year;
    }

    public function setIsOrdered($is_ordered)
    {
        $this->is_ordered = $is_ordered;
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
        $startDate->setWeek($this->selectedWeek);
        $end = Carbon::now();

        $zones = Zone::all();

        $customers = Customer::with('orders')->search($this->search)
        ->when($this->is_ordered, function($q) use ($startDate, $end) {
            $q->orderedBetween($startDate, $end);
        })
        ->ByZones($this->zones)->paginate(30);
        /** @var Customer */
        foreach ($customers as $c) {
            $startTmp = $startDate->clone();
            while ($startTmp->isBefore($end)) {
                $tmpEnd =  $startTmp->clone()->addWeek();
                $c->appendKGTotal($startTmp, $tmpEnd);
                $startTmp->addWeek();
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
