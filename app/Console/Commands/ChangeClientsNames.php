<?php

namespace App\Console\Commands;

use App\Models\Customers\Customer;
use App\Models\Products\Product;
use App\Models\Users\User;
use Illuminate\Console\Command;
use Faker\Factory as Faker;
use GuzzleHttp\Client;

class ChangeClientsNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:change-data-names';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $faker = Faker::create();

        $clients = Customer::all();
        foreach ($clients as $c) {
            $c->name = $faker->name;
            $c->save();
        }
        $users = User::all();
        foreach ($users as $u) {
            $u->first_name = $faker->firstName;
            $u->last_name = $faker->lastName;
            $u->save();
        }
        $products = Product::all();
        foreach ($products as $p) {
            $p->name = $faker->word;
            $p->save();
        }
    }
}
