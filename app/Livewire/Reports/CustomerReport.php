<?php

namespace App\Livewire\Reports;

use App\Models\Customers\Customer;
use App\Models\Customers\Zone;
use App\Models\Pets\Pet;
use App\Models\Users\User;
use Livewire\Component;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class CustomerReport extends Component
{
    use WithFileUploads, AlertFrontEnd, WithPagination;
    public $page_title = '• Customers Report';

    public $fetched_customers_IDs;
    public $search;
    public $selectAll = false; //to select all in the page
    public $selectedCustomers = [];
    public $newCustomerSection = false;
    public $selectedAllCustomers = false; //to select all customers

    public $zone;
    public $Edited_zoneId_sec = false;
    public $Edited_zoneId;

    public $creator;
    public $Edited_creatorId_sec = false;
    public $Edited_creatorId;

    public $creation_date_from;
    public $creation_date_to;
    public $edited_creation_date_from;
    public $edited_creation_date_to;
    public $Edited_creation_date_from_sec = false;

    public function openFilterCreationDate()
    {
        $this->Edited_creation_date_from_sec = true;
        $this->edited_creation_date_from = $this->creation_date_from;
        $this->edited_creation_date_to = $this->creation_date_to;
    }

    public function closeFilterCreationDate()
    {
        $this->Edited_creation_date_from_sec = false;
        $this->edited_creation_date_from = null;
        $this->edited_creation_date_to = null;
    }

    public function setFilterCreationDate()
    {
        $this->creation_date_from = $this->edited_creation_date_from;
        $this->creation_date_to = $this->edited_creation_date_to;
        $this->closeFilterCreationDate();
    }

    public function clearFilterCreationDates(){
        $this->reset(['creation_date_from' , 'creation_date_to']);
    }

    public function openFilteryCreator()
    {
        $this->Edited_creatorId_sec = true;
        $this->Edited_creatorId = $this->creator?->id;
    }

    public function closeFilterCreator()
    {
        $this->Edited_creatorId_sec = false;
        $this->Edited_creatorId = null;
    }

    public function setFilterCreator()
    {
        $this->creator = User::findOrFail($this->Edited_creatorId);
        $this->closeFilterCreator();
    }

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

    public function exportReport(){
        return Customer::exportReport(
            $this->search,
            $this->zone?->id,
            Carbon::parse($this->creation_date_from),
            Carbon::parse($this->creation_date_to),
            $this->creator?->id
        );
    }

    public function render()
    {
        $ZONES = Zone::select('id', 'name')->get();
        $users = User::all();
        $customers = Customer::Report(
            $this->search,
            $this->zone?->id,
            $this->creation_date_from ? Carbon::parse($this->creation_date_from) : null,
            $this->creation_date_from ? Carbon::parse($this->creation_date_to) : null,
            $this->creator?->id
            )->paginate(50);
        $this->fetched_customers_IDs = $customers->pluck('id')->toArray();
        return view('livewire.reports.customer-report', [
            'customers' => $customers,
            'ZONES' => $ZONES,
            'users' => $users,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'customerReport' => 'active']);
    }
}
