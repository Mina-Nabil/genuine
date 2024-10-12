<?php

namespace App\Livewire\Customers;

use App\Models\Customers\Customer;
use App\Models\Customers\Zone;
use App\Models\Pets\Pet;
use Livewire\Component;
use App\Traits\AlertFrontEnd;
use Illuminate\Support\Arr;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CustomerShow extends Component
{
    use AlertFrontEnd,AuthorizesRequests;

    public $page_title;

    public $customer;
    public $section = 'profile';
    public $isOpenAddPetsSec = false;

    public $All_pets;
    public $selectedPets = [];
    public $searchPets;

    public $fullName;
    public $phone;
    public $zone_id;
    public $locationUrl;
    public $address;
    public $EditCustomerSection = false;

    protected $queryString = ['section'];
    protected $listeners = ['removePet'];

    public function updatedSearchPets()
    {
        $this->All_pets = Pet::where('customer_id', null)
            ->search($this->searchPets)
            ->limit(10)
            ->get();
    }

    public function showEditCustomerSection()
    {
        // dd('ssss');
        $this->EditCustomerSection = true;
        $this->fullName = $this->customer->name;
        $this->phone = $this->customer->phone;
        $this->zone_id = $this->customer->zone->id;
        $this->locationUrl = $this->customer->location_url;
        $this->address = $this->customer->address;
    }

    public function closeEditCustomerSection()
    {
        $this->EditCustomerSection = false;
        $this->reset(['fullName' , 'phone', 'zone_id' , 'locationUrl' , 'address' ]);
    }

    public function editCustomer(){
        $this->authorize('update' , $this->customer);
        $this->validate([
            'fullName' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'zone_id' => 'nullable|exists:zones,id',
            'locationUrl' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        $res = $this->customer->editInfo($this->fullName,$this->address,$this->phone,$this->locationUrl,$this->zone_id);

        if($res){
            $this->closeEditCustomerSection();
            $this->alertSuccess('Customer updated!');
        }else{
            $this->alertFailed();
        }
    }

    public function showAddPetsSection()
    {
        $this->isOpenAddPetsSec = true;
    }

    public function closeAddPetsSection()
    {
        $this->isOpenAddPetsSec = false;
        $this->reset(['selectedPets','searchPets']);
    }

    public function changeSection($section)
    {
        $this->section = $section;
        $this->mount($this->customer->id);
    }

    public function assignPets()
    {
        
        if(empty($this->selectedPets)){
            $this->closeAddPetsSection();
            return;
        }
        // Assuming $index is the key you're trying to pluck
        $petsIds =  array_keys($this->selectedPets, true);
        $this->authorize('assign' , Pet::class);

        $res = Pet::reassignToCustomer($petsIds , $this->customer->id);

        if ($res) {
            $this->mount($this->customer->id);
            $this->closeAddPetsSection();
            $this->alertSuccess('Pets added!');
        } else {
            $this->alertFailed();
        }
    }

    public function mount($id)
    {
        $this->customer = Customer::findOrFail($id);
        $this->page_title = '• ' . $this->customer->name;
        $this->All_pets = Pet::where('customer_id', null)->limit(10)->get();
    }

    public function removePet($id)
    {
        $this->authorize('assign' , Pet::class);
        $res = Pet::findOrFail($id)->unassignFromCustomer();

        if ($res) {
            $this->mount($this->customer->id);
            $this->alertSuccess('Pet removed!');
        } else {
            $this->alertFailed();
        }
    }

    public function render()
    {
        $ZONES = Zone::select('id', 'name')->get();
        return view('livewire.customers.customer-show',[
            'ZONES' => $ZONES
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'customers' => 'active']);
    }
}
