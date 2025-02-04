<?php

namespace App\Livewire\Accounting;

use Livewire\Component;
use App\Models\Accounting\EntryTitle;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Traits\AlertFrontEnd;
use App\Models\Accounting\Account;
use App\Models\Users\User;

class EntryTitleIndex extends Component
{
    use AlertFrontEnd, WithPagination, AuthorizesRequests;
    public $page_title = '• Entry Titles';

    public $isAddNewModalOpen;
    public $TitleId;

    public $name;
    public $desc;
    public $accounts = [];

    public $searchText;

    public function addAnotherAccount()
    {
        $this->accounts[] = [
            'account_id' => null,
            'nature' => Account::NATURE_DEBIT,
            'limit' => null,
        ];
    }

    public function removeAccount($index)
    {
        if (count($this->accounts) > 1) {
            unset($this->accounts[$index]);
            $this->accounts = array_values($this->accounts); // Reindex array
        }
    }

    protected function reformatAccounts()
    {
        $formatted = [];

        foreach ($this->accounts as $account) {
            // Ensure the account_id is set before adding to the formatted array
            if (!is_null($account['account_id'])) {
                $formatted[$account['account_id']] = [
                    'nature' => $account['nature'],
                    'limit' => $account['limit'],
                ];
            }
        }

        return $formatted;
    }

    protected function informateAccounts($formatted)
    {
        $accounts = [];

        foreach ($formatted as $account_id => $data) {
            $accounts[] = [
                'account_id' => $account_id,
                'nature' => $data['nature'],
                'limit' => $data['limit'],
            ];
        }

        return $accounts;
    }

    public function editTitle()
    {
        $this->authorize('create', Account::class);

        $this->validate([
            'name' => 'required|string|max:255',
            'desc' => 'nullable|string',
            'accounts.*.account_id' => 'required|exists:accounts,id',
            'accounts.*.nature' => 'required|in:' . implode(',', Account::NATURES),
        ]);

        $accounts = $this->reformatAccounts();
        $res = EntryTitle::findOrFail($this->TitleId)->editTitle($this->name, $this->desc, $accounts);
        if ($res) {
            $this->closeModal();
            $this->alert('success', 'Title updated!');
        } else {
            $this->alert('failed', 'server error');
        }
    }

    public function openNewTypeModal()
    {
        $this->addAnotherAccount();
        $this->isAddNewModalOpen = true;
    }

    public function openEditModel($id)
    {
        $e = EntryTitle::findOrFail($id);
        $this->TitleId = $e->id;
        $this->name = $e->name;
        $this->desc = $e->desc;
        foreach ($e->accounts as $account) {
            $this->accounts[] = [
                'account_id' => $account->id,
                'nature' => $account->pivot->nature,
                'limit' => $account->pivot->limit,
            ];
        }
        // dd($this->accounts);
    }

    public function addTitle()
    {
        $this->authorize('create', Account::class);

        $this->validate([
            'name' => 'required|string|max:255',
            'desc' => 'nullable|string',
            'accounts.*.account_id' => 'required|exists:accounts,id',
            'accounts.*.nature' => 'required|in:' . implode(',', Account::NATURES),
        ]);

        $accounts = $this->reformatAccounts();
        $res = EntryTitle::newEntry($this->name, $this->desc, $accounts);
        if ($res) {
            $this->closeModal();
            $this->alert('success', 'Title added!');
        } else {
            $this->alert('failed', 'server error');
        }
    }

    public function delete($id)
    {
        $res = EntryTitle::findOrFail($id)->deleteTitle();
        if ($res) {
            $this->alert('success', 'Title deleted!');
        } else {
            $this->alert('failed', 'server error');
        }
    }

    public function closeModal()
    {
        $this->accounts = [];
        $this->reset(['TitleId', 'name', 'desc', 'isAddNewModalOpen']);
    }

    /////users section
    public $isAllowedModalOpen;
    // public $selectedEntryId;
    // public $loadedUsers = [];

    // public function openEditUsersModel($titleId){
    //     $this->isAllowedModalOpen = true;
    //     $title = EntryTitle::findOrFail($titleId);
    //     $this->selectedEntryId = $title->id;
    //     $this->loadedUsers = $title->allowed_users()->get()->pluck('id')->toArray();
    // }
    // public function editTitleUsers(){
    //     $title = EntryTitle::findOrFail($this->selectedEntryId);
    //     $title->setAllowedUsers($this->loadedUsers);
    //     $this->closeUsersModal();
    // }
    // public function closeUsersModal(){
    //     $this->reset(['isAllowedModalOpen', 'selectedEntryId', 'loadedUsers']);
    // }

    public function render()
    {
        $accounts_list = Account::all();
        $NATURES = Account::NATURES;
        $titles = EntryTitle::paginate(50);

        return view('livewire.Accounting.entry-title-index', [
            'titles' => $titles,
            'NATURES' => $NATURES,
            'accounts_list' => $accounts_list,

        ])->layout('layouts.accounting', ['page_title' => $this->page_title, 'entry_titles' => 'active']);
    }
}
