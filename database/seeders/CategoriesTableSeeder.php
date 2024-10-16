<?php

namespace Database\Seeders;

use App\Models\Products\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'فراخ'],
            ['name' => 'لحم'],
            ['name' => 'سمك'],
            ['name' => 'خضار'],
            ['name' => 'فاكهة'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
