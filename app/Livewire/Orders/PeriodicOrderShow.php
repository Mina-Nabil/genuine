<?php

namespace App\Livewire\Orders;

use App\Models\Customers\Zone;
use App\Models\Orders\PeriodicOrder;
use App\Models\Orders\PeriodicOrderProduct;
use App\Models\Products\Product;
use Livewire\Component;

class PeriodicOrderShow extends Component
{
    public $page_title;
    public $order;
    public $discountAmount;
    public $comments;
    public $addedComment;
    public $visibleCommentsCount = 5; // Initially show 5 comments

    public $zones;

    //shipping details
    public $updateShippingSec = false;
    public $customerName;
    public $shippingAddress;
    public $customerPhone;
    public $zoneId;

    //note
    public $updateNoteSec = false;
    public $note;

    //add products
    public $addProductsSection = false;
    public $searchAddProducts = ''; //search term
    public $productsToAdd = [];
    public function openAddProductsSec()
    {
        $this->addProductsSection = true;
    }

    public function closeAddProductsSec()
    {
        $this->reset(['addProductsSection', 'searchAddProducts', 'productsToAdd']);
    }
    public function addProductRow($id)
    {
        $p = PeriodicOrderProduct::findOrFail($id);
        $this->reset(['searchAddProducts']);
        $this->productsToAdd[] = ['product_id' => $id, 'quantity' => 1, 'price' => $p->price, 'name' => $p->name, 'combo_id' => null];
    }
    public function removeProductRow($index)
    {
        unset($this->productsToAdd[$index]);
        $this->productsToAdd = array_values($this->productsToAdd);
    }
    public function addProducts()
    {
        $res = $this->order->addProducts($this->productsToAdd);

        if ($res) {
            $this->closeAddProductsSec();
            $this->alertSuccess('Products added');
        } else {
            $this->alertFailed();
        }
    }
    public function openUpdateNote()
    {
        $this->updateNoteSec = true;
        $this->note = $this->order->note;
    }

    public function closeUpdateNote()
    {
        $this->reset(['updateNoteSec', 'note']);
    }

    public function updateNote()
    {
        $this->authorize('update', $this->order);
        $this->validate([
            'note' => 'nullable|string',
        ]);

        $res = $this->order->updateNote($this->note);

        if ($res) {
            $this->mount($this->order->id);
            $this->closeUpdateNote();
            $this->alertSuccess('Note updated');
        } else {
            $this->alertFailed();
        }
    }
    public function openUpdateShippingDetails()
    {
        $this->authorize('update', $this->order);
        $this->updateShippingSec = true;
        $this->customerName = $this->order->customer_name;
        $this->shippingAddress = $this->order->shipping_address;
        $this->customerPhone = $this->order->customer_phone;
        $this->zoneId = $this->order->zone_id;
    }

    public function closeUpdateShippingDetails()
    {
        $this->reset(['updateShippingSec', 'customerName', 'shippingAddress', 'customerPhone', 'zoneId']);
    }

    public function updateShippingDetails()
    {
        $this->authorize('update', $this->order);
        $this->validate(
            [
                'customerName' => 'required|string|max:255',
                'shippingAddress' => 'required|string|max:255',
                'customerPhone' => 'required|string|max:15',
                'zoneId' => 'required|exists:zones,id',
            ],
            attributes: [
                'zoneId' => 'zone',
            ],
        );

        $res = $this->order->updateShippingDetails($this->customerName, $this->shippingAddress, $this->customerPhone, $this->zoneId);

        if ($res) {
            $this->mount($this->order->id);
            $this->closeUpdateShippingDetails();
            $this->alertSuccess('Shipping updated');
        } else {
            $this->alertFailed();
        }
    }
    public function loadMore()
    {
        $this->visibleCommentsCount += 5; // Load 5 more comments
    }

    public function showLess()
    {
        $this->visibleCommentsCount = max(5, $this->visibleCommentsCount - 5); // Show less but minimum 5
    }

    public function addComment()
    {
        $this->authorize('update', $this->order);

        $this->validate([
            'addedComment' => 'required|string',
        ]);
        $this->order->addComment($this->addedComment);
        $this->addedComment = null;
        $this->alertSuccess('Comment added !');
        $this->comments = $this->order
            ->comments()
            ->latest()
            ->take($this->visibleCommentsCount)
            ->get();
    }
    public function mount($id)
    {
        $this->order = PeriodicOrder::findOrFail($id);
        $this->authorize('view', $this->order);
        $this->page_title = '• Orders • #' . $this->order->order_number;
        $this->zones = Zone::select('id', 'name')->get();
    }

    public function render()
    {
        $products = Product::search($this->searchAddProducts)
            ->take(5)
            ->get();

        $this->comments = $this->order
            ->comments()
            ->latest()
            ->take($this->visibleCommentsCount)
            ->get();

        return view('livewire.orders.periodic-order-show',[
            'products' => $products,
        ]);
    }
}
