<?php

namespace App\Livewire\Materials;

use App\Models\Materials\RawMaterial;
use Livewire\Component;

class InvoiceCreate extends Component
{
    public $invoiceTitle;

    public $dummyMaterialsSearch;
    public $isOpenSelectMaterialSec = false;
    public $materialsSearchText;

    public $selectedMaterials = [];
    public $fetchedMaterials = [];

    public $subtotal;
    public $totalItems;
    public $total;


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
        if (array_key_exists($index, $this->fetchedMaterials))
            $this->fetchedMaterials[$index]['total'] = $this->fetchedMaterials[$index]['quantity'] * $this->fetchedMaterials[$index]['price'];

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

    public function render()
    {
        $rawMaterials = RawMaterial::search($this->materialsSearchText)
            ->limit(20)
            ->get();
        return view('livewire.materials.invoice-create',[
            'rawMaterials' => $rawMaterials
        ]);
    }
}
