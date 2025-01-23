<?php

namespace Database\Seeders;

use App\Models\Materials\RawMaterial;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class RawMaterialSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 50; $i++) {
            RawMaterial::createRawMaterial(
                $faker->word,
                $faker->numberBetween(1, 100),
                $faker->sentence,
                $faker->numberBetween(0, 1000)
            );
        }
    }
}
