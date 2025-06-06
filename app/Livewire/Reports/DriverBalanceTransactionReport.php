<?php

namespace App\Livewire\Reports;

use App\Models\Payments\BalanceTransaction;
use App\Models\Payments\CustomerPayment;
use App\Models\Users\Driver;
use App\Models\Users\User;
use App\Traits\AlertFrontEnd;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class DriverBalanceTransactionReport extends Component
{
    use WithPagination, AlertFrontEnd;
    public $page_title = '• حساب مندوب';

    public $userId;

    protected $queryString = ['userId'];

    public $user;

    public $fromDate = '2023-01-01';
    public $toDate = '2023-06-01';

    protected $listeners = ['dateRangeSelected'];

    public function dateRangeSelected($data)
    {
        $this->fromDate = $data[0];
        $this->toDate = $data[1];
        $this->resetPage();
    }

    public function ChangeUser($id)
    {
        if (Auth::user()->is_driver) {
            return;
        }
        $this->user = User::find($id);
        $this->userId = $this->user->id;
    }

    // START: Add to Driver Balance Section
    public $isOpenAddDriverTrans;
    public $driverAmount;
    public $driverPymtNote;
    public $driverPayFrom;

    public function openAddDriverTrans()
    {
        $this->authorize('updateBalance', $this->user);
        $this->isOpenAddDriverTrans = true;
    }

    public function closeAddDriverTrans()
    {
        $this->reset(['isOpenAddDriverTrans', 'driverAmount', 'driverPymtNote']);
    }

    public function addDriverTrnasaction()
    {
        $this->authorize('updateBalance', $this->user);
        $this->validate([
            'driverAmount' => 'required|numeric',
            'driverPymtNote' => 'required|string|max:255',
            'driverPayFrom' => 'nullable|in:' . implode(',', CustomerPayment::PAYMENT_METHODS),
        ]);

        if (
            str_contains($this->driverPymtNote, BalanceTransaction::WD_TYPE_ADVANCE)
            || str_contains($this->driverPymtNote, BalanceTransaction::WD_TYPE_SALARY)
        ) {
            $this->driverAmount = -1 * $this->driverAmount;
        }

        $res = User::findOrFail($this->userId)->addDriverBalance($this->driverAmount, $this->driverPymtNote, $this->driverPayFrom);

        if ($res) {
            $this->closeAddDriverTrans();
            $this->alertSuccess('payment added!');
        } else {
            $this->alertFailed();
        }
    }
    // END: Add to Driver Balance Section

    public function render()
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
        
        $drivers = User::where('type', User::TYPE_DRIVER)->get();

        $query = BalanceTransaction::userTransactions($this->userId);

        if ($this->fromDate && $this->toDate) {
            $query = $query->dateRange($this->fromDate, $this->toDate);
        }

        $transactions = $query->latest()->paginate(50);

        $totalOrderDelivery = $query->clone()->totalOrderDelivery();
        $countOrderDelivery = $query->clone()->countOrderDelivery();
        $totalStartDayDelivery = $query->clone()->totalStartDayDelivery();
        $totalReturn = $query->clone()->totalReturn();

        $sumOfAdvance = $query->clone()->withdrawalTypeSum(BalanceTransaction::WD_TYPE_ADVANCE);
        $sumOfSalary = $query->clone()->withdrawalTypeSum(BalanceTransaction::WD_TYPE_SALARY);
        $sumOfX2 = $query->clone()->withdrawalTypeSum(BalanceTransaction::WD_TYPE_X2);
        $sumOfRoadFees = $query->clone()->withdrawalTypeSum(BalanceTransaction::WD_TYPE_ROAD_FEES);
        $sumOfPurchases = $query->clone()->withdrawalTypeSum(BalanceTransaction::WD_TYPE_PURCHASES);

        $WITHDRAWAL_TYPES = BalanceTransaction::WITHDRAWAL_TYPES;
        $PAYMENT_TYPES = CustomerPayment::PAYMENT_METHODS;

        return view('livewire.reports.driver-balance-transaction-report', [
            'drivers' => $drivers,
            'transactions' => $transactions,
            'totalOrderDelivery' => $totalOrderDelivery,
            'countOrderDelivery' => $countOrderDelivery,
            'totalStartDayDelivery' => $totalStartDayDelivery,
            'totalReturn' => $totalReturn,
            'sumOfAdvance' => $sumOfAdvance,
            'sumOfSalary' => $sumOfSalary,
            'sumOfX2' => $sumOfX2,
            'sumOfRoadFees' => $sumOfRoadFees,
            'sumOfPurchases' => $sumOfPurchases,
            'WITHDRAWAL_TYPES' => $WITHDRAWAL_TYPES,
            'PAYMENT_METHODS' => $PAYMENT_TYPES,
        ])->layout('layouts.app', [
            'page_title' => $this->page_title,
            'driverTransactions' => 'active',
        ]);
    }
}
