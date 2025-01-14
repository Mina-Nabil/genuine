<?php

namespace App\Livewire\Materials;

use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierIndex extends Component
{
    use AlertFrontEnd, WithPagination;
    public $page_title = '• Suppliers';

    public function render()
    {

        return view('livewire.materials.supplier-index');
    }
}
