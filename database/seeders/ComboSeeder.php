<?php

namespace Database\Seeders;

use App\Models\Products\Product;
use App\Models\Products\Combo; // Import the Combo model
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComboSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some example combos with random prices
        $combos = [
            ['name' => 'Family Meal', 'price' => round(rand(1000, 1500), 2)],
            ['name' => 'Lunch Combo', 'price' => round(rand(500, 1000), 2)],
            ['name' => 'Weekend Special', 'price' => round(rand(800, 1200), 2)],
        ];

        // Insert the combos into the 'combos' table
        foreach ($combos as $combo) {
            $createdCombo = Combo::create($combo); // Use the Combo model to create a new combo

            // Retrieve all the products
            $products = Product::all();

            // Get a random set of products for this combo
            $selectedProducts = $products->random(rand(2, 5)); // Select 2-5 random products

            foreach ($selectedProducts as $product) {
                DB::table('combo_products')->insert([
                    'combo_id' => $createdCombo->id, // Use the created combo's ID
                    'product_id' => $product->id,
                    'quantity' => rand(1, 3), // Random quantity between 1 and 3
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
