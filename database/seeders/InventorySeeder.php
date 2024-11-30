<?php

namespace Database\Seeders;

use App\Models\Products\Inventory;
use App\Models\Products\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        if (App::isProduction()) return;
        
        // Get all products
        $products = Product::all();

        // Loop through each product and create a corresponding inventory record
        foreach ($products as $product) {
            $onhand = rand(1, 20);
            Inventory::initializeQuantity($product, $onhand);
        }

    }
}
