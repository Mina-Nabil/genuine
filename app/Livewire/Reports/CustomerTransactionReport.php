<?php

namespace App\Livewire\Reports;

use App\Models\Customers\Customer;
use App\Models\Customers\Zone;
use App\Models\Payments\CustomerPayment;
use App\Models\Pets\Pet;
use App\Models\Users\Driver;
use App\Models\Users\User;
use Livewire\Component;
use App\Traits\AlertFrontEnd;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Payments\Title;

class CustomerTransactionReport extends Component
{
    use WithFileUploads, AlertFrontEnd, WithPagination;
    public $page_title = '• Customers';

    public $fetched_customers_IDs;
    public $search;
    public $selectAll = false; //to select all in the page
    public $selectedCustomers = [];
    public $newCustomerSection = false;
    public $selectedAllCustomers = false; //to select all customers

    //ADD TRANSACTION
    public $isOpenAddTrans;
    public $amount;
    public $paymentMethod = CustomerPayment::PYMT_CASH;
    public $note;

    public $section = CustomerPayment::PYMT_CASH;
    protected $queryString = ['section'];

    public $title_id;

    public function mount(){
        $this->section = CustomerPayment::PYMT_CASH;
        $this->title_id = null;
    }

    public function openAddTransSec(){
        $this->isOpenAddTrans = true;
    }

    public function closeAddTransSec(){
        $this->reset(['isOpenAddTrans','amount','note','title_id']);
    }

    public function updatedTitleId($value)
    {
        if (!$value) return;
        
        $title = Title::find($value);
        if (!$title) return;

        if ($this->amount) {
            $this->validateAmount($title, $this->amount);
        }
    }

    public function updatedAmount($value)
    {
        if (!$this->title_id) return;
        
        $title = Title::find($this->title_id);
        if (!$title) return;

        $this->validateAmount($title, $value);
    }

    private function validateAmount($title, $value)
    {
        if (!$value) return;

        $amount = floatval($value);
        
        // Check if user is authorized to exceed limit
        if (!Auth::user()->can('update', $title) && $amount > $title->limit) {
            $this->addError('amount', "Amount exceeds the limit of " . number_format($title->limit, 2) . " for this payment title.");
        } else {
            $this->resetErrorBag('amount');
        }
    }

    public function addTransaction()
    {
        $this->validate([
            'amount' => 'required|numeric',
            'note' => 'required|string|max:255',
            'paymentMethod' => 'required|in:' . implode(',', CustomerPayment::PAYMENT_METHODS),
            'title_id' => 'nullable|exists:payment_titles,id',
        ]);

        // Additional validation for title limit
        if ($this->title_id) {
            $title = Title::find($this->title_id);
            if (!Auth::user()->can('update', $title) && $this->amount > $title->limit) {
                $this->addError('amount', "Amount exceeds the limit of " . number_format($title->limit, 2) . " for this payment title.");
                return;
            }
        }

        $res = CustomerPayment::createPayment(
            $this->amount,
            $this->paymentMethod,
            $this->note,
            $this->title_id
        );

        if ($res) {
            $this->closeAddTransSec();
            $this->alertSuccess('Added Successfully!');
        } else {
            $this->alertFailed();
        }
    }

    public function changeSection($section)
    {
        $this->section = $section;
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedCustomers = $this->fetched_customers_IDs;
        } else {
            $this->selectedCustomers = [];
        }
    }

    public function selectAllCustomers()
    {
        $this->selectedAllCustomers = true;
        $this->selectedCustomers = Customer::pluck('id')->toArray();
    }

    public function undoSelectAllCustomers()
    {
        $this->selectedAllCustomers = false;
        $this->selectedCustomers = $this->fetched_customers_IDs;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $payments = CustomerPayment::when($this->search, fn($q) => $q->search($this->search))
            ->paymentMethod($this->section)
            ->orderByDesc('customer_payments.id')
            ->paginate(50);
        $driverUsers = User::where('type',User::TYPE_DRIVER)->get();
        $this->fetched_customers_IDs = $payments->pluck('id')->toArray();
        $PAYMENT_METHODS = CustomerPayment::PAYMENT_METHODS;
        
        return view('livewire.reports.customer-transaction-report', [
            'payments' => $payments,
            'PAYMENT_METHODS' => $PAYMENT_METHODS,
            'driverUsers' => $driverUsers,
            'paymentTitles' => Title::all()
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'customerTransReport' => 'active']);
    }
}
