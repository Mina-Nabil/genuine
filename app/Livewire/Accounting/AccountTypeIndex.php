<?php

namespace App\Livewire\Accounting;

use Livewire\Component;
use Illuminate\Http\Request;
use App\Models\Accounting\AccountType;
use App\Traits\AlertFrontEnd;
use Livewire\WithPagination;

class AccountTypeIndex extends Component
{
    use WithPagination, AlertFrontEnd;

    public $page_title = '• Account Types';
    public $searchText;

    public $accountTypeId;
    public $Type_name;
    public $Type_desc;

    public $isAddNewModalOpen = false;

    public function updatedSearchText()
    {
        $this->resetPage();
    }

    public function loadAccountType($id)
    {
        $accountType = AccountType::findOrFail($id);
        $this->accountTypeId = $id;
        $this->Type_name = $accountType->name;
        $this->Type_desc = $accountType->desc;
        $this->isAddNewModalOpen = true;
    }

    public function closeEditModal()
    {
        $this->reset(['accountTypeId', 'Type_name', 'Type_desc', 'isAddNewModalOpen']);
    }

    public function openNewTypeModal()
    {
        $this->isAddNewModalOpen = true;
    }

    public function save()
    {
        $this->validate([
            'Type_name' => 'required|string|max:100',
            'Type_desc' => 'nullable|string|max:255',
        ]);

        if ($this->accountTypeId) {
            $res = AccountType::findOrFail($this->accountTypeId)->editInfo($this->Type_name, $this->Type_desc);
            if ($res) {
                $this->alert('success', 'Account type updated!');
            } else {
                $this->alert('failed', 'Server error');
            }
        } else {
            AccountType::newAccountType($this->Type_name, $this->Type_desc);
            $res = $this->alert('success', 'New account type added!');
            if ($res) {
                $this->alert('success', 'Account type added!');
            } else {
                $this->alert('failed', 'Server error');
            }
        }

        $this->closeEditModal();
    }

    public function render()
    {
        $accountsTypes = AccountType::search($this->searchText)->paginate(30);

        return view('livewire.Accounting.account-type-index', [
            'accountsTypes' => $accountsTypes,
        ])->layout('layouts.accounting', ['page_title' => $this->page_title , 'account_types' => 'active'  ]);
    }
}
