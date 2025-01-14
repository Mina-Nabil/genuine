<?php

namespace App\Livewire\Materials;

use App\Models\Materials\Supplier;
use Livewire\Component;

class SupplierShow extends Component
{
    public $page_title;

    public $supplier;
    
    public function mount($id)
    {
        $this->supplier = Supplier::findOrFail($id);
        $this->page_title = '• ' . $this->supplier->name;
    }

    public function render()
    {

        return view('livewire.materials.supplier-show')->layout('layouts.app', ['page_title' => $this->page_title, 'suppliers' => 'active']);
    }
}
