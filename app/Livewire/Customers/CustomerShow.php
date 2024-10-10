<?php

namespace App\Livewire\Customers;

use App\Models\Customers\Customer;
use Livewire\Component;

class CustomerShow extends Component
{
    public $page_title;

    public $customer;
    public $section = 'profile';

    protected $queryString = ['section'];

    public function changeSection($section)
    {
        $this->section = $section;
        $this->mount($this->customer->id);
    }

    public function mount($id){
        $this->customer = Customer::findOrFail($id);
        $this->page_title = '• '.$this->customer->name;
    }

    public function render()
    {
        return view('livewire.customers.customer-show')->layout('layouts.app', ['page_title' => $this->page_title, 'customers' => 'active']);
    }
}
