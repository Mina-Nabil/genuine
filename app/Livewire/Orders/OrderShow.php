<?php

namespace App\Livewire\Orders;

use App\Models\Customers\Zone;
use App\Models\Orders\Order;
use App\Models\Orders\OrderRemovedProduct;
use App\Models\Payments\CustomerPayment;
use App\Models\Products\Product;
use App\Models\Users\Driver;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Livewire\Component;
use tidy;

class OrderShow extends Component
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
    public $customerPhone;
    public $zoneId;

    //driver
    public $setDriverSection = false;
    public $searchDrivers = '';
    public $confirmRemoveDriver;

    //note
    public $updateNoteSec = false;
    public $note;

    //delivery date
    public $ddateSection = false;
    public $ddate;

    //returns
    public $returnSection;
    public $cancelledProducts = [];
    public $cancelledProductsTotalAmount;
    public $returnPaymentMehod;
    public $removeReasons = [];
    public $isReturnShippingAmount = false;
    public $reason;
    public $otherReason;

    //add products
    public $addProductsSection = false;
    public $searchAddProducts = ''; //search term
    public $productsToAdd = [];

    public $NextStatuses;

    //pay from balance
    public $isOpenPayFromBalanceSec;

    public function openSetDriverSection(){
        $this->setDriverSection = true;
    }

    public function closeSetDriverSection(){
        $this->reset(['setDriverSection','searchDrivers','confirmRemoveDriver']);
    }

    public function openAddProductsSec()
    {
        $this->addProductsSection = true;
    }

    public function closeAddProductsSec()
    {
        $this->reset(['addProductsSection', 'searchAddProducts', 'productsToAdd']);
    }

    public function showConfirmRemoveDriver(){
        $this->confirmRemoveDriver = true;
    }

    public function hideConfirmRemoveDriver(){
        $this->confirmRemoveDriver = false;
    }

    public function setDriver($id = null){
        
        if ($id) {
            Driver::findOrFail($id);
        }
        
        $res = $this->order->assignDriverToOrder($id);

        if ($res) {
            $this->closeSetDriverSection();
            $this->alertSuccess('Driver assigned');
        }else{
            $this->alertFailed();
        }
    }

    public function addProductRow($id)
    {
        $p = Product::findOrFail($id);
        $this->reset(['searchAddProducts']);
        $this->productsToAdd[] = ['product_id' => $id, 'quantity' => 1, 'price' => $p->price, 'name' => $p->name , 'combo_id' => null];
    }

    public function removeProductRow($index)
    {
        unset($this->productsToAdd[$index]);
        $this->productsToAdd = array_values($this->productsToAdd);
    }

    public function addProducts(){
        $res = $this->order->addProducts($this->productsToAdd);

        if ($res) {
            $this->closeAddProductsSec();
            $this->alertSuccess('Products added');
        }else{
            $this->alertFailed();
        }
    }

    public function openPayFromBalance()
    {
        $this->isOpenPayFromBalanceSec = true;
    }

    public function closePayFromBalance()
    {
        $this->isOpenPayFromBalanceSec = false;
    }

    public function PayFromBalance()
    {
        $this->authorize('pay', $this->order);
        $res = $this->order->setAsPaid(Carbon::now(), deductFromBalance: true);
        if ($res) {
            $this->mount($this->order->id);
            $this->closePayFromBalance();
            $this->alertSuccess('Order Payed');
        } else {
            $this->alertFailed();
        }
    }

    public $PAY_BY_PAYMENT_METHOD;

    public function confirmPayOrder($method){
        $this->PAY_BY_PAYMENT_METHOD = $method;
    }

    public function closeConfirmPayOrder(){
        $this->PAY_BY_PAYMENT_METHOD = null;
    }

    public function PayOrder()
    {
        $this->authorize('pay', $this->order);
        $res = $this->order->setAsPaid(Carbon::now(), paymentMethod: $this->PAY_BY_PAYMENT_METHOD, deductFromBalance: false);
        if ($res) {
            $this->mount($this->order->id);
            $this->PAY_BY_PAYMENT_METHOD = null;
            $this->alertSuccess('Order Payed');
        } else {
            $this->alertFailed();
        }
    }

    public function updatedCancelledProducts()
    {
        $this->cancelledProductsTotalAmount = 0;
        foreach ($this->cancelledProducts as $cancelledProducts) {
            $this->cancelledProductsTotalAmount += $cancelledProducts['return_quantity'] * $cancelledProducts['price'];
        }
        if ($this->isReturnShippingAmount) {
            $this->cancelledProductsTotalAmount += $this->order->delivery_amount;
        }
    }

    public function updatedIsReturnShippingAmount()
    {
        $this->updatedCancelledProducts();
    }

    public function openReturnsSection()
    {
        foreach ($this->order->products as $product) {
            $this->cancelledProducts[] = [
                'product_id' => $product->product_id,
                'name' => $product->product->name,
                'quantity' => $product->quantity,
                'price' => $product->price,
                'isReturnToStock' => true,
                'return_quantity' => 0,
            ];
        }

        $this->updatedCancelledProducts();

        $this->removeReasons = OrderRemovedProduct::removeReasons;

        $this->returnSection = true;
    }

    public function closeReturnsSection()
    {
        $this->reset(['returnSection', 'cancelledProducts']);
    }

    public function returnProducts()
    {
        $this->authorize('returnProducts', $this->order);

        $reason = null;
        if ($this->reason === 'Other' && $this->otherReason) {
            $reason = $this->otherReason;
        } elseif ($this->reason && $this->otherReason !== '') {
            $reason = $this->reason;
        } else {
            $reason = null;
        }

        $returnPaymentMethod = null;
        if ($this->returnPaymentMehod !== '' || $this->returnPaymentMehod !== null) {
            $returnPaymentMethod = $this->returnPaymentMehod;
        }

        $res = $this->order->cancelProducts($this->cancelledProducts, $reason, $returnPaymentMethod, $this->isReturnShippingAmount);

        if ($res) {
            $this->mount($this->order->id);
            $this->closeReturnsSection();
            $this->alertSuccess('Products returned');
        } else {
            $this->alertFailed();
        }
    }

    public function openUpdateDdate()
    {
        $this->ddateSection = true;
        $this->ddate = $this->order->delivery_date->toDateString();
    }

    public function closeUpdateDdate()
    {
        $this->reset(['ddateSection', 'ddate']);
    }

    public function updateDeliveryDate()
    {
        $this->authorize('update', $this->order);
        $this->validate([
            'ddate' => 'nullable|date',
        ]);
        $date = $this->ddate ? Carbon::parse($this->ddate) : null;
        $res = $this->order->updateDeliveryDate($date);

        if ($res) {
            $this->mount($this->order->id);
            $this->closeUpdateDdate();
            $this->alertSuccess('Delivery date updated');
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

    public function setStatus($status){

        $res = $this->order->setStatus($status);

        if ($res) {
            $this->mount($this->order->id);
            $this->alertSuccess('Status updated');
        } else {
            $this->alertFailed();
        }
    }

    public function mount($id)
    {
        $this->order = Order::findOrFail($id);
        $this->authorize('view', $this->order);
        $this->page_title = '• Orders • #' . $this->order->order_number;
        $this->zones = Zone::select('id', 'name')->get();
    }

    public function render()
    {
        $this->NextStatuses = Order::getNextStatuses($this->order->status);

        $products = Product::search($this->searchAddProducts)
            ->take(5)
            ->get();
        $PAYMENT_METHODS = CustomerPayment::PAYMENT_METHODS;

        $drivers = Driver::search($this->searchDrivers)->get();

        $this->comments = $this->order
            ->comments()
            ->latest()
            ->take($this->visibleCommentsCount)
            ->get();
        return view('livewire.orders.order-show', [
            'PAYMENT_METHODS' => $PAYMENT_METHODS,
            'products' => $products,
            'drivers' => $drivers,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'orders' => 'active']);
    }
}
