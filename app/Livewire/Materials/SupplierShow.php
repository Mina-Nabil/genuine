<?php

namespace App\Livewire\Materials;

use App\Models\Materials\RawMaterial;
use App\Models\Materials\Supplier;
use App\Models\Materials\SupplierRawMaterial;
use App\Models\Payments\CustomerPayment;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierShow extends Component
{
    use AlertFrontEnd, WithPagination;
    public $page_title;

    public $supplier;

    public $editInfoSection = false;
    public $supplierName;
    public $supplierPhone1;
    public $supplierPhone2;
    public $supplierContactName;
    public $supplierContactPhone;

    //materials
    public $isOpenAssignMaterialSection = false;
    public $price;
    public $expirationDate;
    public $availableRawMaterials;
    public $selectedRawMaterial;
    public $materialsSearch;

    public $isOpenUpdateRawMaterialSection = false;

    public $deleteRawMaterialId;

    public function openConfirmDeleteMaterial($id)
    {
        $this->deleteRawMaterialId = $id;
    }

    public function closeConfirmDeleteMaterial()
    {
        $this->deleteRawMaterialId = null;
    }

    public function deleteRawMaterial()
    {
        $res = SupplierRawMaterial::where('supplier_id', $this->supplier->id)
            ->where('raw_material_id', $this->deleteRawMaterialId)
            ->firstOrFail()
            ->deleteRawMaterial();
        if ($res) {
            $this->closeConfirmDeleteMaterial();
            $this->alertSuccess('Material removed!');
        } else {
            $this->alertFailed();
        }
    }

    public function openUpdateRawMaterialSection($materal_id)
    {
        $r = SupplierRawMaterial::where('supplier_id', $this->supplier->id)
            ->where('raw_material_id', $materal_id)
            ->firstOrFail();
        $this->price = $r->price;
        $this->expirationDate = $r->expiration_date->todatestring();
        $this->isOpenUpdateRawMaterialSection = $materal_id;
    }

    public function closeUpdateRawMaterialSection()
    {
        $this->reset(['price', 'expirationDate', 'isOpenUpdateRawMaterialSection']);
    }

    public function updateRawMaterial()
    {
        $this->validate(
            [
                'price' => 'required|numeric|min:0',
                'expirationDate' => 'required|date|after:today',
            ],
            [
                'price.required' => 'The price field is required.',
                'price.numeric' => 'The price must be a number.',
                'price.min' => 'The price must be at least 0.',
                'expirationDate.required' => 'The expiration date field is required.',
                'expirationDate.date' => 'The expiration date is not a valid date.',
                'expirationDate.after' => 'The expiration date must be a date after today.',
            ],
        );

        $r = SupplierRawMaterial::where('supplier_id', $this->supplier->id)
            ->where('raw_material_id', $this->isOpenUpdateRawMaterialSection)
            ->firstOrFail();

        $res = $r->editInfo($this->price, $this->expirationDate);

        if ($res) {
            $this->closeUpdateRawMaterialSection();
            $this->alertSuccess('Material updated!');
        } else {
            $this->alertFailed();
        }
    }

    public function openAssignMaterialSection()
    {
        $this->isOpenAssignMaterialSection = true;
    }

    public function closeAssignMaterialSection()
    {
        $this->reset(['isOpenAssignMaterialSection', 'selectedRawMaterial', 'price', 'expirationDate']);
    }

    public function selectRawMaterial($id)
    {
        $this->selectedRawMaterial = RawMaterial::findOrFail($id);
    }

    public function clearSelectedMaterial()
    {
        $this->selectedRawMaterial = null;
    }

    public function assignMaterial()
    {
        $this->validate(
            [
                'selectedRawMaterial.id' => 'required|exists:raw_materials,id',
                'price' => 'required|numeric|min:0',
                'expirationDate' => 'required|date|after:today',
            ],
            [
                'selectedRawMaterial.id.required' => 'The raw material is required.',
                'selectedRawMaterial.id.exists' => 'The selected raw material does not exist.',
                'price.required' => 'The price field is required.',
                'price.numeric' => 'The price must be a number.',
                'price.min' => 'The price must be at least 0.',
                'expirationDate.required' => 'The expiration date field is required.',
                'expirationDate.date' => 'The expiration date is not a valid date.',
                'expirationDate.after' => 'The expiration date must be a date after today.',
            ],
        );

        // Check if the raw material is already assigned to the supplier
        if (
            $this->supplier
                ->rawMaterials()
                ->where('raw_material_id', $this->selectedRawMaterial->id)
                ->exists()
        ) {
            $this->addError('selectedRawMaterial.id', 'This raw material is already assigned to the supplier.');
            return;
        }

        $res = $this->supplier->addRawMaterial($this->selectedRawMaterial->id, $this->price, $this->expirationDate);

        if ($res) {
            $this->closeAssignMaterialSection();
            $this->alertSuccess('Material assigned!');
        } else {
            $this->alertFailed();
        }
    }

    public function openEditInfoSection()
    {
        $this->supplierName = $this->supplier->name;
        $this->supplierPhone1 = $this->supplier->phone1;
        $this->supplierPhone2 = $this->supplier->phone2;
        $this->supplierContactName = $this->supplier->contact_name;
        $this->supplierContactPhone = $this->supplier->contact_phone;

        $this->editInfoSection = true;
    }

    public function closeEditInfoSection()
    {
        $this->reset(['supplierName', 'supplierPhone1', 'supplierPhone2', 'supplierContactName', 'supplierContactPhone', 'editInfoSection']);
    }

    public function editInfo()
    {
        $this->validate([
            'supplierName' => 'required|string|max:255',
            'supplierPhone1' => 'required|string|max:255',
            'supplierPhone2' => 'nullable|string|max:255',
            'supplierContactName' => 'nullable|string|max:255',
            'supplierContactPhone' => 'nullable|string|max:255',
        ]);

        $res = $this->supplier->editInfo($this->supplierName, $this->supplierPhone1, $this->supplierPhone2, null, null, $this->supplierContactName, $this->supplierContactPhone);

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
        $this->page_title = '• Supplier • ' . $this->supplier->name;
    }

    public function render()
    {
        $PAYMENT_METHODS = CustomerPayment::PAYMENT_METHODS;
        $supplierPayments = $this->supplier
            ->payments()
            ->latest()
            ->paginate(5, ['*'], 'paymentsPage');
        $supplierTransactions = $this->supplier
            ->transactions()
            ->latest()
            ->paginate(5, ['*'], 'transactionsPage');
        $supplierMaterials = $this->supplier->rawMaterials()->paginate(10, ['*'], 'materialsPage');

        if ($this->isOpenAssignMaterialSection) {
            $assignedMaterialIds = $this->supplier->rawMaterials()->pluck('raw_material_id')->toArray();
            $this->availableRawMaterials = RawMaterial::whereNotIn('id', $assignedMaterialIds)
            ->search($this->materialsSearch)
            ->take(5)
            ->get();
        }

        return view('livewire.materials.supplier-show', [
            'PAYMENT_METHODS' => $PAYMENT_METHODS,
            'supplierPayments' => $supplierPayments,
            'supplierTransactions' => $supplierTransactions,
            'supplierMaterials' => $supplierMaterials,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'suppliers' => 'active']);
    }
}
