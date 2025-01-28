<?php

namespace App\Livewire\Reports;

use App\Models\Users\Driver;
use App\Models\Users\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DriverBalanceTransactionReport extends Component
{
    public $userId;

    protected $queryString = ['userId'];

    public $user;

    public function mount()
    {
        if (Auth::user()->is_driver) {
            if (!$this->user) {
                $this->user = Auth::user();
            }
        } else {
            if ($this->userId) {
                $this->user = User::find($this->userId);
            } else {
                $this->user = Driver::first()->user;
                $this->userId = $this->user->id;
            }
        }
    }

    public function ChangeUser($id){
        $this->user = User::find($id);
        $this->userId = $this->user->id;
    }

    public function render()
    {
        $this->userId = $this->user->id;
        $drivers = Driver::all();
        return view('livewire.reports.driver-balance-transaction-report', [
            'drivers' => $drivers,
        ]);
    }
}
