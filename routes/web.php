<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Livewire\Customers\CustomerIndex;
use App\Livewire\Customers\CustomerShow;
use App\Livewire\Customers\FollowupIndex;
use App\Livewire\Customers\ZoneIndex;
use App\Livewire\Pets\PetIndex;
use App\Livewire\Products\ProductIndex;
use App\Livewire\Products\ProductShow;
use App\Livewire\Tasks\TaskIndex;
use App\Livewire\Tasks\TaskShow;
use App\Livewire\Users\NotificationIndex;
use App\Livewire\Users\Profile;
use App\Livewire\Users\UserIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('customer.index');
});

Route::middleware('auth')->group(function () {
Route::get('/profile',Profile::class);
Route::get('/users',UserIndex::class);
Route::get('/customers',CustomerIndex::class)->name('customer.index');
Route::get('/customers/{id}',CustomerShow::class)->name('customer.show');
Route::get('/products',ProductIndex::class)->name('product.index');
Route::get('/products/{id}',ProductShow::class)->name('product.show');
Route::get('/pets',PetIndex::class);
Route::get('/zones',ZoneIndex::class);
Route::get('/followups',FollowupIndex::class);
Route::get('/tasks',TaskIndex::class);
Route::get('/tasks/{id}',TaskShow::class)->name('tasks.show');
Route::get('/notifications', NotificationIndex::class);
Route::post('/notifications/seen/{id}', [UserController::class, 'setNotfAsSeen']);
});
Route::get('/welcome', [HomeController::class, 'welcome']);
Route::post('/login', [HomeController::class, 'authenticate']);
Route::get('/login', [HomeController::class, 'login'])->name('login');
Route::get('/logout', [HomeController::class, 'logout'])->name('logout');