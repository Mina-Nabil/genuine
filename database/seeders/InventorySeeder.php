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
            $onhand = rand(50, 200);
            $committed = rand(5, 20);
            $available = $onhand - $committed;

            Inventory::create([
                'inventoryable_type' => get_class($product), // Morph type
                'inventoryable_id' => $product->id, // Morph ID
                'on_hand' => $onhand,  // Random value for demonstration
                'committed' => $committed,  // Random value for demonstration
                'available' => $available,  // Random value for demonstration
            ]);
        }

    }
}
