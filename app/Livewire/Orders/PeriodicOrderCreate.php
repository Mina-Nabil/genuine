<?php

namespace App\Livewire\Orders;

use App\Models\Customers\Customer;
use App\Models\Customers\Zone;
use App\Models\Orders\Order;
use App\Models\Orders\PeriodicOrder;
use App\Models\Products\Combo;
use App\Models\Products\Product;
use App\Models\Users\Driver;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class PeriodicOrderCreate extends Component
{
    use AlertFrontEnd;
    public $page_title = '• Orders • New';

    //select products sections
    public $isOpenSelectProductSec = false;
    public $productsSearchText;
    public $filterType;
    public $selectedProducts = [];
    public $fetchedProducts = [];
    public $note;
    public $customersSearchText;
    public $isOpenSelectCustomerSec;
    public $dummyProductsSearch;

    public $paymentMethod;
    public $periodicOption = PeriodicOrder::PERIODIC_WEEKLY;
    public $orderDay =  1;
    public $orderName;

    public $customerIsNew = false;
    public $customerId;
    public $customerBalance;
    public $detuctFromBalance;
    public $customerName;
    public $shippingAddress;
    public $locationURL;
    public $customerPhone;
    public $zoneId;
    public $initiateDiscountAmount;

    public $isOpenSelectComboSec = false;
    public $combosSearchText;

    public $isOpenEditDiscount = false;

    //payments
    public $subtotal;
    public $totalItems;
    public $shippingFee;
    public $zoneName;
    public $total;
    public $discountAmount = 0;

    public function updatedFetchedProducts(){
        $this->refreshPayments();
    }

    public function closeDiscountSection()
    {
        $this->isOpenEditDiscount = false;
        $this->initiateDiscountAmount = null;
    }

    public function openDiscountSection()
    {
        $this->isOpenEditDiscount = true;
        $this->initiateDiscountAmount = $this->discountAmount;
    }

    public function updateDiscount()
    {
        $this->validate([
            'initiateDiscountAmount' => 'required|numeric|min:0',
        ]);

        $this->discountAmount = $this->initiateDiscountAmount;
        $this->closeDiscountSection();
        $this->refreshPayments();
    }

    public function updatedZoneId()
    {
        $this->refreshPayments();
    }

    public function updatedDummyProductsSearch()
    {
        $this->openProductsSection();
        $this->dummyProductsSearch = null;
    }

    public function closeCombosSection()
    {
        $this->isOpenSelectComboSec = false;
        $this->combosSearchText = null;
    }

    public function openCombosSection()
    {
        $this->isOpenSelectComboSec = true;
    }

    public function NewCustomerSection()
    {
        $this->customerIsNew = true;
    }

    public function clearCustomer()
    {
        $this->reset(['customerIsNew', 'customerId', 'customerName', 'shippingAddress','locationURL', 'customerPhone', 'zoneId','detuctFromBalance']);
        $this->refreshPayments();
    }

    public function openCustomerSection()
    {
        $this->isOpenSelectCustomerSec = true;
    }

    public function closeCustomerSection()
    {
        $this->isOpenSelectCustomerSec = false;
        $this->customersSearchText = null;
    }

    public function selectCustomer($id)
    {
        $customer = Customer::findOrFail($id);
        $this->customerId = $customer->id;
        $this->customerBalance = $customer->balance;
        $this->customerName = $customer->name;
        $this->shippingAddress = $customer->address;
        $this->locationURL = $customer->location_url;
        $this->customerPhone = $customer->phone;
        $this->zoneId = $customer->zone?->id;

        if ($customer->balance > 0) {
            $this->detuctFromBalance = true;
        }else{
            $this->detuctFromBalance = false;
        }

        $this->closeCustomerSection();
        $this->refreshPayments();

        
    }

    public function selectCombo($id)
    {
        // Fetch the combo and its associated products
        $combo = Combo::findOrFail($id);

        foreach ($combo->products as $product) {
            // Check if the product already exists in fetchedProducts by its ID
            $existingIndex = collect($this->fetchedProducts)->search(function ($fetchedProduct) use ($product) {
                return $fetchedProduct['id'] == $product->id;
            });

            if ($existingIndex !== false) {
                // Remove the existing product added individually
                unset($this->fetchedProducts[$existingIndex]);
            }

            // Add the product with combo_id to fetchedProducts
            $this->fetchedProducts[] = [
                'id' => $product->id,
                'name' => $product->name,
                'combo_id' => $id, // Combo association
                'quantity' => $product->pivot->quantity, // Default quantity
                'price' => $product->pivot->price, // Price from the pivot
            ];

            // Ensure the product ID is also in selectedProducts
            if (!in_array($product->id, $this->selectedProducts)) {
                $this->selectedProducts[] = $product->id;
            }
        }

        // Re-index fetchedProducts array
        $this->fetchedProducts = array_values($this->fetchedProducts);

        $this->closeCombosSection();
        $this->refreshPayments();
    }

    public function addProducts()
    {
        foreach ($this->selectedProducts as $productId) {
            // If the product is not in a combo, check if it exists individually
            $existsIndividually = collect($this->fetchedProducts)->contains('id', $productId);

            if (!$existsIndividually) {
                $product = Product::findOrFail($productId);
                $this->fetchedProducts[] = [
                    'id' => $productId,
                    'name' => $product->name,
                    'combo_id' => null, // No combo association
                    'quantity' => 1, // Default quantity
                    'price' => $product->price, // Default price
                ];
            }
        }

        // Remove products from fetchedProducts that are not in selectedProducts
        // except those that have a combo_id association
        $this->fetchedProducts = collect($this->fetchedProducts)
            ->filter(function ($product) {
                return in_array($product['id'], $this->selectedProducts) || $product['combo_id'] !== null;
            })
            ->values()
            ->toArray();

        $this->closeProductsSection();
        $this->refreshPayments();
    }

    public function updateTotal($index)
    {
        $this->validate([
            'fetchedProducts.*.quantity' => 'required|integer|min:1',
            'fetchedProducts.*.price' => 'required|numeric|min:0',
        ], [
            'fetchedProducts.*.quantity' => 'Each order item must have a valid quantity',
            'fetchedProducts.*.price' => 'Each order item must have a price',   
        ]);
        $this->fetchedProducts[$index]['total'] = $this->fetchedProducts[$index]['quantity'] * $this->fetchedProducts[$index]['price'];
    }

    public function openProductsSection()
    {
        $this->isOpenSelectProductSec = true;
        $this->dummyProductsSearch = null;
    }

    public function refreshPayments()
    {
        $this->validate([
            'fetchedProducts.*.id' => 'required|exists:products,id',
            'fetchedProducts.*.combo_id' => 'nullable|exists:combos,id',
            'fetchedProducts.*.quantity' => 'required|integer|min:1',
            'fetchedProducts.*.price' => 'required|numeric|min:0',
        ], [
            'fetchedProducts.*.id' => 'Each order item is required',
            'fetchedProducts.*.quantity' => 'Each order item must have a valid quantity',
            'fetchedProducts.*.price' => 'Each order item must have a price',   
        ]);

        $subtotal = 0;
        $totalItems = 0;
        foreach ($this->fetchedProducts as $prod) {
            $subtotal = $subtotal + $prod['quantity'] * $prod['price'];
            $totalItems = $totalItems + $prod['quantity'];
        }
        $shippingFee = 0;
        if ($this->zoneId) {
            $zone = Zone::findOrFail($this->zoneId);
            $shippingFee = $zone->delivery_rate;
            $this->zoneName = $zone->name;
        }

        $this->subtotal = $subtotal;
        $this->totalItems = $totalItems;
        $this->shippingFee = $shippingFee;
        $this->total = $subtotal + $shippingFee - $this->discountAmount;
    }

    public function removeProduct($productId)
    {
        // Find the product being removed
        $productToRemove = collect($this->fetchedProducts)->firstWhere('id', $productId);

        // Check if the product has a combo_id
        if ($productToRemove && isset($productToRemove['combo_id'])) {
            $comboId = $productToRemove['combo_id'];

            // Remove all products with the same combo_id from fetchedProducts
            $this->fetchedProducts = array_filter($this->fetchedProducts, function ($product) use ($comboId) {
                return !isset($product['combo_id']) || $product['combo_id'] !== $comboId; // Keep products not matching the combo_id
            });
        } else {
            // If no combo_id, just remove the single product
            $this->fetchedProducts = array_filter($this->fetchedProducts, function ($product) use ($productId) {
                return isset($product['id']) && $product['id'] != $productId; // Retain products not matching the ID
            });
        }

        // Remove the product ID from selectedProducts
        $this->selectedProducts = array_filter($this->selectedProducts, function ($id) use ($productId) {
            return $id != $productId; // Retain IDs not matching the product ID
        });

        // Re-index the selectedProducts array to maintain sequential numeric keys
        $this->selectedProducts = array_values($this->selectedProducts);
        $this->refreshPayments();
    }

    public function closeProductsSection()
    {
        $this->isOpenSelectProductSec = false;
        $this->productsSearchText = null;
    }

    public function createOrder()
    {
        if ($this->customerId) {
            $this->validate([
                'customerId' => 'required|exists:customers,id',
                'customerName' => 'required|string|max:255',
                'shippingAddress' => 'required|string|max:255',
                'locationURL' => 'required|string|max:255',
                'customerPhone' => 'required|string|max:15',
                'zoneId' => 'required|exists:zones,id',
            ],attributes:[
                'customerId' => 'customer',
                'zoneId' => 'zone',
            ]);
            $customerId = $this->customerId;
        } else {
            $this->validate([
                
                'customerName' => 'required|string|max:255',
                'shippingAddress' => 'required|string|max:255',
                'locationURL' => 'required|string|max:255',
                'customerPhone' => 'required|string|max:15',
                'zoneId' => 'required|exists:zones,id',
            ],attributes:[
                'zoneId' => 'zone',
            ]);
            $res = Customer::newCustomer($this->customerName, $this->shippingAddress, $this->customerPhone,location_url:$this->locationURL, zone_id: $this->zoneId);
            $customerId = $res->id;
        }

        $this->validate([
            'orderName' => 'nullable|string|max:500',
            'note' => 'nullable|string|max:500',
            'periodicOption' => "required|in:" . implode(',', PeriodicOrder::PERIODIC_OPTIONS),
            'fetchedProducts.*.id' => 'required|exists:products,id',
            'fetchedProducts.*.combo_id' => 'nullable|exists:combos,id',
            'fetchedProducts.*.quantity' => 'required|integer|min:1',
            'fetchedProducts.*.price' => 'required|numeric|min:0',
        ], [
            'fetchedProducts.*.id' => 'Each order item is required',
            'fetchedProducts.*.quantity' => 'Each order item must have a valid quantity',
            'fetchedProducts.*.price' => 'Each order item must have a price',   
        ]);


        $res = PeriodicOrder::newPeriodicOrder(
            $customerId,
            $this->customerName,
            $this->shippingAddress,
            $this->customerPhone,
            $this->zoneId,
            $this->locationURL,
            $this->periodicOption,
            $this->orderName,
            $this->orderDay,
            $this->note,
            true,
            $this->fetchedProducts);

        if ($res) {
            $this->alertSuccess('order added!');
            sleep(2);
            return redirect(route('orders.periodic.create'));
        } else {
            $this->alertFailed();
        }
    }

    public function render()
    {
        $products = Product::search($this->productsSearchText)
            ->limit(10)
            ->get();

        $customers = Customer::search($this->customersSearchText)
            ->limit(10)
            ->get();

        $zones = Zone::all();

        $combos = Combo::search($this->combosSearchText)
            ->limit(10)
            ->get();

        $PERIODIC_OPTIONS = PeriodicOrder::PERIODIC_OPTIONS;
        $daysofweek = PeriodicOrder::daysOfWeek;

        return view('livewire.orders.periodic-order-create',[
            'products' => $products,
            'customers' => $customers,
            'zones' => $zones,
            'combos' => $combos,
            'PERIODIC_OPTIONS' => $PERIODIC_OPTIONS,
            'daysofweek' => $daysofweek
        ]);
    }
}
