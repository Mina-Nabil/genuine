<?php

namespace App\Livewire\Admin;

use App\Models\Users\Driver;
use App\Models\Users\User;
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

        $usersStatistics = User::orderStatisticsBetween($fromDate,$toDate)->get();
        
        // Ensure the data is properly sent to the view
        return view('livewire.admin.dashboard', [
            'usersStatistics' => $usersStatistics,
            'totalOrdersWeight' => $usersStatistics->sum('total_weight'),
            'totalOrdersCount' => $usersStatistics->sum('total_orders'),
            'totalAmount' =>$usersStatistics->sum('total_amount'),
            'totalPaid' =>$usersStatistics->sum('total_paid'),
        ])->layout('layouts.app', ['dashboard' => 'active']);
    }
}
