<?php

namespace App\Livewire\Products;

use App\Models\Materials\RawMaterial;
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
    
    public $materialsChanges = [];
    public $oldmaterialsChanges = [];

    public $searchTerm;
    public $sortColomn;
    public $sortDirection = 'asc';

    public $materialSearchTerm;

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
        $this->resetPage('productsPage');
    }

    public function updatingMaterialSearchTerm()
    {
        $this->resetPage('materialsPage');
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

    public function updatingMaterialsChanges()
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

        foreach ($this->productsChanges as $id => $inventoryData) {
            // Check if the current value differs from the original value
            if (isset($this->oldproductsChanges[$id])) {
                if ($inventoryData['on_hand'] != $this->oldproductsChanges[$id]['on_hand'] || $inventoryData['available'] != $this->oldproductsChanges[$id]['available']) {
                    // Add the index to the changed array
                    $inventory = Inventory::find($id);
                    if ($inventory && $inventory->inventoryable) {
                        // Assuming 'inventoryable' is the relationship with Product
                        // Add the index, product name, and other necessary data to the changed array
                        $changedIndexes[] = [
                            'inventory_id' => $id,
                            'product_name' => $inventory->inventoryable->name, // Get the product's name
                            'on_hand' => $inventoryData['on_hand'],
                            'available' => $inventoryData['available'],
                            'from_on_hand' => $this->oldproductsChanges[$id]['on_hand'],
                            'to_on_hand' => $inventoryData['on_hand'],
                            'from_available' => $this->oldproductsChanges[$id]['available'],
                            'to_available' => $inventoryData['available'],
                        ];
                    }
                }
            }
        }

        foreach ($this->materialsChanges as $id => $inventoryData) {
            // Check if the current value differs from the original value for materials
            if (isset($this->oldmaterialsChanges[$id])) {
                if ($inventoryData['on_hand'] != $this->oldmaterialsChanges[$id]['on_hand'] || $inventoryData['available'] != $this->oldmaterialsChanges[$id]['available']) {
                    // Add the index to the changed array
                    $inventory = Inventory::find($id);

                    if ($inventory && $inventory->inventoryable) {
                        // Assuming 'inventoryable' is the relationship with RawMaterial
                        // Add the index, material name, and other necessary data to the changed array
                        $changedIndexes[] = [
                            'inventory_id' => $id,
                            'product_name' => $inventory->inventoryable->name, // Get the material's name
                            'on_hand' => $inventoryData['on_hand'],
                            'available' => $inventoryData['available'],
                            'from_on_hand' => $this->oldmaterialsChanges[$id]['on_hand'],
                            'to_on_hand' => $inventoryData['on_hand'],
                            'from_available' => $this->oldmaterialsChanges[$id]['available'],
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


    public function updateMaterialNew($id)
    {
        if (!array_key_exists($id, $this->materialsChanges)) {
            $this->materialsChanges[$id] = [
                'available' => 0,
                'on_hand' => 0,
                'new' => 0,
            ];
        }

        if ($this->materialsChanges[$id]['available'] === '') {
            $this->materialsChanges[$id]['available'] = 0;
            $this->updateMaterialAvailable($id);
            return;
        }

        if ($this->materialsChanges[$id]['new'] < 0) {
            $this->addError("materialsChanges.$id.new", 'The value must be positive.');
            return;
        }

        $new = $this->materialsChanges[$id]['new'];
        $oldAvailable = $this->oldmaterialsChanges[$id]['available'];
        $oldOnHand = $this->oldmaterialsChanges[$id]['on_hand']; // Old value of on_hand
        $newOnHand = $this->materialsChanges[$id]['on_hand']; // New value of on_hand
        $available = $this->materialsChanges[$id]['available'];
        $this->updateMaterialAvailable($id);

        $difference = $oldOnHand - $oldAvailable;

        $this->materialsChanges[$id]['available'] = $oldAvailable + $new;
        $this->materialsChanges[$id]['on_hand'] = $oldOnHand + $new;
    }

    public function updateMaterialAvailable($id)
    {
        if (!array_key_exists($id, $this->materialsChanges)) {
            $this->materialsChanges[$id] = [
                'available' => 0,
                'on_hand' => 0,
                'new' => 0,
            ];
        }

        if ($this->materialsChanges[$id]['on_hand'] === '') {
            $this->materialsChanges[$id]['on_hand'] = 0;
            $this->updateMaterialOnHand($id);
            return;
        }

        $oldOnHand = $this->oldmaterialsChanges[$id]['on_hand']; // Old value of on_hand
        $newOnHand = $this->materialsChanges[$id]['on_hand']; // New value of on_hand
        $available = $this->materialsChanges[$id]['available'];
        $oldAvailable = $this->oldmaterialsChanges[$id]['available'];

        // Calculate the difference and apply the same to 'available'

        $difference = $oldOnHand - $oldAvailable;

        // Update the 'available' field by the same difference
        $this->materialsChanges[$id]['available'] = $newOnHand - $difference;
    }

    public function updateMaterialOnHand($id)
    {
        if (!array_key_exists($id, $this->materialsChanges)) {
            $this->materialsChanges[$id] = [
                'available' => 0,
                'on_hand' => 0,
                'new' => 0,
            ];
        }

        $oldAvailable = $this->oldmaterialsChanges[$id]['available'];
        $newAvailable = $this->materialsChanges[$id]['available'];
        $oldOnHand = $this->oldmaterialsChanges[$id]['on_hand'];

        // Calculate the difference and apply the same to 'on_hand'
        $difference = $oldAvailable - $oldOnHand;

        // Update the 'on_hand' field by the same difference
        $this->materialsChanges[$id]['on_hand'] = $newAvailable - $difference;
    }

    public function updateNew($id)
    {
        if (!array_key_exists($id, $this->productsChanges)) {
            $this->productsChanges[$id] = [
                'available' => 0,
                'on_hand' => 0,
                'new' => 0,
            ];
        }

        if ($this->productsChanges[$id]['new'] === '') {
            $this->productsChanges[$id]['new'] = 0;
            $this->updateAvailable($id);
            return;
        }

        if ($this->productsChanges[$id]['new'] < 0) {
            $this->addError("productsChanges.$id.new", 'The value must be positive.');
            return;
        }

        $new = $this->productsChanges[$id]['new'];
        $oldAvailable = $this->oldproductsChanges[$id]['available'];
        $oldOnHand = $this->oldproductsChanges[$id]['on_hand']; // Old value of on_hand
        $newOnHand = $this->productsChanges[$id]['on_hand']; // New value of on_hand
        $available = $this->productsChanges[$id]['available'];
        $this->updateAvailable($id);

        $difference = $oldOnHand - $oldAvailable;

        $this->productsChanges[$id]['available'] = $oldAvailable + $new;
        $this->productsChanges[$id]['on_hand'] = $oldOnHand + $new;
        

    }

    public function updateAvailable($id)
    {
        if (!array_key_exists($id, $this->productsChanges)) {
            $this->productsChanges[$id] = [
                'available' => 0,
                'on_hand' => 0,
                'new' => 0,
            ];
        }

        if ($this->productsChanges[$id]['on_hand'] === '') {
            $this->productsChanges[$id]['on_hand'] = 0;
            $this->updateOnHand($id);
            return;
        }

        $oldOnHand = $this->oldproductsChanges[$id]['on_hand']; // Old value of on_hand
        $newOnHand = $this->productsChanges[$id]['on_hand']; // New value of on_hand
        $available = $this->productsChanges[$id]['available'];
        $oldAvailable = $this->oldproductsChanges[$id]['available'];

        // Calculate the difference and apply the same to 'available'

        $difference = $oldOnHand - $oldAvailable;

        // Update the 'available' field by the same difference
        $this->productsChanges[$id]['available'] = $newOnHand - $difference;
    }

    public function updateOnHand($id)
    {
        if (!array_key_exists($id, $this->productsChanges)) {
            $this->productsChanges[$id] = [
                'available' => 0,
                'on_hand' => 0,
                'new' => 0,
            ];
        }

        $oldAvailable = $this->oldproductsChanges[$id]['available'];
        $newAvailable = $this->productsChanges[$id]['available'];
        $oldOnHand = $this->oldproductsChanges[$id]['on_hand'];

        // Calculate the difference and apply the same to 'on_hand'
        $difference = $oldAvailable - $oldOnHand;

        // Update the 'on_hand' field by the same difference
        $this->productsChanges[$id]['on_hand'] = $newAvailable - $difference;
    }

    public function resetChangesTracking()
    {
        $this->hasChanges = false; // Reset change tracking
    }

    public function updatedPage()
    {
        $this->reset(['productsChanges', 'oldproductsChanges','materialsChanges', 'oldmaterialsChanges', 'hasChanges']);
    }

    public function submitTransaction()
    {
        foreach ($this->newChanges as $changedIndex) {
            if (abs((int) $changedIndex['from_on_hand'] - (int) $changedIndex['to_on_hand']) !== abs((int) $changedIndex['from_available'] - (int) $changedIndex['to_available'])) {
                $this->alertFailed();
                return;
            } else {
                $quantity = (int) $changedIndex['to_on_hand'];
                $inventory = Inventory::findOrFail($changedIndex['inventory_id']);
                
                if ($inventory->inventoryable_type === Product::MORPH_TYPE) {
                    $this->authorize('update', $inventory);
                    $inventory->updateOnHandWithNewValue($quantity, $this->transRemark);
                } elseif ($inventory->inventoryable_type === RawMaterial::MORPH_TYPE) {
                    $this->authorize('update', $inventory);
                    $inventory->updateOnHandWithNewValue($quantity, $this->transRemark);
                }
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
            ->where('inventoryable_type', Product::MORPH_TYPE)
            ->paginate(20, ['*'], 'productsPage');

        $materialInventories = Inventory::search($this->materialSearchTerm)
            ->sortBy($this->sortColomn, $this->sortDirection) // or sortByPrice($sortDirection) / sortByWeight($sortDirection)
            ->where('inventoryable_type', RawMaterial::MORPH_TYPE)
            ->paginate(20, ['*'], 'materialsPage');

        $this->productsChanges = array_replace_recursive($inventories->keyBy('id')->toArray(), $this->productsChanges);
        $this->oldproductsChanges = array_replace_recursive($inventories->keyBy('id')->toArray(), $this->oldproductsChanges);

        $this->materialsChanges = array_replace_recursive($materialInventories->keyBy('id')->toArray(), $this->materialsChanges);
        $this->oldmaterialsChanges = array_replace_recursive($materialInventories->keyBy('id')->toArray(), $this->oldmaterialsChanges);

        $this->fetched_inventories_IDs = $inventories->pluck('id')->toArray();

        return view('livewire.products.inventory-transaction-index', [
            'inventories' => $inventories,
            'materialInventories' => $materialInventories,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'inventories' => 'active']);
    }
}
