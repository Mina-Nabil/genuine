<?php

namespace App\Livewire\Reports;

use App\Models\Orders\Order;
use Carbon\Carbon;
use Livewire\Component;

class DailySalesPerformanceReport extends Component
{
    public $page_title = '• Sales Performance Report';

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
        $performanceReport = Order::userPerformanceReport($this->selectedYear, $this->selectedMonth)->get();
        return view('livewire.reports.daily-sales-performance-report',[
            'performanceReport' => $performanceReport
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'salesPerformanceReport' => 'active']);
    }
}
