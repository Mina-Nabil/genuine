<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pets\Pet;
use Faker\Factory as Faker;
use App\Models\Customers\Customer;
use Illuminate\Support\Facades\App;

class PetsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        if (App::isProduction()) return;

        $faker = Faker::create();

        // Define arrays of types for cats and dogs
        $catTypes = ['Siamese', 'Persian', 'Maine Coon', 'Sphynx'];
        $dogTypes = ['German Shepherd', 'Bulldog', 'Poodle', 'Labrador'];

        for ($i = 0; $i < 200; $i++) {
            // Randomly select a category (cat or dog)
            $category = $faker->randomElement(['cat', 'dog']);

            // Based on the category, select the type
            $type = $category === 'cat' 
                ? $faker->randomElement($catTypes) 
                : $faker->randomElement($dogTypes);

            // Create the pet with the appropriate type and category
            Pet::create([
                'name' => $faker->name,
                'category' => $category,
                'type' => $type,
                'bdate' => $faker->date(), // Random birthdate
                'customer_id' => Customer::inRandomOrder()->first()->id, // Assign a random existing customer
            ]);
        }
    }
}
