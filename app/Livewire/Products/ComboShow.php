<?php

namespace App\Livewire\Products;

use App\Models\Products\Combo;
use App\Models\Products\Product;
use App\Traits\AlertFrontEnd;
use Livewire\Component;

class ComboShow extends Component
{
    use AlertFrontEnd;

    public $combo;
    public $page_title;

    public $comboName;
    public $editComboSec;


    public $product_id;
    public $productQuantity;
    public $productPrice;
    public $newProductSec = false;

    public function closeEditComboSec(){
        $this->editComboSec = false;
        $this->reset(['comboName']);
    }

    public function openEditComboSec(){
        $this->authorize('update',$this->combo);
        $this->comboName  = $this->combo->name;
        $this->editComboSec = true;
    }

    public function deleteCombo(){
        $this->authorize('update',$this->combo);
        $this->combo->delete();
        $this->alertSuccess('Combo deleted!');
        return redirect()->route('combo.index');
    }

    public function editCombo(){
        $this->authorize('update',$this->combo);
        $this->validate([
            'comboName' => 'required|string|max:255',
        ]);

        $res = $this->combo->updateCombo($this->comboName);

        if ($res) {
            $this->closeEditComboSec();
            $this->mount($this->combo->id);
            $this->alertSuccess('Combo updated!');
        }else{
            $this->alertFailed();
        }
    }

    public function addProduct()
    {
        $this->authorize('updateProducts',$this->combo);
        $this->validate([
            'product_id' => 'required|exists:products,id',
            'productQuantity' => 'required|numeric|min:1',
            'productPrice' => 'required|numeric|min:1',
        ]);

        $res = $this->combo->addProductToCombo($this->product_id,$this->productQuantity,$this->productPrice);

        if ($res) {
            $this->closeAddProductSec();
            $this->mount($this->combo->id);
            $this->alertSuccess('Product added!');
        }else{
            $this->alertFailed();
        }
    }

    public function closeAddProductSec(){
        $this->newProductSec = false;
        $this->reset(['product_id','productQuantity','productPrice']);
    }

    public function openAddProductSec(){
        $this->newProductSec = true;
    }

    public function mount($id)
    {
        $this->combo = Combo::findOrFail($id);
        $this->page_title = '• Combo • ' . $this->combo->name;
    }

    public function removeProduct($id)
    {
        $this->authorize('update', $this->combo);
        if ($this->combo->products->count() > 1) {
            $res = $this->combo->removeProductFromCombo($id);
            if ($res) {
                $this->mount($this->combo->id);
                $this->alertSuccess('Product Removed!');
            } else {
                $this->alertFailed();
            }
        } else {
            $this->alertFailed();
        }
    }

    public function render()
    {
        $all_products = Product::all();
        return view('livewire.products.combo-show', [
            'all_products' => $all_products,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'combos' => 'active']);
    }
}
