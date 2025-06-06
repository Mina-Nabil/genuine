<?php

namespace App\Livewire\Customers;

use App\Models\Customers\Customer;
use App\Models\Customers\Zone;
use App\Models\Pets\Pet;
use Livewire\Component;
use App\Traits\AlertFrontEnd;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Orders\PeriodicOrder;

class CustomerIndex extends Component
{
    use WithFileUploads, AlertFrontEnd, WithPagination;
    public $page_title = '• Customers';

    public $search;
    public $newCustomerSection = false;

    public $fullName;
    public $phone;
    public $zone_id;
    public $locationUrl;
    public $address;
    public $periodic_type;

    public $pets = [];

    public $zone;
    public $Edited_zoneId_sec = false;
    public $Edited_zoneId;


    public function openFilteryZone()
    {
        $this->Edited_zoneId_sec = true;
        $this->Edited_zoneId = $this->zone?->id;
    }

    public function closeFilteryZone()
    {
        $this->Edited_zoneId_sec = false;
        $this->Edited_zoneId = null;
    }

    public function setFilterZone()
    {
        $this->zone = Zone::findOrFail($this->Edited_zoneId);
        $this->closeFilteryZone();
    }

    public function clearProperty(string $propertyName)
    {
        // Check if the property exists before attempting to clear it
        if (property_exists($this, $propertyName)) {
            $this->$propertyName = null;
        }
    }

    public function mount()
    {
        /** @var User */
        $loggedInUser = Auth::user();
        Log::info($loggedInUser);

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

    ///// Frontend Hnadling
    public function openNewCustomerSec()
    {
        $this->newCustomerSection = true;
    }

    public function closeNewCustomerSec()
    {
        $this->reset(['fullName', 'phone', 'zone_id', 'locationUrl', 'address', 'periodic_type', 'newCustomerSection']);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function addNewCustomer()
    {
        $this->authorize('create', Customer::class);

        $this->validate([
            'fullName' => 'required|string|max:255',
            'phone' => 'required|string',
            'zone_id' => 'nullable|exists:zones,id',
            'locationUrl' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:255',
            'periodic_type' => 'nullable|in:' . implode(',', PeriodicOrder::PERIODIC_TYPES),
        ], attributes: [
            'pets.*.name' => 'pet name',
            'pets.*.category' => 'pet category',
            'pets.*.type' => 'pet type',
            'pets.*.bdate' => 'pet name',
        ]);

        $res = Customer::newCustomer($this->fullName, $this->address, $this->phone, $this->locationUrl, $this->zone_id, $this->periodic_type);

        if ($res) {
            return redirect(route('customer.show', $res->id));
        } else {
            $this->alertFailed();
        }
    }

    public function render()
    {
        $ZONES = Zone::select('id', 'name')->get();
        $customers = Customer::when($this->search, fn($q) => $q->search($this->search))->zone($this->zone?->id)->paginate(50);
        $PET_CATEGORIES = Pet::CATEGORIES;
        return view('livewire.customers.customer-index', [
            'customers' => $customers,
            'ZONES' => $ZONES,
            'PET_CATEGORIES' => $PET_CATEGORIES,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'customers' => 'active']);
    }
}
