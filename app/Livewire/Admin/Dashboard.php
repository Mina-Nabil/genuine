<?php

namespace App\Livewire\Admin;

use App\Models\Users\Driver;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class Dashboard extends Component
{
    use WithPagination;

    public $LogId;
    public $user;
    public $level;
    public $title;
    public $desc;
    public $time;

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

        // dd($this->fromDate,$this->toDate);
        $driversStatistics = Driver::orderStatisticsBetween($fromDate,$toDate)->get();

        $totalOrdersWeight = 0;
        $totalOrdersCount = 0;
        $totalRemaingToPay = 0;

        foreach ($driversStatistics as $driver){
            $totalOrdersWeight += $driver->total_weight;
            $totalOrdersCount += $driver->total_orders;
            $totalRemaingToPay += $driver->total_remaining_to_pay;
        }
        
        // Ensure the data is properly sent to the view
        return view('livewire.admin.dashboard', [
            'driversStatistics' => $driversStatistics,
            'totalOrdersWeight' => $totalOrdersWeight,
            'totalOrdersCount' => $totalOrdersCount,
            'totalRemaingToPay' => $totalRemaingToPay
        ]);
    }
}
