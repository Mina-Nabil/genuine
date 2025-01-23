<?php

namespace App\Livewire\Orders;

use App\Models\Customers\Customer;
use App\Models\Customers\Followup;
use App\Models\Customers\Zone;
use App\Models\Users\User;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class FollowupReport extends Component
{
    use WithPagination , AlertFrontEnd;

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

    //followup
    public $followupDetailsSection; //carries followup id
    public $addFollowupSection; //carries customer id
    public $followupTitle;
    public $followupCallDate;
    public $followupCallTime;
    public $followupDesc;
    public $followupEditId;

    public $search;
    public $searchZoneText;

    public function openFollowupDetailsSec($followup_id)
    {
        $followup = Followup::findOrFail($followup_id);
        $this->addFollowupSection = $followup->called_id;
        $this->followupCallDate = Carbon::parse($followup->call_time)->format('Y-m-d');
        $this->followupCallTime = Carbon::parse($followup->call_time)->format('H:i');
        $this->followupTitle = $followup->title;
        $this->followupDesc = $followup->desc;
        $this->followupEditId = $followup->id;
    }

    public function openAddFollowupSec($customer_id)
    {
        $this->addFollowupSection = $customer_id;
        $this->followupCallDate = Carbon::now()->format('Y-m-d');
        $this->followupCallTime = Carbon::now()->format('H:i');
    }

    public function closeFollowupSection()
    {
        $this->reset(['addFollowupSection', 'followupTitle', 'followupCallDate', 'followupCallTime', 'followupDesc', 'followupEditId']);
    }

    public function addFollowup()
    {
        $this->validate([
            'followupTitle' => 'required|string|max:255',
            'followupCallDate' => 'nullable|date',
            'followupCallTime' => 'nullable',
            'followupDesc' => 'nullable|string|max:255',
        ]);

        $combinedDateTimeString = $this->followupCallDate . ' ' . $this->followupCallTime;
        $combinedDateTime = new \DateTime($combinedDateTimeString);
        if($this->followupEditId){
            /** @var Followup */
            $followup = Followup::findOrFail($this->followupEditId);
            $res = $followup->editInfo($this->followupTitle, $combinedDateTime, $this->followupDesc);
        } else {
            $customer = Customer::find($this->addFollowupSection);
            $res = $customer->addFollowup($this->followupTitle, $combinedDateTime, $this->followupDesc);
        }

        if ($res) {
            $this->alert('success', 'Followup set');
            $this->closeFollowupSection();
        } else {
            $this->alert('failed', 'server error');
        }
    }

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

        $this->selectedWeek = min(4, $start->weekOfMonth);

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
        $startDate = Carbon::createFromDate($this->year, $this->selectedMonth, getStartOfWeek($this->selectedWeek));
        $end = Carbon::now()->endOfDay();

        $zones = Zone::all();

        $customers = Customer::with('orders', 'last_followup')->search($this->search)
            ->when($this->is_ordered, function ($q) use ($startDate, $end) {
                $q->orderedBetween($startDate, $end);
            })
            ->ByZones($this->zones)->paginate(30);

        /** @var Customer */
        foreach ($customers as $c) {
            $startTmp = $startDate->clone()->startOfDay();
            while ($startTmp->lessThanOrEqualTo($end)) {
      
                $tmpEnd =  $startTmp->clone()->addDays(6);
                if ($tmpEnd->dayOfMonth > 25) $tmpEnd->endOfMonth();

                $c->appendKGTotal($startTmp, $tmpEnd);
                $startTmp = $tmpEnd->clone()->addDay();
  
            }
        }
        // Log::info($customers->links());
        return view('livewire.orders.followup-report', [
            'customers' => $customers,
            'start_week' => $startDate,
            'end_week' => $end,
            'saved_zones' => $zones,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'followupReport' => 'active']);
    }
}

// $weeks = [$startDate->copy()->startOfMonth()->addWeeks()->subDays(1)->format('Y-m-d'), $startDate->copy()->startOfMonth()->addWeeks(2)->subDays(1)->format('Y-m-d'), $startDate->copy()->startOfMonth()->addWeeks(3)->subDays(1)->format('Y-m-d'), $startDate->copy()->endOfMonth()->format('Y-m-d')];
