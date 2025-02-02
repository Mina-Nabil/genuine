<?php

namespace App\Livewire\Reports;

use App\Models\Payments\BalanceTransaction;
use App\Models\Users\Driver;
use App\Models\Users\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class DriverBalanceTransactionReport extends Component
{
    use WithPagination;
    public $page_title = '• حساب مندوب';

    public $userId;

    protected $queryString = ['userId'];

    public $user;

    public function mount()
    {
        if (Auth::user()->is_driver) {
            if (!$this->user) {
                $this->user = Auth::user();
                $this->userId = $this->user->id;
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
        if (Auth::user()->is_driver) return;
        $this->user = User::find($id);
        $this->userId = $this->user->id;
    }

    public function render()
    {
        if (Auth::user()->is_driver) $this->mount();
        $transactions = BalanceTransaction::UserTransactions($this->userId)->latest()->paginate(50);
        $this->userId = $this->user->id;
        $drivers = User::where('type',User::TYPE_DRIVER)->get();
        return view('livewire.reports.driver-balance-transaction-report', [
            'drivers' => $drivers,
            'transactions' => $transactions
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'driverTransactions' => 'active']);
    }
}
