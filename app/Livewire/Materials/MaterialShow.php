<?php

namespace App\Livewire\Materials;

use App\Models\Materials\RawMaterial;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\WithPagination;

class MaterialShow extends Component
{
    use AlertFrontEnd, WithPagination;

    public $page_title;
    public $material;
    public $comments;
    public $addedComment;
    public $visibleCommentsCount = 5; // Initially show 5 comments

    public $editInfoSection = false;
    public $materialName;
    public $materialDesc;
    public $materialMinLimit;

    public function loadMore()
    {
        $this->visibleCommentsCount += 5; // Load 5 more comments
    }

    public function showLess()
    {
        $this->visibleCommentsCount = max(5, $this->visibleCommentsCount - 5); // Show less but minimum 5
    }

    public function openEditSection()
    {
        $this->editInfoSection = true;
        $this->materialName = $this->material->name;
        $this->materialDesc = $this->material->desc;
        $this->materialMinLimit = $this->material->min_limit;
    }

    public function closeEditSection()
    {
        $this->reset(['editInfoSection', 'materialName', 'materialDesc', 'materialMinLimit']);
    }

    public function updateMaterialInfo()
    {
        $this->validate([
            'materialName' => 'required|string|max:255',
            'materialDesc' => 'nullable|string|max:255',
            'materialMinLimit' => 'nullable|numeric|min:0',
        ]);

        $res = $this->material->updateInfo($this->materialName, $this->materialDesc, $this->materialMinLimit);

        if ($res) {
            $this->alertSuccess('Material info updated successfully');
            $this->closeEditSection();
        } else {
            $this->alertError('Failed to update material info');
        }
    }



    public function mount($id){
        $this->material = RawMaterial::findOrFail($id);
        $this->authorize('view', $this->material);
        $this->page_title = '• ' . $this->material->name;
    }

    public function render()
    {
        $this->comments = $this->material
            ->comments()
            ->latest()
            ->take($this->visibleCommentsCount)
            ->get();

        $transactions = $this->material->transactions()->orderBy('created_at', 'desc')->paginate(5, ['*'], 'transactionPage');
        $suppliers = $this->material->suppliers()->orderBy('created_at', 'desc')->paginate(10, ['*'], 'suppliersPage');
        return view('livewire.materials.material-show', [
            'transactions' => $transactions,
            'suppliers' => $suppliers
        ]);
    }
}
