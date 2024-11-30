<?php

namespace Database\Seeders;

use App\Models\Products\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (App::isProduction())
            return; //added file import

        $categories = [
            ['name' => 'فراخ'],
            ['name' => 'لحوم'],
            ['name' => 'سمك'],
            ['name' => 'اضافات'],
            ['name' => 'قطط'],
            ['name' => 'مطهرات']
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
