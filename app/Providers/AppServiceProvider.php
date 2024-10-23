<?php

namespace App\Providers;

use App\Models\Customers\Customer;
use App\Models\Products\Inventory;
use App\Models\Products\Product;
use App\Models\Users\AppLog;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            Product::MORPH_TYPE     => Product::class,
            Customer::MORPH_TYPE    => Customer::class, 
            Inventory::MORPH_TYPE   => Inventory::class, 
            AppLog::MORPH_TYPE   => AppLog::class, 
        ]);
    }
}
