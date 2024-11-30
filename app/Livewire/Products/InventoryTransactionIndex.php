<?php

namespace App\Livewire\Products;

use Livewire\Component;
use App\Models\Products\Category;
use App\Models\Products\Inventory;
use App\Models\Products\Product;
use App\Traits\AlertFrontEnd;
use Livewire\WithPagination;

class InventoryTransactionIndex extends Component
{
    use WithPagination, AlertFrontEnd;

    public $page_title = '• Inventories';

    public $fetched_inventories_IDs;
    public $search;
    public $selectAll = false; //to select all in the page
    public $selectedInventories = [];
    public $selectedAllInventories = false; //to select all products

    public $productsChanges = [];
    public $oldproductsChanges = [];
    public $hasChanges = false;

    public $searchTerm;
    public $sortColomn;
    public $sortDirection = 'asc';

    public $newChanges;
    public $transRemark;

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
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedInventories = $this->fetched_inventories_IDs;
        } else {
            $this->selectedInventories = [];
        }
    }

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function selectAllInventories()
    {
        $this->selectedAllInventories = true;
        $this->selectedInventories = Inventory::pluck('id')->toArray();
    }

    public function undoSelectAllInventories()
    {
        $this->selectedAllInventories = false;
        $this->selectedInventories = $this->fetched_inventories_IDs;
    }

    public function updatingProductsChanges()
    {
        $this->hasChanges = true; // Detect changes
    }

    public function updatedProductsChanges()
    {
        $this->hasChanges = true; // Detect changes
    }

    public function getChangedIndexes()
    {
        $changedIndexes = [];
        foreach ($this->productsChanges['data'] as $index => $inventoryData) {
            // Check if the current value differs from the original value
            if (isset($this->oldproductsChanges['data'][$index])) {
                if ($inventoryData['on_hand'] != $this->oldproductsChanges['data'][$index]['on_hand'] || $inventoryData['available'] != $this->oldproductsChanges['data'][$index]['available']) {
                    // Add the index to the changed array
                    $inventory = Inventory::find($inventoryData['id']);
                    if ($inventory && $inventory->inventoryable) {
                        // Assuming 'inventoryable' is the relationship with Product
                        // Add the index, product name, and other necessary data to the changed array
                        $changedIndexes[] = [
                            'inventory_id' => $inventoryData['id'],
                            'product_name' => $inventory->inventoryable->name, // Get the product's name
                            'on_hand' => $inventoryData['on_hand'],
                            'available' => $inventoryData['available'],
                            'from_on_hand' => $this->oldproductsChanges['data'][$index]['on_hand'],
                            'to_on_hand' => $inventoryData['on_hand'],
                            'from_available' => $this->oldproductsChanges['data'][$index]['available'],
                            'to_available' => $inventoryData['available'],
                        ];
                    }
                }
            }
        }

        return $changedIndexes;
    }

    public function openReviewChanges()
    {
        $this->authorize('update', Inventory::all()->first());
        $this->newChanges = $this->getChangedIndexes();
    }

    public function closeReviewChanges()
    {
        $this->newChanges = [];
    }

    public function updateAvailable($index)
    {
        if ($this->productsChanges['data'][$index]['on_hand'] === '') {
            $this->productsChanges['data'][$index]['on_hand'] = 0;
            $this->updateOnHand($index);
            return;
        }

        $oldOnHand = $this->oldproductsChanges['data'][$index]['on_hand']; // Old value of on_hand
        $newOnHand = $this->productsChanges['data'][$index]['on_hand']; // New value of on_hand
        $available = $this->productsChanges['data'][$index]['available'];
        $oldAvailable = $this->oldproductsChanges['data'][$index]['available'];

        // Calculate the difference and apply the same to 'available'

        $difference = $oldOnHand - $oldAvailable;

        // Update the 'available' field by the same difference
        $this->productsChanges['data'][$index]['available'] = $newOnHand - $difference;
    }

    public function updateOnHand($index)
    {
        $oldAvailable = $this->oldproductsChanges['data'][$index]['available'];
        $newAvailable = $this->productsChanges['data'][$index]['available'];
        $oldOnHand = $this->oldproductsChanges['data'][$index]['on_hand'];

        // Calculate the difference and apply the same to 'on_hand'
        $difference = $oldAvailable - $oldOnHand;

        // Update the 'on_hand' field by the same difference
        $this->productsChanges['data'][$index]['on_hand'] = $newAvailable - $difference;
    }

    public function resetChangesTracking()
    {
        $this->hasChanges = false; // Reset change tracking
    }

    public function updatedPage()
    {
        $this->reset(['productsChanges', 'oldproductsChanges']);
        $this->hasChanges = null;
    }

    public function submitTransaction()
    {
        foreach ($this->newChanges as $changedIndex) {
            if (abs((int) $changedIndex['from_on_hand'] - (int) $changedIndex['to_on_hand']) !== abs((int) $changedIndex['from_available'] - (int) $changedIndex['to_available'])) {
                $this->alertFailed();
                return;
            } else {
                $quantity = (int) $changedIndex['to_on_hand'];
                $this->authorize('update', Product::findOrFail($changedIndex['inventory_id'])->inventory);
                Product::findOrFail($changedIndex['inventory_id'])->inventory->updateOnHandWithNewValue($quantity, $this->transRemark);
            }
        }
        $this->resetPage();
        $this->reset(['newChanges', 'transRemark', 'productsChanges', 'oldproductsChanges', 'hasChanges']);
        $this->alertSuccess('Inventory updated');
    }

    public function mount()
    {
        $this->authorize('viewAny', Inventory::class);
    }

    public function render()
    {
        $inventories = Inventory::search($this->searchTerm)
            ->sortBy($this->sortColomn, $this->sortDirection) // or sortByPrice($sortDirection) / sortByWeight($sortDirection)
            ->paginate(20);

        $this->productsChanges = array_replace_recursive($inventories->toArray(), $this->productsChanges);
        $this->oldproductsChanges = array_replace_recursive($inventories->toArray(), $this->oldproductsChanges);

        $categories = Category::all();

        $this->fetched_inventories_IDs = $inventories->pluck('id')->toArray();

        return view('livewire.products.inventory-transaction-index', [
            'inventories' => $inventories,
            'categories' => $categories,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'inventories' => 'active']);
    }
}
