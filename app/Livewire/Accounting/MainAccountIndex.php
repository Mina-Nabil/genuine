<?php

namespace App\Livewire\Accounting;

use App\Models\Accounting\MainAccount;
use Livewire\Component;
use App\Traits\AlertFrontEnd;
use Livewire\WithPagination;
use Illuminate\Http\Request;

class MainAccountIndex extends Component
{
    use WithPagination, AlertFrontEnd;

    public $page_title = '• Main Accounts';
    public $searchText;

    public $mainAccountId;
    public $account_name;
    public $account_type;
    public $account_desc;

    public $isAddNewModalOpen = false;

    public function updatedSearchText()
    {
        $this->resetPage();
    }

    public function loadAccountType($id)
    {
        $acc = MainAccount::findOrFail($id);
        $this->mainAccountId = $id;
        $this->account_name = $acc->name;
        $this->account_type = $acc->type;
        $this->account_desc = $acc->desc;
        $this->isAddNewModalOpen = true;
    }

    public function closeEditModal()
    {
        $this->reset(['mainAccountId', 'account_name', 'account_type', 'account_desc','isAddNewModalOpen']);
    }

    public function openNewTypeModal()
    {
        $this->isAddNewModalOpen = true;
    }

    public function save()
    {
        $this->validate([
            'account_name' => 'required|string|max:100',
            'account_type' => 'required|in:' . implode(',', MainAccount::TYPES),
            'account_desc' => 'nullable|string|max:255',
        ]);

        if ($this->mainAccountId) {
            $res = MainAccount::findOrFail($this->mainAccountId)->editInfo($this->account_name, $this->account_type,$this->account_desc);
            if ($res) {
                $this->alert('success', 'Account updated!');
            } else {
                $this->alert('failed', 'Server error');
            }
        } else {
            $res = MainAccount::newMainAccount($this->account_name, $this->account_type,$this->account_desc);
            if ($res) {
                $this->alert('success', 'Account added!');
            } else {
                $this->alert('failed', 'Server error');
            }
        }

        $this->closeEditModal();
    }



    public function render()
    {
        $ACC_TYPES = MainAccount::TYPES;
        $mainAccounts = MainAccount::search($this->searchText)->paginate(30);
        return view('livewire.Accounting.main-account-index', [
            'mainAccounts' => $mainAccounts,
            'ACC_TYPES' => $ACC_TYPES
        ])->layout('layouts.accounting', ['page_title' => $this->page_title , 'main_accounts' => 'active'  ]);
    }
}
