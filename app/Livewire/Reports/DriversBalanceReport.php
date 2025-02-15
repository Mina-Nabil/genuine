<?php

namespace App\Livewire\Reports;

use App\Models\Payments\BalanceTransaction;
use App\Models\Products\Transaction;
use App\Models\Users\Driver;
use App\Models\Users\User;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DriversBalanceReport extends Component
{
    public $creation_date_from;
    public $creation_date_to;
    public $edited_creation_date_from;
    public $edited_creation_date_to;
    public $Edited_creation_date_from_sec = false;

    public function openFilterCreationDate()
    {
        $this->Edited_creation_date_from_sec = true;
        $this->edited_creation_date_from = $this->creation_date_from;
        $this->edited_creation_date_to = $this->creation_date_to;
    }

    public function closeFilterCreationDate()
    {
        $this->Edited_creation_date_from_sec = false;
        $this->edited_creation_date_from = null;
        $this->edited_creation_date_to = null;
    }

    public function mount()
    {
        $this->creation_date_from = now()->startOfMonth()->toDateString();
        $this->creation_date_to = now()->toDateString();
    }

    public function setFilterCreationDate()
    {
        $this->creation_date_from = $this->edited_creation_date_from;
        $this->creation_date_to = $this->edited_creation_date_to;
        $this->closeFilterCreationDate();
    }

    public function clearFilterCreationDates()
    {
        $this->reset(['creation_date_from', 'creation_date_to']);
    }

    public function render()
    {
        $balanceQuery = BalanceTransaction::DriversOnly()->DateRange(Carbon::parse($this->creation_date_from), Carbon::parse($this->creation_date_to));

        $drivers = User::driver()->get();
        $driversArr = [];
        foreach ($drivers as $d) {
            $driverQuery = $balanceQuery->clone()->userTransactions($d->id);
            $driversArr[$d->id]['name'] = $d->full_name;
            $driversArr[$d->id]['balance'] = $d->balance;
            $driversArr[$d->id]['x2'] = $driverQuery->clone()->withdrawalTypeSum(BalanceTransaction::WD_TYPE_X2);
            $driversArr[$d->id]['orders_total'] = $driverQuery->clone()->totalOrderDelivery();
            $driversArr[$d->id]['orders_count'] = $driverQuery->clone()->countOrderDelivery();
            $driversArr[$d->id]['salary'] = $driverQuery->clone()->withdrawalTypeSum(BalanceTransaction::WD_TYPE_SALARY);
            $driversArr[$d->id]['solfa'] = $driverQuery->clone()->withdrawalTypeSum(BalanceTransaction::WD_TYPE_ADVANCE);
            $driversArr[$d->id]['road'] = $driverQuery->clone()->withdrawalTypeSum(BalanceTransaction::WD_TYPE_ROAD_FEES);
            $driversArr[$d->id]['purchases'] = $driverQuery->clone()->withdrawalTypeSum(BalanceTransaction::WD_TYPE_PURCHASES);
            $driversArr[$d->id]['start_day'] = $driverQuery->clone()->totalStartDayDelivery();
            $driversArr[$d->id]['return'] = $driverQuery->clone()->totalReturn();
        }

        return view('livewire.reports.drivers-balance-report', [
            'driversArr' => $driversArr,
        ])->layout('layouts.app', ['page_title' => 'Drivers Report', 'driversBalanceReport' => 'active']);
    }
}
