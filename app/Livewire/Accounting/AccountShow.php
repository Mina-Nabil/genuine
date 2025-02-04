<?php

namespace App\Livewire\Accounting;

use App\Models\Accounting\Account;
use App\Traits\ToggleSectionLivewire;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class AccountShow extends Component
{
    use WithPagination ,ToggleSectionLivewire;
    public $page_title = 'Account';
    public $searchText;

    public $account;
    public $accountId;
    public $fromDate = '2024-01-01';
    public $toDate = '2024-12-01';
    protected $listeners = ['dateRangeSelected'];

    public $is_open_edit = true;

    public function dateRangeSelected($startDate, $endDate)
    {
        $this->fromDate = $startDate;
        $this->toDate = $endDate;

        $this->resetPage();
    }

    public function mount($id)
    {
        $this->accountId = $id;
        $this->account = Account::findOrFail($id);
    }

    public function render()
    {
        $fromDate = Carbon::parse($this->fromDate);
        $toDate = Carbon::parse($this->toDate);
        $entries = collect(Account::getEntries($this->accountId, Carbon::parse($fromDate), Carbon::parse($toDate),$this->searchText));

        return view('livewire.Accounting.account-show', ['entries' => $entries])->layout('layouts.accounting', ['page_title' => $this->page_title, 'accounts' => 'active']);
    }
}
