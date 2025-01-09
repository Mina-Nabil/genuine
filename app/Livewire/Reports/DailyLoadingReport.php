<?php

namespace App\Livewire\Reports;

use App\Models\Orders\Order;
use Livewire\Component;
use Carbon\Carbon;
use Livewire\WithPagination;

class DailyLoadingReport extends Component
{
    use WithPagination;
    public $page_title = '• Daily Loading Report';

    public $fromDate = '2023-01-01';
    public $toDate = '2023-06-01';
    protected $listeners = ['dateRangeSelected'];

    public function dateRangeSelected($data)
    {
        $this->fromDate = $data[0];
        $this->toDate = $data[1];
        $this->resetPage();
    }

    public function render()
    {
        $fromDate = Carbon::parse($this->fromDate);
        $toDate = Carbon::parse($this->toDate);

        $totals = Order::loadDailyLoadingReport($this->fromDate,$this->toDate);
        return view('livewire.reports.daily-loading-report', ['totals'  =>  $totals])
            ->layout('layouts.app', [
                'page_title'    => $this->page_title,
                'dailyLoading'   => 'active'
            ]);;
    }
}
