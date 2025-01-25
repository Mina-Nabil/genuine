<?php

namespace App\Livewire\Materials;

use App\Models\Materials\RawMaterial;
use App\Models\Materials\Supplier;
use App\Models\Materials\SupplierInvoice;
use App\Traits\AlertFrontEnd;
use Livewire\Component;

class InvoiceCreate extends Component
{
    use AlertFrontEnd;

    public $invoiceTitle;
    public $invoiceCode;

    public $dummyMaterialsSearch;
    public $isOpenSelectMaterialSec = false;
    public $materialsSearchText;

    public $selectedMaterials = [];
    public $fetchedMaterials = [];

    public $subtotal;
    public $totalItems;
    public $total;

    public $supplierIsNew = false;
    public $supplierId;
    public $supplierName;
    public $isOpenSelectSupplierSec;
    public $suppliersSearchText;

    public $supplierPhone1;
    public $supplierPhone2;
    public $supplierEmail;
    public $supplierAddress;
    public $supplierContactName;
    public $supplierContactPhone;

    public $entry_date;
    public $payment_due;
    public $note;
    public $update_supplier_materials = false;

    public function openSupplierSection()
    {
        $this->isOpenSelectSupplierSec = true;
    }

    public function NewSupplierSection()
    {
        $this->supplierIsNew = true;
    }

    public function closeSupplierSection()
    {
        $this->isOpenSelectSupplierSec = false;
        $this->suppliersSearchText = null;
    }

    public function addMaterials()
    {
        foreach ($this->selectedMaterials as $materialId) {
            $material = RawMaterial::findOrFail($materialId);
            $this->fetchedMaterials[] = [
                'id' => $materialId,
                'name' => $material->name,
                'quantity' => 1, // Default quantity
                'price' => 0, // Default price
            ];
        }

        $this->fetchedMaterials = collect($this->fetchedMaterials)
            ->filter(function ($material) {
                return in_array($material['id'], $this->selectedMaterials);
            })
            ->values()
            ->toArray();

        $this->closeMaterialsSection();
        $this->refreshPayments();
    }

    public function clearSupplier()
    {
        $this->reset(['supplierIsNew', 'supplierId', 'supplierName']);
        $this->refreshPayments();
    }

    public function updateTotal($index)
    {
        $this->validate(
            [
                'fetchedMaterials.*.quantity' => 'required|integer|min:1',
                'fetchedMaterials.*.price' => 'required|numeric|min:0',
            ],
            [
                'fetchedMaterials.*.quantity' => 'Each invoice item must have a valid quantity',
                'fetchedMaterials.*.price' => 'Each invoice item must have a price',
            ],
        );
        if (array_key_exists($index, $this->fetchedMaterials)) {
            $this->fetchedMaterials[$index]['total'] = $this->fetchedMaterials[$index]['quantity'] * $this->fetchedMaterials[$index]['price'];
        }

        $this->refreshPayments();
    }

    public function openMaterialsSection()
    {
        $this->isOpenSelectMaterialSec = true;
        $this->dummyMaterialsSearch = null;
    }

    public function closeMaterialsSection()
    {
        $this->isOpenSelectMaterialSec = false;
        $this->materialsSearchText = null;
    }

    public function refreshPayments()
    {
        $this->validate(
            [
                'fetchedMaterials.*.id' => 'required|exists:raw_materials,id',
                'fetchedMaterials.*.quantity' => 'required|integer|min:1',
                'fetchedMaterials.*.price' => 'required|numeric|min:0',
            ],
            [
                'fetchedMaterials.*.id' => 'Each invoive item is required',
                'fetchedMaterials.*.quantity' => 'Each invoive item must have a valid quantity',
                'fetchedMaterials.*.price' => 'Each invoive item must have a price',
            ],
        );

        $subtotal = 0;
        $totalItems = 0;
        foreach ($this->fetchedMaterials as $m) {
            $subtotal = $subtotal + $m['quantity'] * $m['price'];
            $totalItems = $totalItems + $m['quantity'];
        }

        $this->subtotal = $subtotal;
        $this->totalItems = $totalItems;
        $this->total = $subtotal;
    }

    public function selectSupplier($id)
    {
        $supplier = Supplier::findOrFail($id);
        $this->supplierId = $supplier->id;
        $this->supplierName = $supplier->name;

        $this->closeSupplierSection();
        $this->refreshPayments();
    }

