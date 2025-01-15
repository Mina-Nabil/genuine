<?php

namespace App\Livewire\Materials;

use App\Models\Materials\Supplier;
use App\Traits\AlertFrontEnd;
use Livewire\Component;

class SupplierShow extends Component
{
    use AlertFrontEnd;
    public $page_title;

    public $supplier;

    public $editInfoSection = false;
    public $supplierName;
    public $supplierPhone1;
    public $supplierPhone2;
    public $supplierEmail;
    public $supplierAddress;
    public $supplierContactName;
    public $supplierContactPhone;

    public function openEditInfoSection()
    {
        $this->supplierName = $this->supplier->name;
        $this->supplierPhone1 = $this->supplier->phone1;
        $this->supplierPhone2 = $this->supplier->phone2;
        $this->supplierEmail = $this->supplier->email;
        $this->supplierAddress = $this->supplier->address;
        $this->supplierContactName = $this->supplier->contact_name;
        $this->supplierContactPhone = $this->supplier->contact_phone;

        $this->editInfoSection = true;
    }

    public function closeEditInfoSection()
    {
        $this->reset(['supplierName', 'supplierPhone1', 'supplierPhone2', 'supplierEmail', 'supplierAddress', 'supplierContactName', 'supplierContactPhone', 'editInfoSection']);
    }

    public function editInfo(){
        $this->validate([
            'supplierName' => 'required|string|max:255',
            'supplierPhone1' => 'required|string|max:255',
            'supplierPhone2' => 'nullable|string|max:255',
            'supplierEmail' => 'nullable|email',
            'supplierAddress' => 'nullable|string|max:255',
            'supplierContactName' => 'nullable|string|max:255',
            'supplierContactPhone' => 'nullable|string|max:255',
        ]);

        $res = $this->supplier->editInfo($this->supplierName, $this->supplierPhone1, $this->supplierPhone2, $this->supplierEmail, $this->supplierAddress, $this->supplierContactName, $this->supplierContactPhone);
    
        if ($res) {
            $this->closeEditInfoSection();
            $this->alertSuccess('Supplier updated!');
        } else {
            $this->alertFailed();
        }
    }

    public function mount($id)
    {
        $this->supplier = Supplier::findOrFail($id);
        $this->page_title = '• ' . $this->supplier->name;
    }

    public function render()
    {
        return view('livewire.materials.supplier-show')->layout('layouts.app', ['page_title' => $this->page_title, 'suppliers' => 'active']);
    }
}
