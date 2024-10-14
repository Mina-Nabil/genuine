<?php

namespace App\Livewire\Customers;

use App\Models\Customers\Followup;
use Livewire\Component;
use App\Traits\AlertFrontEnd;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class FollowupIndex extends Component
{
    use WithFileUploads, AlertFrontEnd, WithPagination;
    public $page_title = '• Follow-ups';

    public $fetched_followups_IDs;
    public $search;
    public $selectAll = false; //to select all in the page
    public $selectedFollowups = [];
    public $editFollowupSection = null;
    public $selectedAllFollowups = false;//to select all customers

    public $title;
    public $call_time;
    public $desc;

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedFollowups = $this->fetched_customers_IDs;
        } else {
            $this->selectedFollowups = [];
        }
    }

    public function selectAllFollowups(){
        $this->selectedAllFollowups = true;
        $this->selectedFollowups = Followup::pluck('id')->toArray();
    }

    public function undoSelectAllFollowups(){
        $this->selectedAllFollowups = false;
        $this->selectedFollowups = $this->fetched_customers_IDs;
    }


    ///// Frontend Hnadling
    public function openEditInfoSec($id)
    {
        $this->editFollowupSection = $id;
        $followup = Followup::findOrFail($id);
        $this->title  = $followup->title;
        $this->call_time = $followup->call_time;
        $this->desc = $followup->desc;
    }

    public function closeEditInfoSec()
    {
        $this->reset(['title', 'call_time', 'desc', 'editFollowupSection']);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function editInfo()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'call_time' => 'nullable|date|after_or_equal:now',  
            'desc' => 'nullable|string|max:1000', 
        ]);

        $res =  Followup::findOrFail($this->editFollowupSection)->editInfo($this->$this->title,$this->call_time,$this->desc);

        if($res){
            $this->closeEditInfoSec();
            $this->alertSuccess('Followup updated!');
        }else{
            $this->alertFailed();
        }
    }

    public function render()
    {
        return view('livewire.customers.followup-index')->layout('layouts.app', ['page_title' => $this->page_title, 'followups' => 'active']);
    }
}
