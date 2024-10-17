<?php

namespace App\Livewire\Products;

use App\Models\Products\Product;
use App\Traits\AlertFrontEnd;
use Livewire\Component;

class ProductShow extends Component
{
    use AlertFrontEnd;

    public $page_title;
    public $product;
    public $comments;
    public $addedComment;
    public $visibleCommentsCount = 5; // Initially show 5 comments

    public $editProductSection;
    public $productName;
    public $productDesc;

    public $editProductPriceWeightSection;
    public $productPrice;
    public $productWeight;


    public function openEditSection(){
        $this->productName = $this->product->name;
        $this->productDesc = $this->product->desc;
        $this->editProductSection = true;
    }

    public function closeEditSection(){
        $this->editProductSection = false;
        $this->reset(['productName','productDesc']);
    }

    public function openEditPriceWeightSection(){
        $this->productPrice = $this->product->price;
        $this->productWeight = $this->product->weight;
        $this->editProductPriceWeightSection = true;
    }

    public function closeEditPriceWeightSection(){
        $this->editProductPriceWeightSection = false;
        $this->reset(['productPrice','productWeight']);
    }



    public function updateTitleDesc(){
        $this->validate([
            'productName' => 'required|string|max:255',
            'productDesc' => 'nullable|string'
        ]);

        $res = $this->product->updateProductTitleDesc($this->productName,$this->productDesc);

        if ($res) {
            $this->closeEditSection();
            $this->alertSuccess('Product updated!');
        }else{
            $this->alertFailed();
        }
    }

    public function updatePriceWeight(){
        $this->validate([
            'productPrice' => 'required|numeric|min:0',
            'productWeight' => 'required|numeric|min:0',
        ]);

        $res = $this->product->updateProductPriceWeight($this->productPrice,$this->productWeight);

        if ($res) {
            $this->closeEditPriceWeightSection();
            $this->alertSuccess('Product updated!');
        }else{
            $this->alertFailed();
        }
    }


    public function addComment(){
        $this->validate([
            'addedComment' => 'required|string'
        ]);
        $this->product->addComment($this->addedComment);
        $this->addedComment = null;
        $this->alertSuccess('Comment added !');
        $this->comments = $this->product->comments()->latest()->take($this->visibleCommentsCount)->get();
    }

    public function loadMore()
    {
        $this->visibleCommentsCount += 5; // Load 5 more comments
    }

    public function showLess()
    {
        $this->visibleCommentsCount = max(5, $this->visibleCommentsCount - 5); // Show less but minimum 5
    }



    public function mount($id){
        $this->product = Product::findOrFail($id);
        $this->page_title = '• '.$this->product->name;
    }

    public function render()
    {
        $this->comments = $this->product->comments()->latest()->take($this->visibleCommentsCount)->get();
        return view('livewire.products.product-show')->layout('layouts.app', ['page_title' => $this->page_title, 'products' => 'active']);
    }
}
