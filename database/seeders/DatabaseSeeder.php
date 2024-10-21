<?php

namespace Database\Seeders;

use App\Models\Users\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call(UserSeeder::class);
        $this->call(ZonesTableSeeder::class);
        $this->call(CustomersTableSeeder::class);
        $this->call(PetsTableSeeder::class);
        $this->call(TaskAllSeeder::class);
        $this->call(FollowupsTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);
        $this->call(ProductsTableSeeder::class);
        $this->call(ComboSeeder::class);
    }
}
