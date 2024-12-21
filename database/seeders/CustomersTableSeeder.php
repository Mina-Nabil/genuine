<?php

namespace Database\Seeders;

use App\Models\Customers\Customer;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Customer::importData(resource_path('import/Genuine Data.xlsx'));
        return;

        $faker = Faker::create();

        for ($i = 0; $i < 100; $i++) {
            Customer::create([
                'name' => $faker->name,
                'address' => $faker->address,
                'phone' => $faker->phoneNumber,
                'location_url' => $faker->url,
                'zone_id' => rand(1, 20), // Assuming you have 20 zones
            ]);
        }
    }
}
