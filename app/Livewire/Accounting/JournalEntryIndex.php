<?php

namespace App\Livewire\Accounting;

use App\Models\Accounting\Account;
use App\Models\Accounting\JournalEntry;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;

class JournalEntryIndex extends Component
{
    use AlertFrontEnd, WithPagination, AuthorizesRequests, WithFileUploads;
    protected $paginationTheme = 'bootstrap';

    public $page_title = '• Journal Entry';

    public $searchText;
    public $isNewJournalEntryModalOpen = false;
    public $isOpenFilterAccountModal = false;
    public $searchAccountText;
    public $fetched_accounts;
    public $selectedAccount;

    public $AccountId;

    protected $queryString = ['AccountId'];

    // for bulk actions
    public $Sentries;
    public $selectedEntries = [];
    public $selectAll = false;

    // for entry show info
    public $entryInfo;
    public $entryId;

    //for download daily transactions
    public $isOpenDailyTrans = false;
    public $tranactionsDay;

    public function showEntry($id)
    {
        $this->entryId  = $id;

        $this->entryInfo = JournalEntry::findOrFail($id);

        $this->authorize('view', $this->entryInfo);
    }

    public $accountDoc;
    public $uploadFileEntryId;
    public $uploadFileAccountId;

    public function updatedAccountDoc()
    {
        $this->validate([
            'accountDoc' => 'required|mimes:pdf,jpg,jpeg,png,xlsx,xls,doc,docx,rar,zip|max:10120', // 10MB Max
        ]);

        $file_url = $this->accountDoc->store(JournalEntry::FILES_DIRECTORY, 's3');

        $res = JournalEntry::findOrFail($this->uploadFileEntryId)->uploadDoc($this->uploadFileAccountId, $file_url);

        if ($res) {
            $this->mount();
            $this->alert('success', 'file uploaded!');
        } else {
            $this->alert('failed', 'server error');
        }
    }

    public function uploadAccountDoc($entryId, $accountId)
    {
        $this->uploadFileEntryId = $entryId;
        $this->uploadFileAccountId = $accountId;
    }

    public function downloadAccountDoc($entry_id, $account_id)
    {
        return JournalEntry::findOrFail($entry_id)->downloadDoc($account_id);
    }

    //to show child accounts
    public $showChildAccounts = [];

    public function showThisChildAccount($entryId)
    {
        // Check if the ID is already in the array, to avoid duplicates
        if (!in_array($entryId, $this->showChildAccounts)) {
            // Add the account ID to the array
            $this->showChildAccounts[] = $entryId;
        }
    }

    public function hideThisChildAccount($entryId)
    {
        $this->showChildAccounts = array_filter($this->showChildAccounts, function ($id) use ($entryId) {
            return $id !== $entryId;
        });
    }

    public function downloadDailyTransaction()
    {
        $day = Carbon::parse($this->tranactionsDay);
        $res = JournalEntry::downloadDailyTransaction($day);
        if ($res) {

            $this->alert('success', 'transactions downloaded!');
            return $res;
        } else {
            $this->alert('failed', 'server error');
        }
    }

    public function showDownloadDailyTransactionsForm()
    {
        $this->isOpenDailyTrans = true;
    }

    public function hideDownloadDailyTransactionsForm()
    {
        $this->isOpenDailyTrans = false;
    }

    public function downloadCashReceipt($id)
    {
        $res = JournalEntry::findOrFail($id)->downloadCashReceipt();
        if ($res) {
            $this->alert('success', 'Reciept downloaded!');
            return $res;
        } else {
            $this->alert('failed', 'server error');
        }
    }

