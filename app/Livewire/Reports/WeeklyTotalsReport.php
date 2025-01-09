<?php

namespace App\Livewire\Reports;

use App\Models\Orders\Order;
use Carbon\Carbon;
use Livewire\Component;

class WeeklyTotalsReport extends Component
{
    public $page_title = '• Weekly Totals Report';

    public $searchTerm;

    public $currentYear;
    public $lastYears;
    public $selectedYear;

    public $currentMonth;
    public $AllMonths;
    public $selectedMonth;

    

    public function mount()
    {
        $this->currentYear = Carbon::now()->format('Y');
        $this->selectedYear = Carbon::parse($this->currentYear)->format('Y');
        $this->lastYears = collect(range($this->currentYear - 5, $this->currentYear));

        $this->currentMonth =  Carbon::now()->format('m');
        $this->AllMonths = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"];
        $this->selectedMonth = $this->currentMonth;
    }

    public function selectYear($year){
        $this->selectedYear = Carbon::createFromFormat('Y', $year)->format('Y');
    }

    public function selectMonth($month){
        $this->selectedMonth = Carbon::createFromFormat('m', $month)->format('m');
    }

    public function render()
    {
        $zoneReports = Order::weeklyZoneReport($this->selectedYear, $this->selectedMonth , $this->searchTerm)->get();
        $groupedZoneReports = $zoneReports->groupBy('zone_name');
        return view('livewire.reports.weekly-totals-report',[
            'groupedZoneReports' => $groupedZoneReports
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'weeklyTotalsReport' => 'active']);
    }
}
