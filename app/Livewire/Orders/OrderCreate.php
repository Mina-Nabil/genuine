<?php

namespace App\Livewire\Orders;

use App\Models\Customers\Customer;
use App\Models\Customers\Zone;
use App\Models\Products\Combo;
use App\Models\Products\Product;
use App\Models\Users\Driver;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class OrderCreate extends Component
{
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
    public $dumySearchProduct;

    public $customerIsNew = false;
    public $customerId;
    public $customerName;
    public $shippingAddress;
    public $customerPhone;
    public $zoneId;

    public $isOpenSelectComboSec = false;
    public $combosSearchText;

    public function updatedDumySearchProduct()
    {
        $this->openProductsSection();
        $this->dumySearchProduct = null;
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
        $this->reset(['customerIsNew', 'customerId', 'customerName', 'shippingAddress', 'customerPhone', 'zoneId']);
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
        $this->customerName = $customer->name;
        $this->shippingAddress = $customer->address;
        $this->customerPhone = $customer->phone;
        $this->zoneId = $customer->zone?->id;
        $this->closeCustomerSection();
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
    }

    public function updateTotal($index)
    {
        $this->fetchedProducts[$index]['total'] = $this->fetchedProducts[$index]['quantity'] * $this->fetchedProducts[$index]['price'];
    }

    public function openProductsSection()
    {
        $this->isOpenSelectProductSec = true;
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
    }

    public function closeProductsSection()
    {
        $this->isOpenSelectProductSec = false;
        $this->productsSearchText = null;
    }

    public function render()
    {
        $products = Product::search($this->productsSearchText)
            ->limit(10)
            ->get();

        $drivers = Driver::search($this->driversSearchText)
            ->limit(10)
            ->get();

        $customers = Customer::search($this->driversSearchText)
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
