<?php

namespace App\Livewire\Products;

use App\Models\Products\Transaction;
use Livewire\Component;
use App\Traits\AlertFrontEnd;
use Livewire\WithPagination;

use App\Models\Prod;
use App\Models\Products\Product;

class TransactionIndex extends Component
{
    use WithPagination, AlertFrontEnd;

    public $page_title = '• Transactions';

    public $fetched_trans_IDs;
    public $search;
    public $selectAll = false; //to select all in the page
    public $selectedTrans = [];
    public $selectedAllTrans = false; //to select all Trans

    public $sortColomn;
    public $sortDirection = 'asc';
    public $product_id;

    protected $queryString = ['product_id'];

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
            $this->selectedTrans = $this->fetched_trans_IDs;
        } else {
            $this->selectedTrans = [];
        }
    }

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function selectAllTrans()
    {
        $this->selectedAllTrans = true;
        $this->selectedTrans = Transaction::pluck('id')->toArray();
    }

    public function undoSelectAllTrans()
    {
        $this->selectedAllTrans = false;
        $this->selectedTrans = $this->fetched_trans_IDs;
    }

    public function clearProduct(){
        $this->product_id = null;
    }

    public function render()
    {
        $product = null;
        if ($this->product_id) {
            $product = Product::find($this->product_id);
        }
        $trans = Transaction::filterByProduct($this->search, $this->product_id)
            ->latest()
            ->paginate(50);
        $this->fetched_trans_IDs = $trans->pluck('id')->toArray();
        return view('livewire.products.transaction-index', [
            'trans' => $trans,
            'product' => $product,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'Trans' => 'active']);
    }
}
