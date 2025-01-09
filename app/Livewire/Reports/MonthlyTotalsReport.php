<?php

namespace App\Livewire\Reports;

use App\Models\Orders\Order;
use Carbon\Carbon;
use Livewire\Component;

class MonthlyTotalsReport extends Component
{
    public $page_title = '• Monthly Totals Report';

    public $currentYear;
    public $lastYears;
    public $selectedYear;

    public function mount()
    {
        $this->currentYear = Carbon::now()->format('Y');
        $this->selectedYear = Carbon::parse($this->currentYear)->format('Y');
        $this->lastYears = collect(range($this->currentYear - 5, $this->currentYear));
    }

    public function selectYear($year){
        $this->selectedYear = Carbon::createFromFormat('Y', $year)->format('Y');
    }


    public function render()
    {
        $monthlyStats  = Order::monthlyTotals($this->selectedYear)->get();

        $totalOrders = $monthlyStats->sum('total_orders');
        $totalAmount = $monthlyStats->sum('monthly_total_amount');
        $totalWeight = $monthlyStats->sum('monthly_total_weight');

        return view('livewire.reports.monthly-totals-report',[
            'monthlyStats' => $monthlyStats,
            'totalOrders' => $totalOrders,
            'totalAmount' => $totalAmount,
            'totalWeight' => $totalWeight,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'monthlyTotalsReport' => 'active']);
    }
}
