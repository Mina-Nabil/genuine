<?php

namespace Database\Seeders;

use App\Models\Customers\Zone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZonesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Define an array of cities and their average delivery rates
        $zones = [
            ['name' => 'Cairo', 'delivery_rate' => 60],
            ['name' => 'Alexandria', 'delivery_rate' => 50],
            ['name' => 'Giza', 'delivery_rate' => 55],
            ['name' => 'Sharm El Sheikh', 'delivery_rate' => 70],
            ['name' => 'Hurghada', 'delivery_rate' => 65],
            ['name' => 'Mansoura', 'delivery_rate' => 50],
            ['name' => 'Tanta', 'delivery_rate' => 52],
            ['name' => 'Port Said', 'delivery_rate' => 58],
            ['name' => 'Suez', 'delivery_rate' => 60],
            ['name' => 'Aswan', 'delivery_rate' => 75],
            ['name' => 'Luxor', 'delivery_rate' => 80],
            ['name' => 'Ismailia', 'delivery_rate' => 55],
            ['name' => 'Faiyum', 'delivery_rate' => 53],
            ['name' => 'Damanhur', 'delivery_rate' => 54],
            ['name' => 'Zagazig', 'delivery_rate' => 57],
            ['name' => 'Beni Suef', 'delivery_rate' => 56],
            ['name' => 'Menoufia', 'delivery_rate' => 59],
            ['name' => 'Kafr El Sheikh', 'delivery_rate' => 52],
            ['name' => 'Qena', 'delivery_rate' => 78],
            ['name' => 'Matrouh', 'delivery_rate' => 72],
        ];

        // Insert data into the zones table
        foreach ($zones as $zone) {
            Zone::create($zone);
        }
    }
}
