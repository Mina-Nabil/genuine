<?php

namespace Database\Seeders;

use App\Models\Materials\Supplier;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class SupplierSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            Supplier::newSupplier(
                $faker->company,
                $faker->phoneNumber,
                $faker->phoneNumber,
                $faker->email,
                $faker->address
            );
        }
    }
}