    public function removeMaterial($fetchedProductIndex)
    {
        $prod = $this->fetchedMaterials[$fetchedProductIndex];
        unset($this->fetchedMaterials[$fetchedProductIndex]);

        // Remove the product ID from selectedProducts
        $this->selectedMaterials = array_filter($this->selectedMaterials, function ($id) use ($prod) {
            return $id != $prod['id']; // Retain IDs not matching the product ID
        });

        // Re-index the selectedProducts array to maintain sequential numeric keys
        $this->selectedMaterials = array_values($this->selectedMaterials);
        $this->fetchedMaterials = array_values($this->fetchedMaterials);

        $this->refreshPayments();
    }

    public function addInvoice()
    {
        if ($this->supplierIsNew) {
            $this->validate([
                'supplierName' => 'required|string|max:255',
                'supplierPhone1' => 'required|string|max:255',
                'supplierPhone2' => 'nullable|string|max:255',
                'supplierEmail' => 'nullable|email',
                'supplierAddress' => 'nullable|string|max:255',
                'supplierContactName' => 'nullable|string|max:255',
                'supplierContactPhone' => 'nullable|string|max:255',
            ]);

            $supplier = Supplier::newSupplier($this->supplierName, $this->supplierPhone1, $this->supplierPhone2, $this->supplierEmail, $this->supplierAddress, $this->supplierContactName, $this->supplierContactPhone);
        }

        $this->validate(
            [
                'supplierId' => $this->supplierIsNew ? 'nullable' :  'required|exists:suppliers,id',
                'invoiceCode' => 'nullable|string|max:255|unique:supplier_invoices,code',
                'invoiceTitle' => 'nullable|string|max:255',
                'note' => 'nullable|string|max:500',
                'entry_date' => 'required|date',
                'payment_due' => 'nullable|date|after_or_equal:today',
                'fetchedMaterials' => 'required|array|min:1',
                'fetchedMaterials.*.id' => 'required|exists:raw_materials,id',
                'fetchedMaterials.*.quantity' => 'required|integer|min:1',
                'fetchedMaterials.*.price' => 'required|numeric|min:0',
                'update_supplier_materials' => 'required|boolean',
            ],
            [
                'supplierId.required' => 'The supplier is required.',
                'supplierId.exists' => 'The selected supplier does not exist.',
                'invoiceCode.unique' => 'The invoice code must be unique.',
                'invoiceCode.max' => 'The invoice code may not be greater than 255 characters.',
                'invoiceTitle.max' => 'The title may not be greater than 255 characters.',
                'note.max' => 'The note may not be greater than 500 characters.',
                'entry_date.required' => 'The entry date is required.',
                'entry_date.date' => 'The entry date must be a valid date.',
                'payment_due.date' => 'The payment due date must be a valid date.',
                'payment_due.after_or_equal' => 'The payment due date cannot be in the past.',
                'fetchedMaterials.required' => 'At least one raw material is required.',
                'fetchedMaterials.array' => 'The raw materials must be provided as an array.',
                'fetchedMaterials.min' => 'You must specify at least one raw material.',
                'fetchedMaterials.*.id.required' => 'Each raw material must have an ID.',
                'fetchedMaterials.*.id.exists' => 'The selected raw material ID does not exist.',
                'fetchedMaterials.*.quantity.required' => 'Each raw material must have a quantity.',
                'fetchedMaterials.*.quantity.integer' => 'The quantity of each raw material must be an integer.',
                'fetchedMaterials.*.quantity.min' => 'The quantity of each raw material must be at least 1.',
                'fetchedMaterials.*.price.required' => 'Each raw material must have a price.',
                'fetchedMaterials.*.price.numeric' => 'The price of each raw material must be a numeric value.',
                'fetchedMaterials.*.price.min' => 'The price of each raw material must be at least 0.',
                'update_supplier_materials.required' => 'The update supplier materials field is required.',
                'update_supplier_materials.boolean' => 'The update supplier materials field must be true or false.',
            ],
        );

        $res = SupplierInvoice::createInvoice($this->supplierIsNew ? $supplier->id : $this->supplierId, $this->entry_date, $this->fetchedMaterials, $this->invoiceCode, $this->invoiceTitle, $this->note,  $this->payment_due,  $this->update_supplier_materials);

        if ($res) {
            $this->reset();
            $this->alertSuccess('Invoice created!');
        } else {
            $this->alertFailed();
        }
    }

    public function render()
    {
        $rawMaterials = RawMaterial::search($this->materialsSearchText)
            ->limit(20)
            ->get();

        $suppliers = Supplier::search($this->suppliersSearchText)
            ->limit(10)
            ->get();

        return view('livewire.materials.invoice-create', [
            'rawMaterials' => $rawMaterials,
            'suppliers' => $suppliers,
        ]);
    }
}
