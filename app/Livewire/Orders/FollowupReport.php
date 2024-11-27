<?php

namespace App\Livewire\Orders;

use App\Models\Orders\Order;
use Livewire\Component;

class FollowupReport extends Component
{
    public $year; // Current year
    public $years = [];
    public $selectedWeek = 1;
    public $weeksToSelect = [1,2,3,4];
    public $months = [];
    public $selectedMonth;


    public function mount()
    {
        $this->year = date('Y');
        $this->years = range($this->year - 4, $this->year);
        $this->months = array_map(function ($month) {
            return sprintf('%02d', $month);
        }, range(1, 12)); 
        $this->selectedMonth = date('m');
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

    public function render()
    {
        $zoneId = 18;
        $weekCount = $this->selectedWeek;
        $startMonth = $this->year.'-'.$this->selectedMonth;

        $weeklyData = Order::weeklyWeightByCustomer($zoneId, $weekCount, $startMonth);

        return view('livewire.orders.followup-report', [
            'weeks' => $weeklyData['weeks'],
            'customerWeights' => $weeklyData['customerWeights'],
        ]);
    }
}
