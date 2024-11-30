<?php

namespace App\Livewire\Customers;

use App\Models\Customers\Customer;
use App\Models\Customers\Zone;
use App\Models\Pets\Pet;
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
    public $selectedAllCustomers = false; //to select all customers

    public $fullName;
    public $phone;
    public $zone_id;
    public $locationUrl;
    public $address;

    public $pets = [];

    public function mount()
    {
        // Initialize the pets array with the first category set as default
        $firstCategory = Pet::CATEGORIES[0]; // Assuming Pet::CATEGORIES is an array of categories
        $this->pets[] = [
            'name' => '',
            'category' => $firstCategory,
            'type' => '',
            'bdate' => '',
            'types' => Pet::getDistinctPetTypes($firstCategory), // Set types based on the default category
        ];
    }

    public function addPet()
    {
        $firstCategory = Pet::CATEGORIES[0];
        $this->pets[] = [
            'name' => '',
            'category' => $firstCategory,
            'type' => '',
            'bdate' => '',
            'pet_years' => '',
            'pet_months' => '',
            'pet_days' => '',
            'types' => Pet::getDistinctPetTypes($firstCategory), // Initialize types based on the default category
        ];
    }

    public function removePet($index)
    {
        unset($this->pets[$index]);
        $this->pets = array_values($this->pets); // Reset array keys
    }

    public function updatedPets()
    {
        foreach ($this->pets as $index => $pet) {
            if (isset($pet['category'])) {
                // Fetch and update distinct pet types based on the selected category
                $this->pets[$index]['types'] = Pet::getDistinctPetTypes($pet['category']);
            } else {
                $this->pets[$index]['types'] = []; // Clear pet types if no category is selected
            }
        }
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
        $this->authorize('create', Customer::class);

        foreach ($this->pets as $index => $pet) {
            $this->pets[$index]['bdate'] = Pet::calculateBirthDate($pet['pet_years'],$pet['pet_months'],$pet['pet_days']);
        }
        
        
        $this->validate([
            'fullName' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'zone_id' => 'nullable|exists:zones,id',
            'locationUrl' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:255',
            'pets.*.name' => 'nullable|string|max:255',
            'pets.*.category' => 'required|in:' . implode(',', Pet::CATEGORIES),
            'pets.*.type' => 'required|string|max:255',
            'pets.*.bdate' => 'required|date',
        ],attributes:[
            'pets.*.name' => 'pet name',
            'pets.*.category' => 'pet category',
            'pets.*.type' => 'pet type',
            'pets.*.bdate' => 'pet name',
        ]);

        $res = Customer::newCustomer($this->fullName, $this->address, $this->phone, $this->locationUrl, $this->zone_id);

        foreach ($this->pets as $pet) {
            $res->addPet($pet['name'], $pet['category'], $pet['type'], $pet['bdate']);
        }

        if ($res) {
            return redirect(route('customer.show', $res->id));
        } else {
            $this->alertFailed();
        }
    }

    public function render()
    {
        $ZONES = Zone::select('id', 'name')->get();
        $customers = Customer::when($this->search, fn($q) => $q->search($this->search))->paginate(50);
        $this->fetched_customers_IDs = $customers->pluck('id')->toArray();
        $PET_CATEGORIES = Pet::CATEGORIES;
        return view('livewire.customers.customer-index', [
            'customers' => $customers,
            'ZONES' => $ZONES,
            'PET_CATEGORIES' => $PET_CATEGORIES,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'customers' => 'active']);
    }
}
