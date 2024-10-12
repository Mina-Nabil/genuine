<?php

namespace App\Livewire\Customers;

use App\Models\Customers\Customer;
use App\Models\Customers\Zone;
use Livewire\Component;
use App\Traits\AlertFrontEnd;
use Illuminate\Routing\Route;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class CustomerIndex extends Component
{
    use WithFileUploads, AlertFrontEnd, WithPagination;
    public $page_title = '• Customers';

    public $fetched_customers_IDs;
    public $search;
    public $selectAll = false; //to select all in the page
    public $selectedCustomers = [];
    public $newCustomerSection = false;
    public $selectedAllCustomers = false;//to select all customers

    public $fullName;
    public $phone;
    public $zone_id;
    public $locationUrl;
    public $address;

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedCustomers = $this->fetched_customers_IDs;
        } else {
            $this->selectedCustomers = [];
        }
    }

    public function selectAllCustomers(){
        $this->selectedAllCustomers = true;
        $this->selectedCustomers = Customer::pluck('id')->toArray();
    }

    public function undoSelectAllCustomers(){
        $this->selectedAllCustomers = false;
        $this->selectedCustomers = $this->fetched_customers_IDs;
    }


    ///// Frontend Hnadling
    public function openNewCustomerSec()
    {
        $this->newCustomerSection = true;
    }

    public function closeNewCustomerSec()
    {
        $this->reset(['fullName', 'phone', 'zone_id', 'locationUrl', 'address', 'newCustomerSection']);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function addNewCustomer()
    {
        $this->validate([
            'fullName' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'zone_id' => 'nullable|exists:zones,id',
            'locationUrl' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        $res = Customer::newCustomer($this->fullName,$this->address,$this->phone,$this->locationUrl,$this->zone_id);

        if($res){
            return redirect(route('customer.show',$res->id));
        }else{
            $this->alertFailed();
        }
    }

    public function render()
    {
        $ZONES = Zone::select('id', 'name')->get();
        $customers = Customer::when($this->search, fn($q) => $q->search($this->search))->paginate(50);
        $this->fetched_customers_IDs = $customers->pluck('id')->toArray();
        return view('livewire.customers.customer-index', [
            'customers' => $customers,
            'ZONES' => $ZONES
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'customers' => 'active']);
    }
}
