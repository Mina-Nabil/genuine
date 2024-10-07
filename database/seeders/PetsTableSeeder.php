<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pets\Pet;
use Faker\Factory as Faker;
use App\Models\Customers\Customer;

class PetsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 200; $i++) {
            Pet::create([
                'name' => $faker->name,
                'type' => $faker->randomElement(Pet::TYPES), // Randomly select a type (cat or dog)
                'bdate' => $faker->date(), // Random birthdate
                'customer_id' => Customer::inRandomOrder()->first()->id, // Assign a random existing customer
            ]);
        }
    }
}
