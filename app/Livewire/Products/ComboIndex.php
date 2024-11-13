<?php

namespace App\Livewire\Products;

use App\Models\Products\Combo;
use App\Models\Products\Product;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\WithPagination;

class ComboIndex extends Component
{
    use WithPagination, AlertFrontEnd;
    public $page_title = '• Combos';

    public $fetched_combos_IDs;
    public $searchTerm;
    public $selectAll = false; //to select all in the page
    public $selectedCombos = [];
    public $newComboSection = false;
    public $selectedAllCombos = false; //to select all products
    public $sortColomn;
    public $sortDirection = 'asc';

    public $comboName;
    public $productQuantities = []; // To hold products and their quantities
    public $products; // To hold all products, load this in mount()

    public function mount()
    {
        $this->products = Product::all(); // Load all products
        $this->addProduct(); // Initialize with one product field
    }

    public function addProduct()
    {
        $this->productQuantities[] = ['product_id' => null, 'quantity' => 1]; // Add a new product field with default quantity of 1
    }

    public function removeProduct($index)
    {
        if (count($this->productQuantities) > 1) {
            unset($this->productQuantities[$index]); // Remove the selected product field
            $this->productQuantities = array_values($this->productQuantities); // Reset array keys
        }
    }

    public function addNewCombo()
    {
        $this->validate(
            [
                'comboName' => 'required|string|max:255',
                'productQuantities.*.product_id' => 'required|exists:products,id',
                'productQuantities.*.quantity' => 'required|integer|min:0', // Ensure each quantity is a positive integer
                'productQuantities.*.price' => ['required', 'numeric', 'regex:/^\d{1,5}(\.\d{1,2})?$/', 'min:0'],
            ],
            attributes: [
                'comboName' => 'name',
                'productQuantities.*.product_id' => 'product',
                'productQuantities.*.quantity' => 'product quantity', // Ensure each quantity is a positive integer
                'productQuantities.*.price' => 'product price', // Ensure each quantity is a positive integer
            ],
        );

        // Check for duplicate product IDs
        $productIds = array_column($this->productQuantities, 'product_id');

        if (count($productIds) !== count(array_unique($productIds))) {
            $this->addError('productQuantities', 'Duplicate products are not allowed.');
            return;
        }

        // Create the combo with associated products
        $res = Combo::createCombo($this->comboName, $this->productQuantities);

        if ($res) {
            $this->closeNewComboSec();
            $this->alertSuccess('Combo created!');
        } else {
            $this->alertFailed();
        }
    }

    public function openNewComboSec()
    {
        $this->newComboSection = true;
    }

    public function closeNewComboSec()
    {
        $this->productQuantities = [];
        $this->addProduct();
        $this->reset(['comboName', 'newComboSection']);
    }

    public function sortByColomn($colomn)
    {
        $this->sortColomn = $colomn;
        if ($this->sortDirection) {
            if ($this->sortDirection === 'asc') {
                $this->sortDirection = 'desc';
            } else {
                $this->sortDirection = 'asc';
            }
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedCombos = $this->fetched_combos_IDs;
        } else {
            $this->selectedCombos = [];
        }
    }

    public function selectAllCombos()
    {
        $this->selectedAllCombos = true;
        $this->selectedCombos = Combo::pluck('id')->toArray();
    }

    public function undoSelectAllCombos()
    {
        $this->selectedAllCombos = false;
        $this->selectedCombos = $this->fetched_combos_IDs;
    }

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function render()
    {
        $combos = Combo::search($this->searchTerm)
            ->sortBy($this->sortColomn, $this->sortDirection)
            ->paginate(10);
        $this->fetched_combos_IDs = $combos->pluck('id')->toArray();
        return view('livewire.products.combo-index', [
            'combos' => $combos,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'combos' => 'active']);
    }
}
