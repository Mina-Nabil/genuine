<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Livewire\Accounting\AccountIndex;
use App\Livewire\Accounting\AccountShow;
use App\Livewire\Accounting\CreateJournalEntry;
use App\Livewire\Accounting\EntryTitleIndex;
use App\Livewire\Accounting\JournalEntryIndex;
use App\Livewire\Accounting\MainAccountIndex;
use App\Livewire\Accounting\UnapprovedEntryIndex;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Users\Calendar;
use App\Livewire\Customers\CustomerIndex;
use App\Livewire\Customers\CustomerShow;
use App\Livewire\Customers\FollowupIndex;
use App\Livewire\Customers\TitlesIndex;
use App\Livewire\Customers\ZoneIndex;
use App\Livewire\Materials\InvoiceCreate;
use App\Livewire\Materials\InvoiceIndex;
use App\Livewire\Materials\InvoiceShow;
use App\Livewire\Materials\MaterialIndex;
use App\Livewire\Materials\MaterialShow;
use App\Livewire\Materials\PaidInvoiceIndex;
use App\Livewire\Materials\SupplierIndex;
use App\Livewire\Materials\SupplierShow;
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
use App\Livewire\Reports\DailySalesPerformanceReport;
use App\Livewire\Reports\DailyTotalsReport;
use App\Livewire\Reports\DriverBalanceTransactionReport;
use App\Livewire\Reports\DriversBalanceReport;
use App\Livewire\Reports\DriverShiftDeliveryReport;
use App\Livewire\Reports\InventoryReport;
use App\Livewire\Reports\MonthlyTotalsReport;
use App\Livewire\Reports\OrderReport;
use App\Livewire\Reports\PaymentTitleReport;
use App\Livewire\Reports\ProductionPlanning;
use App\Livewire\Reports\WeeklyTotalsReport;
use App\Livewire\Reports\ZoneCountReport;
use App\Livewire\Tasks\TaskIndex;
use App\Livewire\Tasks\TaskShow;
use App\Livewire\Users\NotificationIndex;
use App\Livewire\Users\Profile;
use App\Livewire\Users\UserIndex;
use App\Models\Accounting\Account;
use App\Models\Users\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('orders.index');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/orders/driver', OrderDriverShift::class)->name('orders.driver.shift');
    Route::get('/report/drivers/transactions', DriverBalanceTransactionReport::class)->name('reports.drivers.transactions');
});

Route::middleware(['auth', 'no_driver', 'accounting_only_admin'])->group(function () {

    Route::get('/profile/{id}', Profile::class)->name('profile');
    Route::get('/users', UserIndex::class);

    Route::get('/customers', CustomerIndex::class)->name('customer.index');
    Route::get('/customers/{id}', CustomerShow::class)->name('customer.show');

    Route::get('/suppliers', SupplierIndex::class)->name('supplier.index');
    Route::get('/suppliers/{id}', SupplierShow::class)->name('supplier.show');

    Route::get('/materials', MaterialIndex::class)->name('material.index');
    Route::get('/materials/{id}', MaterialShow::class)->name('material.show');

    Route::get('/invoices/create', InvoiceCreate::class)->name('invoice.create');
    Route::get('/invoices', InvoiceIndex::class)->name('invoice.index');
    Route::get('/invoices/paid', PaidInvoiceIndex::class)->name('paid.invoice.index');
    Route::get('/invoices/{id}', InvoiceShow::class)->name('invoice.show');

    Route::get('/products', ProductIndex::class)->name('product.index');
    Route::get('/products/{id}', ProductShow::class)->name('product.show');

    Route::get('/inventories', InventoryTransactionIndex::class)->name('inventories.index');
    Route::get('/transactions', TransactionIndex::class)->name('transactions.index');

    Route::get('/combos', ComboIndex::class)->name('combo.index');
    Route::get('/combos/{id}', ComboShow::class)->name('combo.show');

    Route::get('/pets', PetIndex::class);
    Route::get('/zones', ZoneIndex::class);
    Route::get('/titles', TitlesIndex::class);
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
    Route::get('/orders/cancelled', CancelledOrderIndex::class)->name('orders.cancelled');
    Route::get('/orders/periodic', PeriodicOrderIndex::class)->name('orders.periodic.index');
    Route::get('/orders/inventory', OrderInventory::class)->name('orders.inventory');
    Route::get('/orders/{id}', OrderShow::class)->name('orders.show');

    Route::get('/report/followup', FollowupReport::class)->name('reports.followup');
    Route::get('/report/daily-loading', DailyLoadingReport::class)->name('reports.daily.loading');
    Route::get('/report/customers', CustomerReport::class)->name('reports.customers');
    Route::get('/report/orders', OrderReport::class)->name('reports.orders');
    Route::get('/report/orders/totals', DailyTotalsReport::class)->name('reports.orders.daily');
    Route::get('/report/orders/monthly', MonthlyTotalsReport::class)->name('reports.orders.monthly');
    Route::get('/report/orders/weekly', WeeklyTotalsReport::class)->name('reports.orders.weekly');
    Route::get('/report/orders/performance', DailySalesPerformanceReport::class)->name('reports.orders.performance');
    Route::get('/report/customers/transactions', CustomerTransactionReport::class)->name('reports.customers.transactions');
    Route::get('/report/Zones/count', ZoneCountReport::class)->name('reports.zones.count');
    Route::get('/report/inventory', InventoryReport::class)->name('reports.inventory');
    Route::get('/report/drivers/balance', DriversBalanceReport::class)->name('reports.drivers.balance');
    Route::get('/report/payment-titles', PaymentTitleReport::class)
    ->name('report.payment-titles');
    Route::get('/report/product-sales', App\Livewire\Reports\ProductSalesReport::class)
    ->name('report.product-sales');
    Route::get('/report/driver-shift-delivery', DriverShiftDeliveryReport::class)
    ->name('report.driver-shift-delivery');
    Route::get('/report/payment-methods', App\Livewire\Reports\PaymentMethodReport::class)
    ->name('report.payment-methods');
    
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    Route::get('/productions', ProductionPlanning::class)->name('production.planning');


    //accounting
    Route::get('/accounts/main', MainAccountIndex::class);
    Route::get('/accounts', AccountIndex::class);
    Route::get('/accounts/importtree', function () {
        Account::importAccounts();
        return response()->redirectTo('/accounts/main');
    });
    Route::get('/accounts/entries', JournalEntryIndex::class);
    Route::get('/accounts/titles', EntryTitleIndex::class);
    Route::get('/accounts/entries/new', CreateJournalEntry::class);
    Route::get('/accounts/entries/unapproved', UnapprovedEntryIndex::class);
    Route::get('/accounts/gettree/{id}', function ($id) {
        return response()->json(Account::findOrFail($id)->getTree());
    });
    Route::get('/accounts/{id}', AccountShow::class)->name('accounts.show');

    Route::get('/switch-session/{username}', function($username){
        return User::switchSession($username);
    });

});


Route::get('/welcome', [HomeController::class, 'welcome']);
Route::post('/login', [HomeController::class, 'authenticate']);
Route::get('/login', [HomeController::class, 'login'])->name('login');
Route::get('/logout', [HomeController::class, 'logout'])->name('logout');




