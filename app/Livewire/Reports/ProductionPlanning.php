<?php

namespace App\Livewire\Reports;

use App\Models\Materials\RawMaterial;
use App\Models\Orders\OrderProduct;
use App\Models\Products\Product;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class ProductionPlanning extends Component
{
    use WithPagination;
    public $page_title = '• Production Planning';

    public $searchTerm;
    public $deliveryDate;
    public $isToDate = false; //actual day or not

    public $fetched_products_IDs;
    public $search;
    public $searchRm;
    public $selectAll = false; //to select all in the page
    public $selectedProducts = [];
    public $selectedAllProducts = false; //to select all products

    public $sortColomn;
    public $sortDirection = 'asc';

    public function updatedSearch(){
        $this->resetPage('productPage');
    }

    public function updatedSearchRm(){
        $this->resetPage('materialPage');
    }

    public function mount()
    {
        $this->authorize('viewProductionPlanning',Product::class);
        $this->deliveryDate = Carbon::tomorrow()->toDateString();
    }

    public function render()
    {
        $orderProducts = OrderProduct::productionPlanning($this->deliveryDate,$this->isToDate,$this->searchTerm)->paginate(50, ['*'] , 'productPage');
        $rawMaterials = RawMaterial::search($this->searchRm)->paginate(10 , ['*'] , 'materialPage');
        return view('livewire.reports.production-planning',[
            'orderProducts' => $orderProducts,
            'rawMaterials' => $rawMaterials
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'productions' => 'active']);;
    }
}
