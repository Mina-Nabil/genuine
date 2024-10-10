<?php

namespace App\Livewire\Pets;

use App\Models\Customers\Customer;
use App\Models\Pets\Pet;
use Livewire\Component;
use App\Traits\AlertFrontEnd;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class PetIndex extends Component
{
    use WithFileUploads, AlertFrontEnd, WithPagination;
    public $page_title = '• Pets';

    public $fetched_pets_IDs;
    public $search;
    public $selectAll = false; //to select all in the page
    public $selectedPets = [];
    public $newPetSection = false;
    public $selectedAllPets = false; //to select all pets

    public $name;
    public $type = Pet::TYPE_DOG;
    public $bdate;

    public $searchCustomers;
    public $fetchedCustomers;
    public $selectedCustomer;

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedPets = $this->fetched_pets_IDs;
        } else {
            $this->selectedPets = [];
        }
    }

    public function selectAllPets()
    {
        $this->selectedAllPets = true;
        $this->selectedPets = Pet::pluck('id')->toArray();
    }

    public function undoSelectAllPets()
    {
        $this->selectedAllPets = false;
        $this->selectedPets = $this->fetched_pets_IDs;
    }

    ///// Frontend Hnadling
    public function openNewPetSec()
    {
        $this->newPetSection = true;
    }

    public function closeNewPetSec()
    {
        $this->reset(['name', 'type', 'bdate', 'selectedCustomer', 'newPetSection']);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSearchCustomers(){
        $this->fetchedCustomers = Customer::Search($this->searchCustomers)->limit(5)->get();
    }
    public function selectCustomer($id){
        $this->selectedCustomer = Customer::findOrFail($id);
        $this->searchCustomers = null;
    }

    public function clearCustomer(){
        $this->selectedCustomer = null;
    }

    public function addNewPet()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:' . implode(',', Pet::TYPES),
            'bdate' => 'required|date|before:today',
            'selectedCustomer.id' => 'required|exists:customers,id',
        ],attributes:[
            'bdate' => 'Birth date',
            'selectedCustomer.id' => 'customer',

        ]);

        $customerID = $this->selectedCustomer->id;

        $res = Pet::newPet($this->name, $this->type, $this->bdate, $customerID);

        if ($res) {
            $this->closeNewPetSec();
            $this->alertSuccess('Pet added!');
        } else {
            $this->alertFailed();
        }
    }

    public function render()
    {
        $customers = Customer::select('id', 'name')->get();
        $pets = Pet::when($this->search, fn($q) => $q->search($this->search))->paginate(50);
        $this->fetched_pets_IDs = $pets->pluck('id')->toArray();
        $PET_TYPES = Pet::TYPES;
        
        return view('livewire.pets.pet-index', [
            'pets' => $pets,
            'PET_TYPES' => $PET_TYPES,
            'customers' => $customers,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'pets' => 'active']);
    }
}
