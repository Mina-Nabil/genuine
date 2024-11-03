<?php

namespace App\Livewire\Products;

use App\Models\Products\Product;
use App\Traits\AlertFrontEnd;
use Exception;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class ProductShow extends Component
{
    use AlertFrontEnd,WithPagination;

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

    public $addTransSection;
    public $transQuantity;
    public $transRemark;

    public function loadMore()
    {
        $this->visibleCommentsCount += 5; // Load 5 more comments
    }

    public function showLess()
    {
        $this->visibleCommentsCount = max(5, $this->visibleCommentsCount - 5); // Show less but minimum 5
    }

    public function openEditSection(){
        $this->authorize('update',$this->product);
        $this->productName = $this->product->name;
        $this->productDesc = $this->product->desc;
        $this->editProductSection = true;
    }

    public function closeEditSection(){
        $this->editProductSection = false;
        $this->reset(['productName','productDesc']);
    }

    public function openTransSection(){
        $this->authorize('controlTransactions',$this->product);
        $this->addTransSection = true;
    }

    public function closeTransSection(){
        $this->addTransSection = false;
        $this->reset(['transQuantity','transRemark']);
    }

    public function openEditPriceWeightSection(){
        $this->authorize('update',$this->product);

        $this->productPrice = $this->product->price;
        $this->productWeight = $this->product->weight;
        $this->editProductPriceWeightSection = true;
    }

    public function closeEditPriceWeightSection(){
        $this->editProductPriceWeightSection = false;
        $this->reset(['productPrice','productWeight']);
    }

    public function addTransaction(){

        $this->authorize('controlTransactions',$this->product);

        $this->validate([
            'transQuantity' => 'required|integer',
            'transRemark' => 'nullable|string'
        ]);

        $res = $this->product->inventory->addTransaction($this->transQuantity , $this->transRemark);

            if ($res instanceof Exception) {
                throw ValidationException::withMessages([
                    'transQuantity' => $res->getMessage(),
                ]);
            }elseif($res){
                $this->closeTransSection();
                $this->alertSuccess('Transaction added!');
            }else{
                $this->alertFailed();
            }
            
            
            
    }

    public function updateTitleDesc(){

        $this->authorize('update',$this->product);

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

        $this->authorize('update',$this->product);

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

        $this->authorize('update',$this->product);

        $this->validate([
            'addedComment' => 'required|string'
        ]);
        $this->product->addComment($this->addedComment);
        $this->addedComment = null;
        $this->alertSuccess('Comment added !');
        $this->comments = $this->product->comments()->latest()->take($this->visibleCommentsCount)->get();
    }

    public $description;

    protected $listeners = ['updateDescription'];

    // Method to handle the event emitted by the Quill editor
    public function updateDescription($content)
    {
        $this->description = $content;
    }


    public function mount($id){
        $this->product = Product::findOrFail($id);
        $this->authorize('view',$this->product);
        $this->page_title = '• '.$this->product->name;
    }


    public function render()
    {
        $this->comments = $this->product->comments()->latest()->take($this->visibleCommentsCount)->get();
        $transactions = $this->product->transactions()->orderBy('created_at', 'desc')->paginate(5);
        return view('livewire.products.product-show',[
            'transactions' => $transactions
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'products' => 'active']);
    }
}
