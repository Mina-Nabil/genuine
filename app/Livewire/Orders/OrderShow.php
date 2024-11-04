<?php

namespace App\Livewire\Orders;

use App\Models\Customers\Zone;
use App\Models\Orders\Order;
use App\Traits\AlertFrontEnd;
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

    //shipping details
    public $updateShippingSec = false;
    public $customerName;
    public $shippingAddress;
    public $customerPhone;
    public $zoneId;

    //note
    public $updateNoteSec = false;
    public $note;

    public $zones;

    public function openUpdateNote()
    {
        $this->updateNoteSec = true;
        $this->note = $this->order->note;
    }

    public function closeUpdateNote()
    {
        $this->reset(['updateNoteSec', 'note']);
    }

    public function updateNote(){
        $this->authorize('update', $this->order);
        $this->validate([
            'note' => 'nullable|string'
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
        $this->order = Order::findOrFail($id);
        $this->authorize('view', $this->order);
        $this->page_title = '• Orders • #' . $this->order->order_number;
        $this->zones = Zone::select('id', 'name')->get();
    }

    public function render()
    {
        $this->comments = $this->order
            ->comments()
            ->latest()
            ->take($this->visibleCommentsCount)
            ->get();
        return view('livewire.orders.order-show')->layout('layouts.app', ['page_title' => $this->page_title, 'orders' => 'active']);
    }
}
