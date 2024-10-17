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
