<?php

namespace Database\Seeders;

use App\Models\Materials\RawMaterial;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RawMaterialsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the number of raw materials to create
        $rawMaterialCount = 50;

        for ($i = 1; $i <= $rawMaterialCount; $i++) {
            $name = 'Raw Material ' . $i;
            $desc = 'Description for ' . $name;
            $min_limit = rand(10, 100); // Random minimum limit
            $initial_quantity = rand(0, 500); // Random initial quantity

            RawMaterial::createRawMaterial($name, $min_limit, $desc, $initial_quantity);
        }
    }
}
