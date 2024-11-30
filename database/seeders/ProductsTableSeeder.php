<?php

namespace Database\Seeders;

use App\Models\Products\Category;
use App\Models\Products\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\App;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        if (App::isProduction()) {
            Product::importData(resource_path('import/Genuine Data.xlsx'));
            return;
        }

        // Assuming the categories have already been seeded
        $categories = Category::all();

        // Initialize Faker
        $faker = Faker::create();

        // Sample products data
        $products = [
            ['name' => 'رز بالخلطه', 'desc' => $faker->sentence(10), 'price' => 30.00, 'weight' => 500, 'category_id' => $categories[0]->id],
            ['name' => 'كفتة', 'desc' => $faker->sentence(10), 'price' => 25.00, 'weight' => 300, 'category_id' => $categories[1]->id],
            ['name' => 'سمك مقلي', 'desc' => $faker->sentence(10), 'price' => 40.00, 'weight' => 600, 'category_id' => $categories[2]->id],
            ['name' => 'سلطة خضار', 'desc' => $faker->sentence(10), 'price' => 15.00, 'weight' => 200, 'category_id' => $categories[3]->id],
            ['name' => 'فاكهة مشكّلة', 'desc' => $faker->sentence(10), 'price' => 20.00, 'weight' => 300, 'category_id' => $categories[4]->id],
            ['name' => 'فراخ مشوية', 'desc' => $faker->sentence(10), 'price' => 50.00, 'weight' => 700, 'category_id' => $categories[0]->id],
            ['name' => 'لحم بقر', 'desc' => $faker->sentence(10), 'price' => 60.00, 'weight' => 800, 'category_id' => $categories[1]->id],
            ['name' => 'جمبري', 'desc' => $faker->sentence(10), 'price' => 100.00, 'weight' => 500, 'category_id' => $categories[2]->id],
            ['name' => 'فاصوليا خضراء', 'desc' => $faker->sentence(10), 'price' => 18.00, 'weight' => 400, 'category_id' => $categories[3]->id],
            ['name' => 'تفاح', 'desc' => $faker->sentence(10), 'price' => 10.00, 'weight' => 300, 'category_id' => $categories[4]->id],
            ['name' => 'بروكلي', 'desc' => $faker->sentence(10), 'price' => 12.00, 'weight' => 200, 'category_id' => $categories[3]->id],
            ['name' => 'بطيخ', 'desc' => $faker->sentence(10), 'price' => 25.00, 'weight' => 1500, 'category_id' => $categories[4]->id],
            ['name' => 'لحم ضأن', 'desc' => $faker->sentence(10), 'price' => 70.00, 'weight' => 700, 'category_id' => $categories[1]->id],
            ['name' => 'سمك مشوي', 'price' => 55.00, 'weight' => 600, 'category_id' => $categories[2]->id],
            ['name' => 'معكرونة', 'price' => 20.00, 'weight' => 250, 'category_id' => $categories[0]->id],
            ['name' => 'بيتزا', 'price' => 45.00, 'weight' => 400, 'category_id' => $categories[1]->id],
            ['name' => 'تشيز كيك', 'price' => 30.00, 'weight' => 250, 'category_id' => $categories[4]->id],
            ['name' => 'كبسة دجاج', 'price' => 55.00, 'weight' => 700, 'category_id' => $categories[0]->id],
            ['name' => 'دجاج محشي', 'price' => 65.00, 'weight' => 800, 'category_id' => $categories[0]->id],
            ['name' => 'فطائر خضار', 'price' => 20.00, 'weight' => 250, 'category_id' => $categories[3]->id],
            ['name' => 'لحم مقدد', 'price' => 75.00, 'weight' => 500, 'category_id' => $categories[1]->id],
            ['name' => 'كرات اللحم', 'price' => 35.00, 'weight' => 300, 'category_id' => $categories[1]->id],
            ['name' => 'سلطة فواكه', 'price' => 18.00, 'weight' => 200, 'category_id' => $categories[4]->id],
            ['name' => 'طاجن لحم', 'price' => 80.00, 'weight' => 600, 'category_id' => $categories[1]->id],
            ['name' => 'سندوتشات دجاج', 'price' => 25.00, 'weight' => 200, 'category_id' => $categories[0]->id],
            ['name' => 'برجر لحم', 'price' => 40.00, 'weight' => 300, 'category_id' => $categories[1]->id],
            ['name' => 'سموزي فواكه', 'price' => 15.00, 'weight' => 250, 'category_id' => $categories[4]->id],
            ['name' => 'حساء خضار', 'price' => 20.00, 'weight' => 300, 'category_id' => $categories[3]->id],
            ['name' => 'فطائر لحم', 'price' => 40.00, 'weight' => 250, 'category_id' => $categories[1]->id],
            ['name' => 'باستا دجاج', 'price' => 35.00, 'weight' => 300, 'category_id' => $categories[0]->id],
            ['name' => 'مقبلات مشوية', 'price' => 50.00, 'weight' => 400, 'category_id' => $categories[2]->id],
            ['name' => 'سلمون', 'price' => 90.00, 'weight' => 600, 'category_id' => $categories[2]->id],
            ['name' => 'دجاج بالفرن', 'price' => 45.00, 'weight' => 700, 'category_id' => $categories[0]->id],
            ['name' => 'خضار مشوية', 'price' => 22.00, 'weight' => 300, 'category_id' => $categories[3]->id],
            ['name' => 'شوربة لحم', 'price' => 25.00, 'weight' => 350, 'category_id' => $categories[1]->id],
            ['name' => 'شاورما دجاج', 'price' => 50.00, 'weight' => 400, 'category_id' => $categories[0]->id],
            ['name' => 'فتة', 'price' => 30.00, 'weight' => 500, 'category_id' => $categories[3]->id],
            ['name' => 'عصير برتقال', 'price' => 10.00, 'weight' => 250, 'category_id' => $categories[4]->id],
            ['name' => 'حلوى غربية', 'price' => 20.00, 'weight' => 300, 'category_id' => $categories[4]->id],
            ['name' => 'كعكة عيد ميلاد', 'price' => 80.00, 'weight' => 1000, 'category_id' => $categories[4]->id],
            ['name' => 'شوربة خضار', 'price' => 15.00, 'weight' => 300, 'category_id' => $categories[3]->id],
            ['name' => 'مكرونة بالصوص', 'price' => 30.00, 'weight' => 400, 'category_id' => $categories[0]->id],
            ['name' => 'صحن مشاوي', 'price' => 90.00, 'weight' => 800, 'category_id' => $categories[1]->id],
            ['name' => 'فواكه مجففة', 'price' => 40.00, 'weight' => 200, 'category_id' => $categories[4]->id],
            ['name' => 'دجاج باربيكيو', 'price' => 55.00, 'weight' => 600, 'category_id' => $categories[0]->id],
            ['name' => 'سمك مملح', 'price' => 35.00, 'weight' => 400, 'category_id' => $categories[2]->id],
            ['name' => 'مقبلات دجاج', 'price' => 30.00, 'weight' => 300, 'category_id' => $categories[0]->id],
            ['name' => 'فطائر جزر', 'price' => 25.00, 'weight' => 250, 'category_id' => $categories[3]->id],
            ['name' => 'سناك لحم', 'price' => 45.00, 'weight' => 350, 'category_id' => $categories[1]->id],
            ['name' => 'كعكة الشوكولاتة', 'price' => 55.00, 'weight' => 400, 'category_id' => $categories[4]->id],
            ['name' => 'حساء دجاج', 'price' => 22.00, 'weight' => 300, 'category_id' => $categories[0]->id],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
