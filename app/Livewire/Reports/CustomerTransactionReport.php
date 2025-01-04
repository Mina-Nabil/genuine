<?php

namespace App\Livewire\Reports;

use App\Models\Customers\Customer;
use App\Models\Customers\Zone;
use App\Models\Payments\CustomerPayment;
use App\Models\Pets\Pet;
use Livewire\Component;
use App\Traits\AlertFrontEnd;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

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

    public $section = CustomerPayment::PYMT_CASH;
    protected $queryString = ['section'];

    public function mount(){
        $this->section = CustomerPayment::PYMT_CASH;
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
            ->orderByDesc('id')
            ->latest()
            ->paginate(50);
        $this->fetched_customers_IDs = $payments->pluck('id')->toArray();
        $PAYMENT_METHODS = CustomerPayment::PAYMENT_METHODS;
        return view('livewire.reports.customer-transaction-report', [
            'payments' => $payments,
            'PAYMENT_METHODS' => $PAYMENT_METHODS
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'customerTransReport' => 'active']);;
    }
}
