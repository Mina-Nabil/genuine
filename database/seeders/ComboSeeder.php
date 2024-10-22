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
<<<<<<< HEAD
        // Example combos with just the name (prices are now stored in the combo_products table)
=======
        return; //added file import
        
        // Create some example combos with random prices
>>>>>>> 3d32f378e83d393ffa7a2438630745959b9757e2
        $combos = [
            ['name' => 'Family Meal'],
            ['name' => 'Lunch Combo'],
            ['name' => 'Weekend Special'],
        ];

        // Insert the combos into the 'combos' table
        foreach ($combos as $combo) {
            $createdCombo = Combo::create($combo); // Create a new combo using the Combo model

            // Retrieve all the products
            $products = Product::all();

            // Get a random set of products for this combo
            $selectedProducts = $products->random(rand(2, 5)); // Select 2-5 random products

            foreach ($selectedProducts as $product) {
                DB::table('combo_products')->insert([
                    'combo_id' => $createdCombo->id, // Use the created combo's ID
                    'product_id' => $product->id,
                    'quantity' => rand(1, 3), // Random quantity between 1 and 3
                    'price' => round(rand(100, 500), 2), // Random price between 100 and 500
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
