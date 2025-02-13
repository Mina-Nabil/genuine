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

    public $supplierId;
    public $supplierName;
    public $suppliersSearchText;

    public $entry_date;
    public $payment_due;
    public $note;
    public $extraFeeAmount;
    public $extraFeeDesc;

    public function addMaterials()
    {
        foreach ($this->selectedMaterials as $materialId) {
            $material = Supplier::findOrFail($this->supplierId)
                ->avialableRawMaterials()
                ->where('raw_materials.id', $materialId)
                ->firstOrFail();

            $this->fetchedMaterials[] = [
                'id' => $materialId,
                'name' => $material->name,
                'quantity' => 1, // Default quantity
                'price' => $material->pivot->price, // Price from pivot table
                'max_price' => $material->pivot->price + 1, // Price from pivot table
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
        $this->reset(['supplierId', 'supplierName', 'fetchedMaterials']);
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
        $extraFee = $this->extraFeeAmount ?? 0;
        foreach ($this->fetchedMaterials as $m) {
            $subtotal = $subtotal + $m['quantity'] * $m['price'];
            $totalItems = $totalItems + $m['quantity'];
        }

        $this->subtotal = $subtotal;
        $this->totalItems = $totalItems;
        $this->total = $subtotal  + (is_numeric($extraFee) ? $extraFee : 0);
    }

    public function selectSupplier($id)
    {
        $supplier = Supplier::findOrFail($id);
        $this->supplierId = $supplier->id;
        $this->supplierName = $supplier->name;

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
        $this->validate(
            [
                'supplierId' => 'required|exists:suppliers,id',
                'invoiceCode' => 'nullable|string|max:255|unique:supplier_invoices,code',
                'invoiceTitle' => 'nullable|string|max:255',
                'note' => 'nullable|string|max:500',
                'entry_date' => 'required|date',
                'payment_due' => 'nullable|date|after_or_equal:today',
                'fetchedMaterials' => 'required|array|min:1',
                'fetchedMaterials.*.id' => 'required|exists:raw_materials,id',
                'fetchedMaterials.*.quantity' => 'required|integer|min:1',
                'fetchedMaterials.*.price' => 'required|numeric|min:0',
                'extraFeeAmount' => 'nullable|numeric|not_in:0',
                'extraFeeDesc' => 'nullable|string|max:255',
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
                'extraFeeAmount.numeric' => 'The extra fee amount must be a number.',
                'extraFeeAmount.not_in' => 'The extra fee amount must not be zero.',
                'extraFeeDesc.string' => 'The description must be a string.',
                'extraFeeDesc.max' => 'The description may not be greater than 255 characters.',
            ],
        );

        if (
            (is_null($this->extraFeeAmount) && !is_null($this->extraFeeDesc)) ||
            (!is_null($this->extraFeeAmount) && is_null($this->extraFeeDesc))
        ) {
            $this->addError('extraFeeDesc', 'Both the extra fee amount and description must be provided together or left empty.');
            return;
        }

        $res = SupplierInvoice::createInvoice($this->supplierId, $this->entry_date, $this->fetchedMaterials, $this->invoiceCode, null, $this->note, $this->payment_due, $this->extraFeeDesc,$this->extraFeeAmount);

        if ($res) {
            $this->reset();
            $this->alertSuccess('Invoice created!');
        } else {
            $this->alertFailed();
        }
    }

    public function updatedExtraFeeAmount(){
        $this->refreshPayments();
    }

    public function render()
    {
        if ($this->supplierId) {
            $rawMaterials = Supplier::findOrFail($this->supplierId)
                ->avialableRawMaterials()
                ->search($this->materialsSearchText)
                ->take(20)
                ->get();
        } else {
            $rawMaterials = null;
        }

        $suppliers = Supplier::search($this->suppliersSearchText)
            ->limit(10)
            ->get();

        return view('livewire.materials.invoice-create', [
            'rawMaterials' => $rawMaterials,
            'suppliers' => $suppliers,
        ]);
    }
}
