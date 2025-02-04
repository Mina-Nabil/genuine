<?php

namespace App\Livewire\Accounting;

use App\Models\Accounting\Account;
use App\Models\Accounting\EntryTitle;
use App\Models\Accounting\JournalEntry;
use App\Traits\AlertFrontEnd;
use Http\Client\Common\Plugin\Journal;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use PgSql\Lob;

class CreateJournalEntry extends Component
{
    use WithFileUploads, AlertFrontEnd, AuthorizesRequests;

    public $selectedTitle;
    public $title;
    public $notes;
    public $cash_entry_type;
    public $receiver_name;

    public $credit_accounts_list;
    public $debit_accounts_list;

    public $credit_accounts = [];
    public $debit_accounts = [];

    public $entry_titles;

    public function addAnotherDebitAccount()
    {
        $this->debit_accounts[] = [
            'account_id' => null,
            'nature' => Account::NATURE_DEBIT,
            'amount' => null,
            'currency' => JournalEntry::CURRENCY_EGP,
            'currency_amount' => null,
            'currency_rate' => null,
            'doc_url' => null,
        ];
    }

    public function removeDebitAccount($index)
    {
        if (count($this->debit_accounts) > 1) {
            unset($this->debit_accounts[$index]);
            $this->debit_accounts = array_values($this->debit_accounts); // Reindex array
        }
    }

    public function addAnotherCreditAccount()
    {
        $this->credit_accounts[] = [
            'account_id' => null,
            'nature' => Account::NATURE_CREDIT,
            'amount' => null,
            'currency' => JournalEntry::CURRENCY_EGP,
            'currency_amount' => null,
            'currency_rate' => null,
            'doc_url' => null,
        ];
    }

    public function removeCreditAccount($index)
    {
        if (count($this->credit_accounts) > 1) {
            unset($this->credit_accounts[$index]);
            $this->credit_accounts = array_values($this->credit_accounts); // Reindex array
        }
    }

    public function updatedTitle()
    {
        $title = $this->title;
        $this->entry_titles = EntryTitle::where('name', 'like', "%$title%")
            ->limit(5)
            ->get();
    }

    protected function reformatDebitAccounts()
    {
        $formatted = [];

        foreach ($this->debit_accounts as $account) {
            // Ensure the account_id is set before adding to the formatted array
            if (!is_null($account['account_id'])) {
                $formatted[$account['account_id']] = [
                    'nature' => 'debit', // Always "debit" for debit accounts
                    'amount' => $account['amount'],
                    'currency' => $account['currency'] ?? JournalEntry::CURRENCY_EGP,
                    'currency_amount' => $account['currency_amount'] ?? 0,
                    'currency_rate' => $account['currency_rate'] ?? 1,
                    'doc_url' => $account['doc_url'],
                ];
            }
        }

        return $formatted;
    }

    protected function reformatCreditAccounts()
    {
        $formatted = [];

        foreach ($this->credit_accounts as $account) {
            // Ensure the account_id is set before adding to the formatted array
            if (!is_null($account['account_id'])) {
                $formatted[$account['account_id']] = [
                    'nature' => 'credit', // Always "credit" for credit accounts
                    'amount' => $account['amount'],
                    'currency' => $account['currency'] ?? JournalEntry::CURRENCY_EGP,
                    'currency_amount' => $account['currency_amount'] ?? 0,
                    'currency_rate' => $account['currency_rate'] ?? 1,
                    'doc_url' => $account['doc_url'],
                ];
            }
        }

        return $formatted;
    }

