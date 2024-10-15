<?php

namespace App\Livewire\Customers;

use App\Models\Customers\Customer;
use App\Models\Customers\Followup;
use App\Models\Customers\Zone;
use App\Models\Pets\Pet;
use Livewire\Component;
use App\Traits\AlertFrontEnd;
use App\Traits\ToggleSectionLivewire;
use Illuminate\Support\Arr;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CustomerShow extends Component
{
    use AlertFrontEnd, AuthorizesRequests, ToggleSectionLivewire;

    public $page_title;

    public $customer;
    public $section = 'profile';
    public $isOpenAddPetsSec = false;

    public $petCategory = Pet::CATEGORIES[0];
    public $petType;
    public $petBdate;
    public $petName;
    public $petNote;

    //followups
    public $addFollowupSection = false;
    public $followupTitle;
    public $followupCallDate;
    public $followupCallTime;
    public $followupDesc;
    public $followupId;
    public $deleteFollowupId;
    public $callerNoteSec = false;
    public $callerNotetype;
    public $callerNoteId;
    public $note;

    public $fullName;
    public $phone;
    public $zone_id;
    public $locationUrl;
    public $address;
    public $EditCustomerSection = false;

    protected $queryString = ['section'];
    protected $listeners = ['removePet'];

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
        $this->reset(['fullName', 'phone', 'zone_id', 'locationUrl', 'address']);
    }

    public function editCustomer()
    {
        $this->authorize('update', $this->customer);
        $this->validate([
            'fullName' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'zone_id' => 'nullable|exists:zones,id',
            'locationUrl' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        $res = $this->customer->editInfo($this->fullName, $this->address, $this->phone, $this->locationUrl, $this->zone_id);

        if ($res) {
            $this->closeEditCustomerSection();
            $this->alertSuccess('Customer updated!');
        } else {
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
        $this->reset(['petType', 'petBdate', 'petName', 'petNote']);
        $this->petCategory = Pet::CATEGORIES[0];
    }

    public function changeSection($section)
    {
        $this->section = $section;
        $this->mount($this->customer->id);
    }

    public function addPet()
    {
        $this->validate([
            'petCategory' => 'required|in:' . implode(',', Pet::CATEGORIES), // Must be a valid category
            'petType' => 'required|string|max:255', // Required string with a maximum length
            'petBdate' => 'required|date|before_or_equal:today', // Required date, should not be a future date
            'petName' => 'nullable|string|max:255', // Optional string with a maximum length
            'petNote' => 'nullable|string',
        ]);

        $res = $this->customer->addPet($this->petName, $this->petCategory, $this->petType, $this->petBdate, $this->petNote);

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
    }

    public function removePet($id)
    {
        $this->authorize('assign', Pet::class);
        $res = Pet::findOrFail($id)->unassignFromCustomer();

        if ($res) {
            $this->mount($this->customer->id);
            $this->alertSuccess('Pet removed!');
        } else {
            $this->alertFailed();
        }
    }

    //followups
    public function toggleCallerNote($type = null, $id = null)
    {
        $this->callerNotetype = $type;
        $this->callerNoteId = $id;
        $this->toggle($this->callerNoteSec);
    }
    public function submitCallerNote()
    {
        if ($this->callerNotetype === 'called') {
            $this->setFollowupAsCalled($this->callerNoteId, $this->note);
        } elseif ($this->callerNotetype === 'cancelled') {
            $this->setFollowupAsCancelled($this->callerNoteId, $this->note);
        }
    }
    public function closeEditFollowup()
    {
        $this->followupId = null;
        $this->followupTitle = null;
        $this->followupCallDate = null;
        $this->followupCallTime = null;
        $this->followupDesc = null;
    }
    public function closeFollowupSection()
    {
        $this->followupTitle = null;
        $this->followupCallDate = null;
        $this->followupCallTime = null;
        $this->followupDesc = null;
        $this->addFollowupSection = false;
    }
    public function OpenAddFollowupSection()
    {
        $this->addFollowupSection = true;
    }

    public function editThisFollowup($id)
    {
        $this->followupId = $id;
        $f = Followup::find($id);
        $this->followupTitle = $f->title;
        $combinedDateTime = new \DateTime($f->call_time);
        $this->followupCallDate = $combinedDateTime->format('Y-m-d');
        $this->followupCallTime = $combinedDateTime->format('H:i:s');
        $this->followupDesc = $f->desc;
    }

    public function deleteThisFollowup($id)
    {
        $this->deleteFollowupId = $id;
    }

    public function dismissDeleteFollowup()
    {
        $this->deleteFollowupId = null;
    }

    public function deleteFollowup()
    {
        $res = Followup::find($this->deleteFollowupId)->delete();
        if ($res) {
            $this->alert('success', 'Followup Deleted successfuly');
            $this->dismissDeleteFollowup();
            $this->mount($this->customer->id);
        } else {
            $this->alert('failed', 'server error');
        }
    }

    public function addFollowup()
    {
        $this->validate([
            'followupTitle' => 'required|string|max:255',
            'followupCallDate' => 'nullable|date',
            'followupCallTime' => 'nullable',
            'followupDesc' => 'nullable|string|max:255',
        ]);

        $combinedDateTimeString = $this->followupCallDate . ' ' . $this->followupCallTime;
        $combinedDateTime = new \DateTime($combinedDateTimeString);

        $customer = Customer::find($this->customer->id);

        $res = $customer->addFollowup($this->followupTitle, $combinedDateTime, $this->followupDesc);

        if ($res) {
            $this->alert('success', 'Followup added successfuly');
            $this->closeFollowupSection();
            $this->mount($this->customer->id);
            return redirect()->route('customer.show', $this->customer->id);
        } else {
            $this->alert('failed', 'server error');
        }
    }

    public function editFollowup()
    {
        $this->validate([
            'followupTitle' => 'required|string|max:255',
            'followupCallDate' => 'nullable|date',
            'followupCallTime' => 'nullable',
            'followupDesc' => 'nullable|string|max:255',
        ]);

        $combinedDateTimeString = $this->followupCallDate . ' ' . $this->followupCallTime;
        $combinedDateTime = new \DateTime($combinedDateTimeString);

        $followup = Followup::find($this->followupId);

        $res = $followup->editInfo($this->followupTitle, $combinedDateTime, $this->followupDesc);

        if ($res) {
            $this->alert('success', 'Followup updated successfuly');
            $this->closeEditFollowup();
            $this->mount($this->customer->id);
        } else {
            $this->alert('failed', 'server error');
        }
    }

    public function setFollowupAsCalled($id)
    {
        $res = Followup::find($id)->setAsCalled($this->note);
        if ($res) {
            $this->alert('success', 'Followup updated successfuly');
            $this->toggleCallerNote();
            $this->mount($this->customer->id);
        } else {
            $this->alert('failed', 'server error');
        }
    }

    public function setFollowupAsCancelled($id)
    {
        $res = Followup::find($id)->setAsCancelled($this->note);
        if ($res) {
            $this->alert('success', 'Followup updated successfuly');
            $this->toggleCallerNote();
            $this->mount($this->customer->id);
        } else {
            $this->alert('failed', 'server error');
        }
    }

    public function render()
    {
        $ZONES = Zone::select('id', 'name')->get();
        $PET_CATEGORIES = Pet::CATEGORIES;

        $PET_TYPES = Pet::getDistinctPetTypes($this->petCategory);
        return view('livewire.customers.customer-show', [
            'ZONES' => $ZONES,
            'PET_CATEGORIES' => $PET_CATEGORIES,
            'PET_TYPES' => $PET_TYPES,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'customers' => 'active']);
    }
}
