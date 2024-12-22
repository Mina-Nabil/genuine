<?php

namespace App\Livewire\Orders;

use App\Models\Customers\Customer;
use App\Models\Customers\Zone;
use App\Models\Orders\Order;
use App\Models\Products\Combo;
use App\Models\Products\Product;
use App\Models\Users\Driver;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderCreate extends Component
{
    use AlertFrontEnd;
    public $page_title = '• Orders • New';

    //select products sections
    public $isOpenSelectProductSec = false;
    public $productsSearchText;
    public $filterType;
    public $selectedProducts = [];
    public $fetchedProducts = [];
    public $ddate;
    public $note;
    public $driver;
    public $isOpenSelectDriverSec;
    public $driversSearchText;
    public $customersSearchText;
    public $isOpenSelectCustomerSec;
    public $dummyProductsSearch;

    public $paymentMethod;
    public $periodicOption;

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

    public $hasPrevOrdersAlert = false;

    //payments
    public $subtotal;
    public $totalItems;
    public $shippingFee;
    public $zoneName;
    public $total;
    public $discountAmount = 0;

    //last order
    public $customerLastOrder = false;

    public $fetchedCombos = [];

    public function updatedFetchedProducts()
    {
        Log::info($this->fetchedProducts);
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

    public function selectDriver($id)
    {
        $this->driver = Driver::findOrFail($id);
        $this->closeDriverSection();
    }

    public function clearDriver()
    {
        $this->reset(['driver']);
    }

    public function clearCustomer()
    {
        $this->reset(['customerIsNew', 'customerId', 'customerName', 'shippingAddress', 'locationURL', 'customerPhone', 'zoneId', 'detuctFromBalance', 'customerLastOrder']);
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

    public function updatedDdate()
    {
        if ($this->customerId) {
            $customer = Customer::findOrFail($this->customerId);
            if ($this->ddate && !$customer->orders->where('delivery_date', Carbon::parse($this->ddate))->isEmpty()) {
                $this->hasPrevOrdersAlert = true;
            } else {
                $this->hasPrevOrdersAlert = false;
            }
        }
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

        if ($this->ddate && !$customer->orders->where('delivery_date', Carbon::parse($this->ddate))->isEmpty()) {
            $this->hasPrevOrdersAlert = true;
        } else {
            $this->hasPrevOrdersAlert = false;
        }

        $latestOrder = $customer->orders()->latest()->first()?->id;

        if ($latestOrder) {
            $this->customerLastOrder = $latestOrder;
        }

        if ($customer->balance > 0) {
            $this->detuctFromBalance = true;
        } else {
            $this->detuctFromBalance = false;
        }

        $this->closeCustomerSection();
        $this->refreshPayments();
    }

    public function reorderLastOrder()
    {
        $this->fetchedProducts = [];
        $lastOrder = Order::find($this->customerLastOrder);

        $this->customerName = $lastOrder->customer_name;
        $this->shippingAddress = $lastOrder->shipping_address;
        $this->locationURL = $lastOrder->location_url;
        $this->customerPhone = $lastOrder->customer_phone;
        $this->zoneId = $lastOrder->zone_id;
        $this->discountAmount = $lastOrder->discount_amount;

        if ($lastOrder->driver_id) {
            $this->selectDriver($lastOrder->driver_id);
        }

        foreach ($lastOrder->products as $product) {
            // dd($product->product_id);
            $origProd = Product::find($product->product_id);
            if ($origProd)
                $this->fetchedProducts[] = [
                    'id' => $product->product_id,
                    'name' => $origProd->name,
                    'combo_id' => $product->combo_id,
                    'quantity' => $product->quantity,
                    'price' => $product->price,
                ];
        }

        $this->refreshPayments();
    }

    public function openDriverSection()
    {
        $this->isOpenSelectDriverSec = true;
    }

    public function closeDriverSection()
    {
        $this->isOpenSelectDriverSec = false;
        $this->driversSearchText = null;
    }

    public function updatedFetchedCombos()
    {
        foreach ($this->fetchedCombos as $combo) {
            // Fetch the combo from the database with its related products and pivot
            $c = Combo::with('products')->find($combo['combo_id']);

            if ($c) {
                // Loop through fetchedProducts to find matching combo_id
                foreach ($this->fetchedProducts as &$product) {
                    if ($product['combo_id'] === $combo['combo_id']) {
                        // Find the product in the combo's products relationship
                        $pivot = $c->products->where('id', $product['id'])->first();

                        if ($pivot) {
                            // Update product quantity using combo_quantity and pivot quantity
                            $product['quantity'] = (is_numeric($combo['combo_quantity']) ? $combo['combo_quantity'] : 0) * $pivot->pivot->quantity;
                        }
                    }
                }
            }
        }
        $this->refreshPayments();
    }

    public function selectCombo($id)
    {
        // Fetch the combo and its associated products
        $combo = Combo::findOrFail($id);

        foreach ($combo->products as $product) {
            // Check if the product already exists in fetchedProducts by its ID
            $existingIndex = collect($this->fetchedProducts)->search(function ($fetchedProduct) use ($product, $combo) {
                return ($fetchedProduct['id'] == $product->id) && ($fetchedProduct['combo_id'] == $combo->id);
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
                'combo_name' => $combo->name, // Combo association
            ];

            // Ensure the product ID is also in selectedProducts
            // if (!in_array($product->id, $this->selectedProducts)) {
            //     $this->selectedProducts[] = $product->id;
            // }
        }

        foreach ($this->fetchedProducts as $product) {
            $existingComboKey = array_search($product['combo_id'], array_column($this->fetchedCombos, 'combo_id'));
        }

        if ($existingComboKey === false) {
            $this->fetchedCombos[] = [
                'combo_id' => $product['combo_id'],
                'combo_name' => $product['combo_name'],
                'combo_quantity' => 1,
            ];
        } else {
            // Increment the quantity of the existing combo
            $this->fetchedCombos[$existingComboKey]['combo_quantity']++;
            $this->updatedFetchedCombos();
        }

        $this->fetchedProducts = array_values($this->fetchedProducts);

        $this->closeCombosSection();
        $this->refreshPayments();
    }

    public function addProducts()
    {
        foreach ($this->selectedProducts as $productId) {
            // If the product is not in a combo, check if it exists individually
            $existsIndividually = false;

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
        $this->validate(
            [
                'fetchedProducts.*.quantity' => 'required|integer|min:1',
                'fetchedProducts.*.price' => 'required|numeric|min:0',
            ],
            [
                'fetchedProducts.*.quantity' => 'Each order item must have a valid quantity',
                'fetchedProducts.*.price' => 'Each order item must have a price',
            ],
        );
        if (array_key_exists($index, $this->fetchedProducts))
            $this->fetchedProducts[$index]['total'] = $this->fetchedProducts[$index]['quantity'] * $this->fetchedProducts[$index]['price'];
    }

    public function openProductsSection()
    {
        $this->isOpenSelectProductSec = true;
        $this->dummyProductsSearch = null;
    }

    public function refreshPayments()
    {
        $this->validate(
            [
                'fetchedProducts.*.id' => 'required|exists:products,id',
                'fetchedProducts.*.combo_id' => 'nullable|exists:combos,id',
                'fetchedProducts.*.quantity' => 'required|integer|min:1',
                'fetchedProducts.*.price' => 'required|numeric|min:0',
            ],
            [
                'fetchedProducts.*.id' => 'Each order item is required',
                'fetchedProducts.*.quantity' => 'Each order item must have a valid quantity',
                'fetchedProducts.*.price' => 'Each order item must have a price',
            ],
        );

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

    public function removeProduct($fetchedProductIndex)
    {

        $prod = $this->fetchedProducts[$fetchedProductIndex];
        unset($this->fetchedProducts[$fetchedProductIndex]);

        // //////////Removing Item by item no need to check for combo 
        // // Check if the product has a combo_id
        // if ($productToRemove && isset($productToRemove['combo_id'])) {
        //     $comboId = $productToRemove['combo_id'];

        //     // Remove all products with the same combo_id from fetchedProducts
        //     $this->fetchedProducts = array_filter($this->fetchedProducts, function ($product) use ($comboId) {
        //         return !isset($product['combo_id']) || $product['combo_id'] !== $comboId; // Keep products not matching the combo_id
        //     });
        // } else {
        //     $this->fetchedProducts = array_filter($this->fetchedProducts, function ($product) use ($productId) {
        //         return isset($product['id']) && $product['id'] != $productId; // Retain products not matching the ID
        //     });
        // }

        // Remove the product ID from selectedProducts
        $this->selectedProducts = array_filter($this->selectedProducts, function ($id) use ($prod) {
            return $id != $prod['id']; // Retain IDs not matching the product ID
        });

        // Re-index the selectedProducts array to maintain sequential numeric keys
        $this->selectedProducts = array_values($this->selectedProducts);
        $this->fetchedProducts = array_values($this->fetchedProducts);

        $this->fetchedCombos = array_filter($this->fetchedCombos, function ($comboArr) {
            foreach ($this->fetchedProducts as $prod) {
                if ($prod['combo_id'] == $comboArr['combo_id']) return true;
            }
            return false;
        });

        $this->refreshPayments();
    }

    public function closeProductsSection()
    {
        $this->isOpenSelectProductSec = false;
        $this->productsSearchText = null;
    }

    public function createOrder()
    {
        $hasErrors = null;

        if (Carbon::parse($this->ddate)->isToday()) {
            foreach ($this->fetchedProducts as $index => $product) {
                $p = Product::findOrFail($product['id']);

                if ($p->inventory->available - $product['quantity'] < 0) {
                    $this->addError("fetchedProducts.$index.quantity", "Quantity exceeds available stock: {$p->inventory->available}");
                    $hasErrors = true;
                }
            }

            if ($hasErrors) {
                return;
            }
        }

        $detuctFromBalance = false;
        if ($this->customerId) {
            $this->validate(
                [
                    'customerId' => 'required|exists:customers,id',
                    'customerName' => 'required|string|max:255',
                    'shippingAddress' => 'required|string|max:255',
                    'locationURL' => 'nullable|string|max:255',
                    'customerPhone' => 'required|string',
                    'zoneId' => 'required|exists:zones,id',
                ],
                attributes: [
                    'customerId' => 'customer',
                    'zoneId' => 'zone',
                ],
            );
            $customerId = $this->customerId;
            $detuctFromBalance = $this->detuctFromBalance;
        } else {
            $this->validate(
                [
                    'customerName' => 'required|string|max:255',
                    'shippingAddress' => 'required|string|max:255',
                    'locationURL' => 'required|string|max:255',
                    'customerPhone' => 'required|string|max:15',
                    'zoneId' => 'required|exists:zones,id',
                ],
                attributes: [
                    'zoneId' => 'zone',
                ],
            );
            $res = Customer::newCustomer($this->customerName, $this->shippingAddress, $this->customerPhone, location_url: $this->locationURL, zone_id: $this->zoneId);
            $customerId = $res->id;
        }

        $this->validate(
            [
                'total' => 'nullable|numeric|min:0',
                'shippingFee' => 'nullable|numeric|min:0',
                'discountAmount' => 'nullable|numeric|min:0',
                'ddate' => 'required|date',
                'note' => 'nullable|string|max:500',
                'fetchedProducts.*.id' => 'required|exists:products,id',
                'fetchedProducts.*.combo_id' => 'nullable|exists:combos,id',
                'fetchedProducts.*.quantity' => 'required|integer|min:1',
                'fetchedProducts.*.price' => 'required|numeric|min:0',
            ],
            [
                'fetchedProducts.*.id' => 'Each order item is required',
                'fetchedProducts.*.quantity' => 'Each order item must have a valid quantity',
                'fetchedProducts.*.price' => 'Each order item must have a price',
            ],
        );

        $driverID = null;
        $this->driver ? ($driverID = $this->driver->id) : null;

        $res = Order::newOrder($customerId, $this->customerName, $this->shippingAddress, $this->customerPhone, $this->zoneId, $this->locationURL, $driverID, $this->total, $this->shippingFee, $this->discountAmount, $this->ddate ? Carbon::parse($this->ddate) : null, $this->note, $this->fetchedProducts, $detuctFromBalance);

        if ($res) {
            $this->alertSuccess('order added!');
            sleep(2);
            return redirect(route('orders.create'));
        } else {
            $this->alertFailed();
        }
    }

    public function mount(Request $request)
    {
        $this->customerLastOrder = $request->query('order_id');
        if ($this->customerLastOrder) {
            $order = Order::findOrFail($this->customerLastOrder);
            $this->selectCustomer($order->customer_id);
            $this->reorderLastOrder();
        }
    }

    public function render()
    {
        $products = Product::search($this->productsSearchText)
            ->limit(20)
            ->get();

        $drivers = Driver::search($this->driversSearchText)
            ->limit(10)
            ->get();

        $customers = Customer::search($this->customersSearchText)
            ->limit(10)
            ->get();

        $zones = Zone::all();

        $combos = Combo::search($this->combosSearchText)
            ->limit(10)
            ->get();

        return view('livewire.orders.order-create', [
            'products' => $products,
            'drivers' => $drivers,
            'customers' => $customers,
            'zones' => $zones,
            'combos' => $combos,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'orders' => 'active']);
    }
}
