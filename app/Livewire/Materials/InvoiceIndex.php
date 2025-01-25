<?php

namespace App\Livewire\Materials;

use App\Models\Materials\Supplier;
use App\Models\Materials\SupplierInvoice;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceIndex extends Component
{
    use WithPagination;

    public $page_title = '• Active Invoices';

    public $search;

    //Filters
    public $isOpenSupplierSection = false;
    public $supplierSearchText;
    public $selectedSupplier;

    public $dueDateFrom;
    public $dueDateTo;
    public $Edited_dueDate_sec;
    public $editedDueDateFrom;
    public $editedDueDateTo;

    public $entryDateFrom;
    public $entryDateTo;
    public $Edited_entryDate_sec;
    public $editedEntryDateFrom;
    public $editedEntryDateTo;

    public function clearEntryDate()
    {
        $this->reset(['entryDateFrom', 'entryDateTo']);
    }

    public function openFilteryEntryDate()
    {
        $this->editedEntryDateFrom = $this->entryDateFrom;
        $this->editedEntryDateTo = $this->entryDateTo;
        $this->Edited_entryDate_sec = true;
    }

    public function closeFilteryEntryDate(){
        $this->Edited_entryDate_sec = false;
        $this->reset(['editedEntryDateFrom', 'editedEntryDateTo']);
    }

    public function setFilteryEntryDate()
    {
        $this->validate([
            'editedEntryDateFrom' => 'nullable|date|required_without:editedEntryDateTo',
            'editedEntryDateTo' => 'nullable|date|required_without:editedEntryDateFrom|after_or_equal:editedEntryDateFrom',
        ], [
            'editedEntryDateFrom.required_without' => 'The from date field is required.',
            'editedEntryDateTo.required_without' => 'The to date field is required.',
            'editedEntryDateTo.after_or_equal' => 'The to date must be a date after or equal to the from date.',
        ]);
        $this->entryDateFrom = $this->editedEntryDateFrom;
        $this->entryDateTo = $this->editedEntryDateTo;
        $this->Edited_entryDate_sec = false;
        $this->reset(['editedEntryDateFrom', 'editedEntryDateTo']);
    }

    public function clearDueDate()
    {
        $this->reset(['dueDateFrom', 'dueDateTo']);
    }

    public function openFilteryDueDate()
    {
        $this->editedDueDateFrom = $this->dueDateFrom;
        $this->editedDueDateTo = $this->dueDateTo;
        $this->Edited_dueDate_sec = true;
    }

    public function closeFilteryDueDate()
    {
        $this->Edited_dueDate_sec = false;
        $this->reset(['editedDueDateFrom', 'editedDueDateTo']);
    }

    public function setFilteryDueDate()
    {
        $this->validate([
            'editedDueDateFrom' => 'nullable|date|required_without:editedDueDateTo',
            'editedDueDateTo' => 'nullable|date|required_without:editedDueDateFrom|after_or_equal:editedDueDateFrom',
        ], [
            'editedDueDateFrom.required_without' => 'The from date field is required.',
            'editedDueDateTo.required_without' => 'The to date field is required.',
            'editedDueDateTo.after_or_equal' => 'The to date must be a date after or equal to the from date.',
        ]);
        $this->dueDateFrom = $this->editedDueDateFrom;
        $this->dueDateTo = $this->editedDueDateTo;
        $this->closeFilteryDueDate();
    }

    public function openSupplierSection()
    {
        $this->isOpenSupplierSection = true;
    }

    public function closeSupplierSection()
    {
        $this->isOpenSupplierSection = false;
        $this->supplierSearchText = '';
    }

    public function selectSupplier($supplier_id)
    {
        $this->selectedSupplier = Supplier::findOrFail($supplier_id);
        $this->closeSupplierSection();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function clearProperty(string $propertyName)
    {
        // Check if the property exists before attempting to clear it
        if (property_exists($this, $propertyName)) {
            $this->$propertyName = null;
        }
    }

    public function render()
    {
        $invoices = SupplierInvoice::search($this->search, $this->selectedSupplier?->id, $this->dueDateFrom, $this->dueDateTo, false , $this->entryDateFrom, $this->entryDateTo)->paginate(50);
        $suppliers = Supplier::search($this->supplierSearchText)
            ->limit(10)
            ->get();

        return view('livewire.materials.invoice-index', [
            'invoices' => $invoices,
            'suppliers' => $suppliers,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'invoices' => 'active']);
    }
}
