<?php

namespace App\Livewire\Orders;

use App\Models\Customers\Zone;
use App\Models\Orders\PeriodicOrder;
use App\Models\Orders\PeriodicOrderProduct;
use App\Models\Products\Product;
use App\Traits\AlertFrontEnd;
use Livewire\Component;

class PeriodicOrderShow extends Component
{
    use AlertFrontEnd;

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
    public $locationUrl;
    public $customerPhone;
    public $zoneId;

    //note
    public $updateNoteSec = false;
    public $note;

    //add products
    public $addProductsSection = false;
    public $searchAddProducts = ''; //search term
    public $productsToAdd = [];

    //Edit Product
    public $periodicOrderProduct;
    public $productQuantity;
    public $productPrice;

    //remove product
    public $deleteProductId;

    //periodic details
    public $periodicOption;
    public $orderDay;
    public $editPeriodicDetailsSec = false;

    public function openEditPeriodicDetails()
    {
        $this->editPeriodicDetailsSec = true;
        $this->periodicOption = $this->order->periodic_option;
        $this->orderDay = $this->order->order_day;
    }

    public function closeEditPeriodicDetails()
    {
        $this->reset(['editPeriodicDetailsSec', 'periodicOption', 'orderDay']);
    }

    public function updatePeriodicDetails()
    {
        $this->authorize('update', $this->order);
        if ($this->periodicOption === PeriodicOrder::PERIODIC_WEEKLY || $this->periodicOption === PeriodicOrder::PERIODIC_BI_WEEKLY) {
            $this->validate([
                'periodicOption' => 'required|in:' . implode(',', PeriodicOrder::PERIODIC_OPTIONS),
                'orderDay' => 'required|numeric|min:1|max:7',
            ],messages:[
                'orderDay.required' => 'The order day is required.',
                'orderDay.numeric' => 'The order day must be a day of week.',
                'orderDay.min' => 'The order day must be a day of week.',
                'orderDay.max' => 'The order day must be a day of week.',
            ]);
        } else {
            $this->validate([
                'periodicOption' => 'required|in:' . implode(',', PeriodicOrder::PERIODIC_OPTIONS),
                'orderDay' => 'required|numeric|min:1|max:30',
            ],messages:[
                'orderDay.required' => 'The order day is required.',
                'orderDay.numeric' => 'The order day must be a day of month.',
                'orderDay.min' => 'The order day must be a day of month.',
                'orderDay.max' => 'The order day must be a day of month.',
            ]);
        }

        $res = $this->order->updatePeriodicDetails($this->periodicOption,$this->orderDay);

        if ($res) {
            $this->closeEditPeriodicDetails();
            $this->mount($this->order->id);
            $this->alertSuccess('Periodic details updated');
        } else {
            $this->alertFailed();
        }
    }

    public function showConfirmRemoveProduct($id)
    {
        $this->deleteProductId = $id;
    }

    public function hideConfirmRemoveProduct()
    {
        $this->deleteProductId = null;
    }

    public function deleteProduct()
    {
        $this->authorize('delete', $this->order);
        $res = PeriodicOrderProduct::findOrFail($this->deleteProductId)->deleteProduct();
        if ($res) {
            $this->hideConfirmRemoveProduct();
            $this->mount($this->order->id);
            $this->alertSuccess('Products deleted');
        } else {
            $this->alertFailed();
        }
    }

    public function openEditProduct($id)
    {
        $this->periodicOrderProduct = PeriodicOrderProduct::findOrFail($id);
        $this->productQuantity = $this->periodicOrderProduct->quantity;
        $this->productPrice = $this->periodicOrderProduct->price;
    }

    public function closeEditProduct()
    {
        $this->reset(['periodicOrderProduct', 'productQuantity', 'productPrice']);
    }

    public function updateProduct()
    {
        $this->authorize('update', $this->order);

        $this->validate([
            'productQuantity' => 'required|numeric|min:1',
            'productPrice' => 'required|numeric|min:1',
        ]);

        $res = $this->periodicOrderProduct->editProductInfo($this->productQuantity, $this->productPrice);

        if ($res) {
            $this->closeEditProduct();
            $this->alertSuccess('Products updated');
        } else {
            $this->alertFailed();
        }
    }

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
        $p = Product::findOrFail($id);

        // Check if the product already exists in the array
        foreach ($this->productsToAdd as &$product) {
            if ($product['product_id'] == $id) {
                $product['quantity'] += 1;
                $this->reset(['searchAddProducts']);
                return; // Exit after updating the quantity
            }
        }

        // If not found, add a new product
        $this->productsToAdd[] = [
            'product_id' => $id,
            'quantity' => 1,
            'price' => $p->price,
            'name' => $p->name,
            'combo_id' => null,
        ];

        $this->reset(['searchAddProducts']);
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
        $this->locationUrl = $this->order->location_url;
        $this->customerPhone = $this->order->customer_phone;
        $this->zoneId = $this->order->zone_id;
    }

    public function closeUpdateShippingDetails()
    {
        $this->reset(['updateShippingSec', 'customerName', 'locationUrl', 'shippingAddress', 'customerPhone', 'zoneId']);
    }

    public function updateShippingDetails()
    {
        $this->authorize('update', $this->order);
        $this->validate(
            [
                'customerName' => 'required|string|max:255',
                'shippingAddress' => 'required|string|max:255',
                'locationUrl' => 'required|string|max:255',
                'customerPhone' => 'required|string|max:15',
                'zoneId' => 'required|exists:zones,id',
            ],
            attributes: [
                'zoneId' => 'zone',
                'locationUrl' => 'location url',
            ],
        );

        $res = $this->order->updateShippingDetails($this->customerName, $this->shippingAddress, $this->locationUrl, $this->customerPhone, $this->zoneId);

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

        $PERIODIC_OPTIONS  = PeriodicOrder::PERIODIC_OPTIONS;
        $daysofweek = PeriodicOrder::daysOfWeek;

        return view('livewire.orders.periodic-order-show', [
            'products' => $products,
            'PERIODIC_OPTIONS' => $PERIODIC_OPTIONS,
            'daysofweek' => $daysofweek
        ]);
    }
}
