<?php

namespace App\Livewire\Reports;

use App\Models\Orders\OrderProduct;
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
    public $selectAll = false; //to select all in the page
    public $selectedProducts = [];
    public $selectedAllProducts = false; //to select all products

    public $sortColomn;
    public $sortDirection = 'asc';

    public function mount()
    {
        $this->deliveryDate = Carbon::tomorrow()->toDateString();
    }

    public function render()
    {
        $orderProducts = OrderProduct::productionPlanning($this->deliveryDate,$this->isToDate,$this->searchTerm)->paginate(50);
        return view('livewire.reports.production-planning',[
            'orderProducts' => $orderProducts
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'productions' => 'active']);;
    }
}
