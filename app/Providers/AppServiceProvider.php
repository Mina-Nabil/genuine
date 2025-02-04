<?php

namespace App\Providers;

use App\Models\Accounting\Account;
use App\Models\Accounting\EntryTitle;
use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\MainAccount;
use App\Models\Accounting\UnapprovedEntry;
use App\Models\Customers\Customer;
use App\Models\Customers\Zone;
use App\Models\Materials\RawMaterial;
use App\Models\Materials\Supplier;
use App\Models\Materials\SupplierInvoice;
use App\Models\Orders\Order;
use App\Models\Orders\PeriodicOrder;
use App\Models\Products\Inventory;
use App\Models\Products\Product;
use App\Models\Tasks\TaskWatcher;
use App\Models\Users\AppLog;
use App\Models\Users\Driver;
use App\Models\Users\User;
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
            SupplierInvoice::MORPH_TYPE     => SupplierInvoice::class,
            Product::MORPH_TYPE     => Product::class,
            Customer::MORPH_TYPE    => Customer::class, 
            Supplier::MORPH_TYPE    => Supplier::class, 
            RawMaterial::MORPH_TYPE    => RawMaterial::class, 
            Inventory::MORPH_TYPE   => Inventory::class, 
            AppLog::MORPH_TYPE   => AppLog::class, 
            Order::MORPH_TYPE   => Order::class, 
            PeriodicOrder::MORPH_TYPE   => PeriodicOrder::class, 
            Zone::MORPH_TYPE => Zone::class,
            Driver::MORPH_TYPE => Driver::class,
            TaskWatcher::MORPH_TYPE => TaskWatcher::class,
            User::MORPH_TYPE => User::class,
            JournalEntry::MORPH_TYPE => JournalEntry::class,
            MainAccount::MORPH_TYPE => MainAccount::class,
            Account::MORPH_TYPE => Account::class,
            UnapprovedEntry::MORPH_TYPE => UnapprovedEntry::class,
            EntryTitle::MORPH_TYPE => EntryTitle::class,
        ]);
    }
}