    public function closeShowInfo()
    {
        $this->reset(['entryId', 'entryInfo']);
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedEntries = collect($this->Sentries['data'])->pluck('id')->toArray();
        } else {
            $this->selectedEntries = [];
        }
    }

    public function updatedSearchAccountText()
    {
        $this->fetched_accounts = Account::searchBy($this->searchAccountText)
            ->limit(10)
            ->get();
    }

    public function reviewSelectedEntries()
    {
        $this->authorize('review', JournalEntry::class);
        foreach ($this->selectedEntries as $id) {
            JournalEntry::findOrFail($id)->reviewEntry();
        }

        $this->selectedEntries = [];
        $this->mount();
        $this->alert('success', 'Entries reviewed');
    }

    public function reviewEntry($id)
    {
        $this->authorize('review', JournalEntry::class);

        $res = JournalEntry::findOrFail($id)->reviewEntry();
        if ($res) {
            $this->alert('success', 'Entry Successfuly Reviewed!');
        } else {
            $this->alert('failed', 'server error');
        }
    }

    public function clearAccountFilter()
    {
        $this->selectedAccount = null;
        $this->AccountId = null;
    }

    public function downloadCreditDoc($id)
    {
        $entry = JournalEntry::find($id);
        $this->authorize('update', $entry);
        $fileContents = Storage::disk('s3')->get($entry->credit_doc_url);
        $extension = pathinfo($entry->credit_doc_url, PATHINFO_EXTENSION);
        $headers = [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . '#' . $entry->day_serial . '_credit_doc.' . $extension . '"',
        ];

        return response()->stream(
            function () use ($fileContents) {
                echo $fileContents;
            },
            200,
            $headers,
        );
    }

    public function downloadDebitDoc($id)
    {
        $entry = JournalEntry::find($id);
        $this->authorize('update', $entry);
        $fileContents = Storage::disk('s3')->get($entry->debit_doc_url);
        $extension = pathinfo($entry->debit_doc_url, PATHINFO_EXTENSION);
        $headers = [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . '#' . $entry->day_serial . '_debit_doc.' . $extension . '"',
        ];

        return response()->stream(
            function () use ($fileContents) {
                echo $fileContents;
            },
            200,
            $headers,
        );
    }

    public function openSelectAccountModel()
    {
        $this->isOpenFilterAccountModal = true;
    }

    public function closeSelectAccountModel()
    {
        $this->isOpenFilterAccountModal = false;
    }

    public function openAddNewModal()
    {
        $this->isNewJournalEntryModalOpen = true;
    }

    public function closeAddNewModal()
    {
        $this->isNewJournalEntryModalOpen = false;
    }

    public function revertEntry($id)
    {

        $e = JournalEntry::findOrFail($id);

        $this->authorize('update', $e);

        $res = $e->revertEntry();

        if ($res) {
            $this->alert('success', 'Entry reverted successfuly!');
            $this->mount();
        } else {
            $this->alert('failed', 'server error');
        }
    }

    public function selectAccount($id)
    {
        $this->selectedAccount = Account::findOrFail($id);
        $this->AccountId = $id;
        $this->authorize('view', $this->selectedAccount);
        $this->closeSelectAccountModel();
    }

    public function redirectToAccount($id)
    {
        $this->dispatchBrowserEvent('openNewTab', ['url' => route('accounts.show', $id)]);
    }

    public function mount()
    {
        $this->Sentries = JournalEntry::all();
        $this->authorize('viewAny', JournalEntry::class);
        if ($this->AccountId) {
            $this->selectedAccount = Account::findOrFail($this->AccountId);
        }
    }

    public function render()
    {
        $entries = JournalEntry::when($this->selectedAccount, function ($q) {
            return $q->byAccount($this->selectedAccount->id);
        })->latest()->paginate(20);

        $this->Sentries = $entries->toArray();

        $creditAccounts = Account::byNature(Account::NATURE_CREDIT)->get();
        $debitAccounts = Account::byNature(Account::NATURE_DEBIT)->get();

        return view('livewire.Accounting.journal-entry-index', [
            'entries' => $entries,
            'creditAccounts' => $creditAccounts,
            'debitAccounts' => $debitAccounts,
        ])->layout('layouts.accounting', ['page_title' => $this->page_title, 'entries' => 'active']);
    }
}
