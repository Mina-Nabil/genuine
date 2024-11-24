<?php

namespace App\Livewire\Products;

use App\Models\Products\Category;
use App\Models\Products\Product;
use App\Traits\AlertFrontEnd;
use Illuminate\Routing\Route;
use Livewire\Component;
use Livewire\WithPagination;

class ProductIndex extends Component
{
    use WithPagination , AlertFrontEnd;

    public $page_title = '• Products';

    public $fetched_products_IDs;
    public $search;
    public $selectAll = false; //to select all in the page
    public $selectedProducts = [];
    public $newProductSection = false;
    public $selectedAllProducts = false; //to select all products

    public $productName;
    public $productPrice;
    public $productWeight;
    public $category_id;
    public $initialQuantity;

    public $searchTerm;
    public $categoryId;
    public $minPrice;
    public $maxPrice;
    public $minWeight;
    public $maxWeight;
    public $sortColomn;
    public $sortDirection = 'asc';

    public function sortByColomn($colomn){
        $this->sortColomn = $colomn;
        if($this->sortDirection){
            if($this->sortDirection === 'asc'){
                $this->sortDirection = 'desc';
            }else{
                $this->sortDirection = 'asc';
            }
        }
    }

    ///// Frontend Hnadling
    public function openNewProductSec()
    {
        $this->newProductSection = true;
    }

    public function closeNewProductSec()
    {
        $this->reset(['productName', 'productPrice', 'productWeight', 'category_id', 'newProductSection']);
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedProducts = $this->fetched_products_IDs;
        } else {
            $this->selectedProducts = [];
        }
    }

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function addNewProduct()
    {
        $this->validate([
            'productName' => 'required|string|max:255',
            'productPrice' => 'required|numeric|min:0',
            'productWeight' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'initialQuantity' => 'required|numeric|min:0',
        ],attributes:[
            'category_id' => 'category',
        ]);

        // Create a new product
        $res = Product::createProduct($this->category_id , $this->productName, $this->productPrice, $this->productWeight ,null,$this->initialQuantity );

        if ($res) {
            return redirect(Route('product.show' , $res->id));
        }else{
            $this->alertFailed();
        }

    }

    public function selectAllProducts()
    {
        $this->selectedAllProducts = true;
        $this->selectedProducts = Product::pluck('id')->toArray();
    }

    public function undoSelectAllProducts()
    {
        $this->selectedAllProducts = false;
        $this->selectedProducts = $this->fetched_products_IDs;
    }

    public function mount(){
        $this->authorize('viewAny' , Product::class);
    }

    public function render()
    {
        $products = Product::search($this->searchTerm)
            ->filterByCategory($this->categoryId)
            ->filterByPriceRange($this->minPrice, $this->maxPrice)
            ->filterByWeightRange($this->minWeight, $this->maxWeight)
            ->sortBy($this->sortColomn,$this->sortDirection) // or sortByPrice($sortDirection) / sortByWeight($sortDirection)
            ->paginate(30);

        $categories = Category::all();

        $this->fetched_products_IDs = $products->pluck('id')->toArray();
        return view('livewire.products.product-index', [
            'products' => $products,
            'categories' => $categories,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'products' => 'active']);
    }
}
