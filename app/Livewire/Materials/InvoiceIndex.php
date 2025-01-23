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

    public $page_title = '• Invoices';

    public $search;

    //Filters
    public $isOpenSupplierSection = false;
    public $supplierSearchText;
    public $selectedSupplier;

    public $dueDate = [];
    public $Edited_dueDate;
    public $Edited_dueDate_sec;
    public $selectedDueDates = [];
    public $is_paid = null;

    public function filterisPaid()
    {
        if ($this->is_paid === null) {
            $this->is_paid = true;
        } else {
            $this->is_paid = !$this->is_paid;
        }
    }

    public function clearisPaid()
    {
        $this->reset('is_paid');
    }

    public function updatedEditedDueDate($value)
    {
        foreach ($this->selectedDueDates as $date) {
            if ($date->toDateString() === $value) {
                return;
            }
        }
        $this->selectedDueDates[] = Carbon::parse($value);
        $this->Edited_dueDate = null;
    }

    public function removeSelectedDate($index)
    {
        unset($this->selectedDueDates[$index]);
        $this->selectedDueDates = array_values($this->selectedDueDates); // Reset array keys
    }

    public function clearDueDate()
    {
        $this->dueDate = [];
    }

    public function openFilteryDueDate()
    {
        $this->Edited_dueDate_sec = true;

        foreach ($this->dueDate as $date) {
            $this->selectedDueDates[] = $date;
        }
    }

    public function closeFilteryDueDate()
    {
        $this->Edited_dueDate_sec = false;
        $this->Edited_dueDate = null;
        $this->selectedDueDates = [];
    }

    public function setFilteryDueDate()
    {
        $this->dueDate = $this->selectedDueDates;
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
        $invoices = SupplierInvoice::search($this->search, $this->selectedSupplier?->id, $this->dueDate, $this->is_paid)->paginate(50);
        $suppliers = Supplier::search($this->supplierSearchText)
            ->limit(10)
            ->get();

        return view('livewire.materials.invoice-index', [
            'invoices' => $invoices,
            'suppliers' => $suppliers,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'invoices' => 'active']);
    }
}