    public function save()
    {
        if ($this->cash_entry_type) {
            $this->validate([
                'receiver_name' => 'required|string|max:255',
                'cash_entry_type' => 'required|in:' . implode(',', JournalEntry::CASH_ENTRY_TYPES),
            ]);
        } else {
            $this->receiver_name = null;
        }

        $this->validate(
            [
                'debit_accounts.*.account_id' => 'required|exists:accounts,id', // Ensure account_id exists
                'debit_accounts.*.amount' => 'required|numeric|min:0', // Ensure amount is a positive number
                'debit_accounts.*.currency' => 'required|in:' . implode(',', JournalEntry::CURRENCIES), // Currency must be a string of max length 3
                'debit_accounts.*.currency_amount' => 'nullable|numeric|min:0', // Optional, but must be a positive number if present
                'debit_accounts.*.currency_rate' => 'nullable|numeric|min:0', // Optional, positive number
                'debit_accounts.*.doc_url' => 'nullable|file|mimes:xlsx,csv,jpg,jpeg,png,pdf|max:2048', // Optional file
                'credit_accounts.*.account_id' => 'required|exists:accounts,id', // Ensure account_id exists
                'credit_accounts.*.amount' => 'required|numeric|min:0', // Ensure amount is a positive number
                'credit_accounts.*.currency' => 'required|in:' . implode(',', JournalEntry::CURRENCIES), // Currency must be a string of max length 3
                'credit_accounts.*.currency_amount' => 'nullable|numeric|min:0', // Optional, but must be a positive number if present
                'credit_accounts.*.currency_rate' => 'nullable|numeric|min:0', // Optional, positive number
                'credit_accounts.*.doc_url' => 'nullable|file|mimes:xlsx,csv,jpg,jpeg,png,pdf|max:2048', // Optional file
                'notes' => 'nullable|string', // Optional field; if provided, must be a string
            ]
        );

        $this->authorize('createEntry', $this->selectedTitle);

        $formattedDebitAccounts = $this->reformatDebitAccounts();
        $formattedCreditAccounts = $this->reformatCreditAccounts();

        // Upload documents and update URLs for debit accounts
        foreach ($this->debit_accounts as $account) {
            if (isset($account['doc_url']) && $account['doc_url']) {
                // Store the file and get the URL
                $doc = $account['doc_url'];
                $url = $doc->store(JournalEntry::FILES_DIRECTORY, 's3');
                $formattedDebitAccounts[$account['account_id']]['doc_url'] = $url; // Update the URL in the formatted array
            }
        }

        // Upload documents and update URLs for credit accounts
        foreach ($this->credit_accounts as $account) {
            if (isset($account['doc_url']) && $account['doc_url']) {
                // Store the file and get the URL
                $doc = $account['doc_url'];
                $url = $doc->store(JournalEntry::FILES_DIRECTORY, 's3');
                $formattedCreditAccounts[$account['account_id']]['doc_url'] = $url; // Update the URL in the formatted array
            }
        }

        $accounts = $formattedDebitAccounts + $formattedCreditAccounts;

        $res = JournalEntry::newJournalEntry($this->selectedTitle->id, $this->cash_entry_type, $this->receiver_name, comment: $this->notes, accounts: $accounts);
        if (is_string($res)) {
            $this->alert('failed', $res);
        } else if ($res) {
            redirect(url('/accounts/entries'));
        } else {
            $this->alert('failed', 'server error');
        }
    }

    public function updatedCashEntryType()
    {
        if ($this->cash_entry_type === '') {
            $this->reset('receiver_name');
        }
    }

    public function mount()
    {
        $this->entry_titles = EntryTitle::withCount('accounts')
            ->orderBy('accounts_count', 'desc')
            ->limit(5)
            ->get();
    }

    public function selectTitle($id)
    {
        $this->selectedTitle = EntryTitle::find($id);
        $this->entry_titles = null;
        if ($id == 1) {
            $all_accounts = Account::all();
            $this->credit_accounts_list = $all_accounts;
            $this->debit_accounts_list = $all_accounts;
        } else {
            $this->credit_accounts_list = $this->selectedTitle
                ->accounts()
                ->wherePivot('nature', Account::NATURE_CREDIT)
                ->get();
            $this->debit_accounts_list = $this->selectedTitle
                ->accounts()
                ->wherePivot('nature', Account::NATURE_DEBIT)
                ->get();
        }

        $this->addAnotherCreditAccount();
        $this->addAnotherDebitAccount();
    }

    public function render()
    {
        $accounts = Account::whereIn('id', [2])->get();
        $CURRENCIES = JournalEntry::CURRENCIES;
        $CASH_ENTRY_TYPES = JournalEntry::CASH_ENTRY_TYPES;

        return view('livewire.Accounting.create-journal-entry', [
            'accounts' => $accounts,
            'CURRENCIES' => $CURRENCIES,
            'CASH_ENTRY_TYPES' => $CASH_ENTRY_TYPES,
        ])->layout('layouts.accounting', ['page_title' => 'Journal Entry • New', 'entries' => 'active']);
    }
}
