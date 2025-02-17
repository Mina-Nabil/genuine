<?php

namespace App\Livewire\Admin;

use App\Models\Materials\RawMaterial;
use App\Models\Materials\SupplierRawMaterial;
use App\Models\Orders\Order;
use App\Models\Payments\BalanceTransaction;
use App\Models\Payments\CustomerPayment;
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

        $usersStatistics = User::orderStatisticsBetween($fromDate, $toDate)->get();

        $materialsUnderLimit = RawMaterial::UnderLimit()->get();
        $nearlyExpiredMaterials = SupplierRawMaterial::NearlyExpired()->get();
        $expiredMaterials = SupplierRawMaterial::Expired()->get();
        $totalActiveOrdersCount = Order::search()
            ->OpenOrders()
            ->sortByDeliveryDate()
            ->notDebitOrders()
            ->selectRaw('Count(orders.id) as active_orders')->first()?->active_orders;


        $cashBalance = CustomerPayment::PaymentMethod(CustomerPayment::PYMT_CASH)->limit(1)->latest()->first()?->type_balance ?? 0;
        $bankBalance = CustomerPayment::PaymentMethod(CustomerPayment::PYMT_BANK_TRANSFER)->limit(1)->latest()->first()?->type_balance ?? 0;
        $walletBalance = CustomerPayment::PaymentMethod(CustomerPayment::PYMT_WALLET)->limit(1)->latest()->first()?->type_balance ?? 0;


        // Ensure the data is properly sent to the view
        return view('livewire.admin.dashboard', [
            'usersStatistics'       => $usersStatistics,
            'totalOrdersWeight'     => $usersStatistics->sum('total_weight'),
            'totalOrdersCount'      => $usersStatistics->sum('total_orders'),
            'totalAmount'           => $usersStatistics->sum('total_amount'),
            'totalPaid'             => $usersStatistics->sum('total_paid'),
            'materialsUnderLimit'   => $materialsUnderLimit,
            'nearlyExpiredMaterials' => $nearlyExpiredMaterials,
            'totalActiveOrdersCount' => $totalActiveOrdersCount,
            'expiredMaterials'      => $expiredMaterials,
            'cashBalance'           => $cashBalance,
            'bankBalance'           => $bankBalance,
            'walletBalance'         => $walletBalance,
        ])->layout('layouts.app', ['dashboard' => 'active']);
    }
}
