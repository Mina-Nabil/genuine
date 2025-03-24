<?php

namespace App\Livewire\Reports;

use App\Models\Payments\CustomerPayment;
use App\Models\Payments\Title;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class PaymentMethodReport extends Component
{
    use AlertFrontEnd, WithPagination;

    public $page_title = '• Payment Method Report';

    #[Url]
    public $paymentMethod;
    public $Edited_paymentMethod;
    public $Edited_paymentMethod_sec = false;

    public $payment_date_from;
    public $payment_date_to;
    public $edited_payment_date_from;
    public $edited_payment_date_to;
    public $Edited_payment_date_from_sec = false;
    public $title_id;
    public $Edited_title_id;
    public $Edited_title_id_sec = false;

    public function mount()
    {
        $this->payment_date_from = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->payment_date_to = Carbon::now()->format('Y-m-d');
        $this->paymentMethod = CustomerPayment::PYMT_CASH; // Default to cash
    }

    public function openFilterTitle()
    {
        $this->Edited_title_id_sec = true;
        $this->Edited_title_id = $this->title_id;
    }

    public function closeFilterTitle()
    {
        $this->Edited_title_id_sec = false;
        $this->Edited_title_id = null;
    }

    public function setFilterTitle()
    {
        $this->title_id = $this->Edited_title_id;
        $this->closeFilterTitle();
        $this->resetPage();
    }

    public function openFilterPaymentMethod()
    {
        $this->Edited_paymentMethod_sec = true;
        $this->Edited_paymentMethod = $this->paymentMethod;
    }

    public function closeFilterPaymentMethod()
    {
        $this->Edited_paymentMethod_sec = false;
        $this->Edited_paymentMethod = null;
    }

    public function setFilterPaymentMethod()
    {
        $this->paymentMethod = $this->Edited_paymentMethod;
        $this->closeFilterPaymentMethod();
        $this->resetPage();
    }

    public function openFilterPaymentDate()
    {
        $this->Edited_payment_date_from_sec = true;
        $this->edited_payment_date_from = $this->payment_date_from;
        $this->edited_payment_date_to = $this->payment_date_to;
    }

    public function closeFilterPaymentDate()
    {
        $this->Edited_payment_date_from_sec = false;
        $this->edited_payment_date_from = null;
        $this->edited_payment_date_to = null;
    }

    public function setFilterPaymentDate()
    {
        $this->payment_date_from = $this->edited_payment_date_from;
        $this->payment_date_to = $this->edited_payment_date_to;
        $this->closeFilterPaymentDate();
        $this->resetPage();
    }

    public function clearFilterPaymentDates()
    {
        $this->reset(['payment_date_from', 'payment_date_to']);
        $this->resetPage();
    }

    public function render()
    {
        $payments = CustomerPayment::paymentMethodReport(
            $this->paymentMethod,
            $this->payment_date_from ? Carbon::parse($this->payment_date_from) : null,
            $this->payment_date_to ? Carbon::parse($this->payment_date_to) : null,
            $this->title_id
        )->get();

        // Calculate start and end balances
        $endBalance = $payments->last()->type_balance ?? 0;

        // Sum of all amounts in the current dataset
        $totalAmount = $payments->sum('amount');

        // Calculate starting balance
        $startBalance = $endBalance - $totalAmount;

        $PAYMENT_METHODS = CustomerPayment::PAYMENT_METHODS;

        $titles = Title::all();
        $selected_title = Title::find($this->title_id);

        return view('livewire.reports.payment-method-report', [
            'payments' => $payments,
            'PAYMENT_METHODS' => $PAYMENT_METHODS,
            'startBalance' => $startBalance,
            'endBalance' => $endBalance,
            'totalAmount' => $totalAmount,
            'titles' => $titles,
            'selected_title' => $selected_title,
        ])->layout('layouts.app', ['page_title' => 'تقرير الحسابات', 'paymentMethodReport' => 'active']);
    }
}
