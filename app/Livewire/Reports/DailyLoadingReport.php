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
    public $deliveryDate;

    public function mount()
    {
        $this->deliveryDate = Carbon::today()->toDateString();
    }

    public function render()
    {
        $totals = Order::loadDailyLoadingReport($this->deliveryDate);
        return view('livewire.reports.daily-loading-report', ['totals'  =>  $totals])
            ->layout('layouts.app', [
                'page_title'    => $this->page_title,
                'productions'   => 'active'
            ]);;
    }
}
