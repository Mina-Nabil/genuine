<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Livewire\Users\Calendar;
use App\Livewire\Customers\CustomerIndex;
use App\Livewire\Customers\CustomerShow;
use App\Livewire\Customers\FollowupIndex;
use App\Livewire\Customers\ZoneIndex;
use App\Livewire\Orders\CancelledOrderIndex;
use App\Livewire\Orders\ClosedOrderIndex;
use App\Livewire\Orders\FollowupReport;
use App\Livewire\Orders\OrderCreate;
use App\Livewire\Orders\OrderDriverShift;
use App\Livewire\Orders\OrderIndex;
use App\Livewire\Orders\OrderInventory;
use App\Livewire\Orders\OrderShow;
use App\Livewire\Orders\PastDueOrderIndex;
use App\Livewire\Orders\PeriodicOrderCreate;
use App\Livewire\Orders\PeriodicOrderIndex;
use App\Livewire\Orders\PeriodicOrderShow;
use App\Livewire\Pets\PetIndex;
use App\Livewire\Products\ComboIndex;
use App\Livewire\Products\ComboShow;
use App\Livewire\Products\InventoryTransactionIndex;
use App\Livewire\Products\ProductIndex;
use App\Livewire\Products\ProductShow;
use App\Livewire\Products\TransactionIndex;
use App\Livewire\Reports\CustomerReport;
use App\Livewire\Reports\CustomerTransactionReport;
use App\Livewire\Reports\DailyLoadingReport;
use App\Livewire\Reports\OrderReport;
use App\Livewire\Reports\ProductionPlanning;
use App\Livewire\Tasks\TaskIndex;
use App\Livewire\Tasks\TaskShow;
use App\Livewire\Users\NotificationIndex;
use App\Livewire\Users\Profile;
use App\Livewire\Users\UserIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('customer.index');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/orders/driver', OrderDriverShift::class)->name('orders.driver.shift');
});

Route::middleware(['auth', 'no_driver'])->group(function () {

    Route::get('/profile/{id}', Profile::class)->name('profile');
    Route::get('/users', UserIndex::class);

    Route::get('/customers', CustomerIndex::class)->name('customer.index');
    Route::get('/customers/{id}', CustomerShow::class)->name('customer.show');

    Route::get('/products', ProductIndex::class)->name('product.index');
    Route::get('/products/{id}', ProductShow::class)->name('product.show');

    Route::get('/inventories', InventoryTransactionIndex::class)->name('inventories.index');
    Route::get('/transactions', TransactionIndex::class)->name('transactions.index');

    Route::get('/combos', ComboIndex::class)->name('combo.index');
    Route::get('/combos/{id}', ComboShow::class)->name('combo.show');

    Route::get('/pets', PetIndex::class);
    Route::get('/zones', ZoneIndex::class);
    Route::get('/followups', FollowupIndex::class);

    Route::get('/calendar', Calendar::class);

    Route::get('/tasks', TaskIndex::class);
    Route::get('/tasks/{id}', TaskShow::class)->name('tasks.show');
    Route::get('/notifications', NotificationIndex::class);

    Route::post('/notifications/seen/{id}', [UserController::class, 'setNotfAsSeen']);

    Route::get('/orders/new', OrderCreate::class)->name('orders.create');
    Route::get('/orders/periodic/new', PeriodicOrderCreate::class)->name('orders.periodic.create');
    Route::get('/orders/periodic/{id}', PeriodicOrderShow::class)->name('orders.periodic.show');

    Route::get('/orders', OrderIndex::class)->name('orders.index');
    Route::get('/orders/pastdue', PastDueOrderIndex::class)->name('orders.past.due');
    Route::get('/orders/closed', ClosedOrderIndex::class)->name('orders.closed');
    Route::get('/orders/cancelled', CancelledOrderIndex::class)->name('orders.closed');
    Route::get('/orders/periodic', PeriodicOrderIndex::class)->name('orders.periodic.index');
    Route::get('/orders/inventory', OrderInventory::class)->name('orders.inventory');
    Route::get('/orders/{id}', OrderShow::class)->name('orders.show');

    Route::get('/report/followup', FollowupReport::class)->name('reports.followup');
    Route::get('/report/daily-loading', DailyLoadingReport::class)->name('reports.daily.loading');
    Route::get('/report/customers', CustomerReport::class)->name('reports.customers');
    Route::get('/report/orders', OrderReport::class)->name('reports.orders');
    Route::get('/report/customers/transactions', CustomerTransactionReport::class)->name('reports.customers.transactions');



    Route::get('/productions', ProductionPlanning::class)->name('production.planning');
});


Route::get('/welcome', [HomeController::class, 'welcome']);
Route::post('/login', [HomeController::class, 'authenticate']);
Route::get('/login', [HomeController::class, 'login'])->name('login');
Route::get('/logout', [HomeController::class, 'logout'])->name('logout');
