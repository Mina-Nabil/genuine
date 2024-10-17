<?php

namespace Database\Seeders;

use App\Models\Products\Inventory;
use App\Models\Products\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all products
        $products = Product::all();

        // Loop through each product and create a corresponding inventory record
        foreach ($products as $product) {
            Inventory::create([
                'product_id' => $product->id,
                'on_hand' => rand(50, 200),  // Random value for demonstration
                'committed' => rand(5, 20),  // Random value for demonstration
            ]);
        }

    }
}
