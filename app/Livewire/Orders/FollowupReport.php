<?php

namespace App\Livewire\Orders;

use App\Models\Customers\Zone;
use App\Models\Orders\Order;
use App\Models\Users\User;
use Livewire\Component;

class FollowupReport extends Component
{
    
    public $page_title = '• Follow-up Report';


    public $year; // Current year
    public $years = [];
    public $selectedWeek = 1;
    public $weeksToSelect = [1,2,3,4];
    public $months = [];
    public $selectedMonth;
    public $zones;
    public $zone;


    public function mount()
    {
        $this->year = date('Y');
        $this->years = range($this->year - 4, $this->year);
        $this->months = array_map(function ($month) {
            return sprintf('%02d', $month);
        }, range(1, 12)); 
        $this->selectedMonth = date('m');
        $this->zones = Zone::select('id', 'name')->get();
        $this->zone = $this->zones->first();;
    }

    public function setZone($zone_id)
    {
        $this->zone = Zone::findOrFail($zone_id);
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
        
        $zoneId = $this->zone->id;
        $weekCount = $this->selectedWeek;
        $startMonth = $this->year.'-'.$this->selectedMonth;

        $weeklyData = Order::weeklyWeightByCustomer($zoneId, $weekCount, $startMonth);

        return view('livewire.orders.followup-report', [
            'weeks' => $weeklyData['weeks'],
            'customerWeights' => $weeklyData['customerWeights'],
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'followupReport' => 'active']);
    }
}


// $weeks = [$startDate->copy()->startOfMonth()->addWeeks()->subDays(1)->format('Y-m-d'), $startDate->copy()->startOfMonth()->addWeeks(2)->subDays(1)->format('Y-m-d'), $startDate->copy()->startOfMonth()->addWeeks(3)->subDays(1)->format('Y-m-d'), $startDate->copy()->endOfMonth()->format('Y-m-d')];