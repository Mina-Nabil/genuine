<?php

namespace App\Livewire\Accounting;

use App\Models\Accounting\Account;
use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\MainAccount;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AccountIndex extends Component
{
    use AlertFrontEnd, AuthorizesRequests;

    public $page_title = 'Accounts';
    public $isAddNewModalOpen = false;

    public $acc_code;
    public $acc_name;
    public $nature;
    public $mainAccountId;
    public $parent_account_id;
    public $acc_desc;
    public $defaultCurrency = JournalEntry::CURRENCY_EGP;

    private $filteredAccounts;

    // filters properties
    public $searchText;
    public $account_nature;
    public $mainAccID;

    //edit info
    public $accountID;

    //to show child accounts
    public $showChildAccounts = [];

    public function showThisChildAccount($accountId)
    {
        // Check if the ID is already in the array, to avoid duplicates
        if (!in_array($accountId, $this->showChildAccounts)) {
            // Add the account ID to the array
            $this->showChildAccounts[] = $accountId;
        }
    }

    public function hideThisChildAccount($accountId)
    {
        $this->showChildAccounts = array_filter($this->showChildAccounts, function ($id) use ($accountId) {
            return $id !== $accountId;
        });
    }

    public function updatedMainAccountId()
    {
        if ($this->mainAccountId) {
            $this->filteredAccounts = MainAccount::find($this->mainAccountId)->accounts()->get();
        }
    }

    // Method to open the modal
    public function openAddNewModal()
    {
        $this->reset(['acc_code', 'acc_name', 'nature', 'mainAccountId', 'parent_account_id', 'acc_desc']);
        $this->isAddNewModalOpen = true;
    }

    // Method to open edit modal
    public function openEditModal($id)
    {
        $a = Account::findOrFail($id);
        $this->acc_code = $a->code;
        $this->acc_name = $a->name;
        $this->nature = $a->nature;
        $this->mainAccountId = $a->main_account_id;
        $this->parent_account_id = $a->parent_account_id;
        $this->acc_desc = $a->desc;
        $this->defaultCurrency = $a->default_currency;
        $this->filteredAccounts = MainAccount::find($this->mainAccountId)->accounts()->get();
        $this->accountID = $id;
    }

    // Method to close edit modal
    public function closeEditModal()
    {
        $this->filteredAccounts = null;
        $this->reset(['acc_code', 'acc_name', 'acc_desc', 'nature', 'mainAccountId', 'parent_account_id', 'accountID']);
    }

    // Method to close the modal
    public function closeAddNewModal()
    {
        $this->isAddNewModalOpen = false;
    }

    // Define variables to hold options
    public $natures;

    public function updateNature($text)
    {
        $this->account_nature = $text;
    }

    public function updateMainAccountID($text)
    {
        $this->mainAccID = $text;
    }

    public function mount()
    {
        $this->authorize('view', Auth::user(), Account::class);
        $this->natures = Account::NATURES;
    }

    public function saveEdit()
    {
        // $this->filteredAccounts = MainAccount::find($this->mainAccountId)->accounts()->get();
        $this->validate([
            'acc_code' => 'required|numeric|gt:0',
            'acc_name' => 'required|string|max:100',
            'nature' => 'required|in:' . implode(',', Account::NATURES),
            'mainAccountId' => 'required|exists:main_accounts,id',
            'parent_account_id' => 'nullable|exists:accounts,id',
            'acc_desc' => 'nullable|string',
            'defaultCurrency' => 'required|in:' . implode(',', JournalEntry::CURRENCIES)
        ]);
        
        $res = Account::findOrFail($this->accountID)->editInfo($this->acc_code, $this->acc_name, $this->nature, $this->mainAccountId, $this->parent_account_id, $this->acc_desc, default_currency: $this->defaultCurrency);
        if ($res) {
            $this->closeEditModal();
            $this->alert('success', 'Account successfully updated');
        } else {
            $this->alert('failed', 'server error');
        }
    }

    public function save()
    {
        $this->validate([
            // 'acc_code' => 'required|numeric|gt:0',
            'acc_name' => 'required|string|max:100',
            'nature' => 'required|in:' . implode(',', Account::NATURES),
            'mainAccountId' => 'required|exists:main_accounts,id',
            'parent_account_id' => 'nullable|exists:accounts,id',
            'acc_desc' => 'nullable|string',
        ]);

        $res = Account::newAccount(null, $this->acc_name, $this->nature, $this->mainAccountId, $this->parent_account_id, $this->acc_desc, default_currency: $this->defaultCurrency);

        if ($res) {
            $this->closeAddNewModal();
            $this->alert('success', 'Account successfully created');
        } else {
            $this->alert('failed', 'server error');
        }
    }

    public function render()
    {
        $accounts = Account::orderByCode()
            ->when($this->account_nature, function ($q) {
                return $q->byNature($this->account_nature);
            })
            ->when($this->mainAccID, function ($q) {
                return $q->byMainAccount($this->mainAccID);
            })
            ->when($this->searchText, function ($q) {
                return $q->searchBy($this->searchText);
            })->when(!$this->searchText, fn($q) => $q->parentAccounts())
            ->get();
        if ($accounts->count() > 50) $accounts = $accounts->whereNull('parent_account_id');

        $main_accounts = MainAccount::all();
        $CURRENCIES = JournalEntry::CURRENCIES;

        return view('livewire.Accounting.account-index', [
            'accounts' => $accounts,
            'main_accounts' => $main_accounts,
            'CURRENCIES' => $CURRENCIES,
            'filteredAccounts' => $this->filteredAccounts
        ])->layout('layouts.accounting', ['page_title' => $this->page_title, 'accounts' => 'active']);
    }
}
