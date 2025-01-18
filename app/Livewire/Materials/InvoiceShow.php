<?php

namespace App\Livewire\Materials;

use App\Models\materials\InvoiceRawMaterial;
use App\Models\Materials\RawMaterial;
use App\Models\materials\SupplierInvoice;
use App\Traits\AlertFrontEnd;
use Livewire\Component;

class InvoiceShow extends Component
{
    use AlertFrontEnd;

    public $invoice;

    // remove raw material
    public $returnedRawMateralId;
    public $addRawmaterialModal = false;
    public $searchRawMaterialText;

    //remocve raw material quantity
    public $returnedRawMaterial;
    public $returnedRawMaterialQty;

    public $selectedRawMaterial;
    public $quantity;
    public $price;

    public function openReturnRawMaterialQtyModal($rawMaterialId)
    {
        $this->returnedRawMaterial = InvoiceRawMaterial::where('supplier_invoice_id', $this->invoice->id)
            ->where('raw_material_id', $rawMaterialId)
            ->firstOrFail();
    }

    public function closeReturnRawMaterialQtyModal()
    {
        $this->returnedRawMaterial = null;
    }

    public function returnRawMaterialQty()
    {
        $this->validate(
            [
                'returnedRawMaterialQty' => 'required|numeric|min:1|max:' . $this->returnedRawMaterial->quantity,
            ],
            [
                'returnedRawMaterialQty.required' => 'Please enter the quantity',
                'returnedRawMaterialQty.numeric' => 'The quantity must be a number',
                'returnedRawMaterialQty.min' => 'The quantity must be at least 1',
                'returnedRawMaterialQty.max' => 'The quantity must be at most ' . $this->returnedRawMaterial->quantity,
            ],
        );

        $res = $this->invoice->returnRawMaterial($this->returnedRawMaterial->id, $this->returnedRawMaterialQty);

        if ($res) {
            $this->alertSuccess('Raw material quantity retuned successfully');
            $this->closeReturnRawMaterialQtyModal();
        } else {
            $this->alertFailed();
        }
    }

    public function openAddRawMaterialModal()
    {
        $this->addRawmaterialModal = true;
    }

    public function closeAddRawMaterialModal()
    {
        $this->addRawmaterialModal = false;
    }

    public function addRawMaterial()
    {
        $this->validate(
            [
                'selectedRawMaterial' => 'required|exists:raw_materials,id',
                'quantity' => 'required|numeric|min:1',
                'price' => 'required|numeric|min:0',
            ],
            [
                'selectedRawMaterial.required' => 'Please select a raw material',
                'selectedRawMaterial.exists' => 'The selected raw material does not exist',
                'quantity.required' => 'Please enter the quantity',
                'quantity.numeric' => 'The quantity must be a number',
                'quantity.min' => 'The quantity must be at least 1',
                'price.required' => 'Please enter the price',
                'price.numeric' => 'The price must be a number',
                'price.min' => 'The price must be at least 0',
            ],
        );

        $res = $this->invoice->addRawMaterial($this->selectedRawMaterial, $this->quantity, $this->price);

        if ($res) {
            $this->alertSuccess('Raw material added successfully');
            $this->closeAddRawMaterialModal();
        } else {
            $this->alertFailed();
        }
    }

    public function openReturnRawMaterialModal($id)
    {
        $this->returnedRawMateralId = $id;
    }

    public function closeReturnRawMaterialModal()
    {
        $this->returnedRawMateralId = null;
    }

    public function returnRawMaterial()
    {
        $res = $this->invoice->returnAllQuantityOfRawMaterial($this->returnedRawMateralId);

        if ($res) {
            $this->alertSuccess('Raw material retuned successfully');
            $this->closeReturnRawMaterialModal();
        } else {
            $this->alertFailed();
        }
    }

    public function mount($id)
    {
        $this->invoice = SupplierInvoice::findOrFail($id);
    }

    public function render()
    {
        $rawMaterials = RawMaterial::search($this->searchRawMaterialText)
            ->take(10)
            ->get();
        return view('livewire.materials.invoice-show', [
            'rawMaterials' => $rawMaterials,
        ]);
    }
}
