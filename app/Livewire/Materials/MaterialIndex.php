<?php

namespace App\Livewire\Materials;

use App\Models\Materials\RawMaterial;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\WithPagination;

class MaterialIndex extends Component
{
    use AlertFrontEnd, WithPagination;
    public $page_title = '• Raw Materials';

    public $fetched_materials_IDs;
    public $search;
    public $selectAll = false;
    public $selectedMaterials = [];
    public $newMaterialSection = false;
    public $selectedAllMaterials = false;

    public $name;
    public $limit;
    public $desc;
    public $initialQty = 0;

    public function addMaterial()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:raw_materials,name',
            'desc' => 'nullable|string|max:1000',
            'limit' => 'nullable|numeric|min:0',
            'initialQty' => 'required|numeric|min:0',
        ]);

        $res = RawMaterial::createRawMaterial($this->name, $this->limit, $this->desc, $this->initialQty);

        if ($res) {
            $this->alertSuccess('created!');
            $this->closeNewMaterialSection();
            // return redirect(route('materials.show', $res->id));
        } else {
            $this->alertFailed();
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedMaterials = $this->fetched_materials_IDs;
        } else {
            $this->selectedMaterials = [];
        }
    }

    public function selectAllMaterials()
    {
        $this->selectedAllMaterials = true;
        $this->selectedMaterials = RawMaterial::pluck('id')->toArray();
    }

    public function undoSelectAllMaterials()
    {
        $this->selectedAllMaterials = false;
        $this->selectedMaterials = $this->fetched_materials_IDs;
    }
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openNewMaterialSection(){
        $this->newMaterialSection = true;
    }

    public function closeNewMaterialSection()
    {
        $this->reset(['newMaterialSection', 'name', 'desc', 'limit', 'initialQty']);
    }

    public function render()
    {
        $materials = RawMaterial::search($this->search)->paginate(50);
        $this->fetched_materials_IDs = $materials->pluck('id')->toArray();
        return view('livewire.materials.material-index',[
            'materials' => $materials
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'materials' => 'active']);
    }
}
