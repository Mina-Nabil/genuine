<?php

namespace App\Livewire\Materials;

use App\Models\Materials\SupplierInvoice;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceIndex extends Component
{
    use WithPagination;

    public $page_title = '• Invoices';


    public $fetched_invoices_IDs;   
    public $search;
    public $selectAll = false;
    public $selectedInvoices = [];
    public $selectedAllInvoices = false;


    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedInvoices = $this->fetched_invoices_IDs;
        } else {
            $this->selectedInvoices = [];
        }
    }

    public function selectAllInvoices()
    {
        $this->selectedAllInvoices = true;
        $this->selectedInvoices = SupplierInvoice::pluck('id')->toArray();
    }

    public function undoSelectAllInvoices()
    {
        $this->selectedAllInvoices = false;
        $this->selectedInvoices = $this->fetched_invoices_IDs;
    }
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $invoices = SupplierInvoice::search($this->search)->paginate(50);
        $this->fetched_invoices_IDs = $invoices->pluck('id')->toArray();

        return view('livewire.materials.invoice-index',[
            'invoices' => $invoices
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'invoices' => 'active']);
    }
}
