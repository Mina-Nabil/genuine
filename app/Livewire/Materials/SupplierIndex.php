<?php

namespace App\Livewire\Materials;

use App\Models\Materials\Supplier;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierIndex extends Component
{
    use AlertFrontEnd, WithPagination;
    public $page_title = '• Suppliers';

    public $search;
    public $newSupplierSection = false;

    public $supplierName;
    public $supplierPhone1;
    public $supplierPhone2;
    // public $supplierEmail;
    // public $supplierAddress;
    public $supplierContactName;
    public $supplierContactPhone;

    public function addSupplier()
    {
        $this->validate([
            'supplierName' => 'required|string|max:255',
            'supplierPhone1' => 'required|string|max:255',
            'supplierPhone2' => 'nullable|string|max:255',
            // 'supplierEmail' => 'nullable|email',
            // 'supplierAddress' => 'nullable|string|max:255',
            'supplierContactName' => 'nullable|string|max:255',
            'supplierContactPhone' => 'nullable|string|max:255',
        ]);

        $res = Supplier::newSupplier($this->supplierName, $this->supplierPhone1, $this->supplierPhone2, null, null, $this->supplierContactName, $this->supplierContactPhone);

        if ($res) {
            return redirect(route('supplier.show', $res->id));
        } else {
            $this->alertFailed();
        }
    }

    public function openNewSupplierSection(){
        $this->newSupplierSection = true;
    }

    public function closeNewSupplierSection()
    {
        $this->reset(['newSupplierSection', 'supplierName', 'supplierPhone1', 'supplierPhone2', 'supplierContactName', 'supplierContactPhone']);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $suppliers = Supplier::search($this->search)->paginate(50);
        return view('livewire.materials.supplier-index', [
            'suppliers' => $suppliers,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'suppliers' => 'active']);
    }
}
