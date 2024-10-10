<?php

use App\Http\Controllers\HomeController;
use App\Livewire\Customers\CustomerIndex;
use App\Livewire\Customers\CustomerShow;
use App\Livewire\Customers\ZoneIndex;
use App\Livewire\Pets\PetIndex;
use App\Livewire\Users\Profile;
use App\Livewire\Users\UserIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('/welcome');
});

Route::middleware('auth')->group(function () {
Route::get('/profile',Profile::class);
Route::get('/users',UserIndex::class);
Route::get('/customers',CustomerIndex::class);
Route::get('/customers/{id}',CustomerShow::class)->name('customer.show');
Route::get('/pets',PetIndex::class);
Route::get('/zones',ZoneIndex::class);
});
Route::get('/welcome', [HomeController::class, 'welcome']);
Route::post('/login', [HomeController::class, 'authenticate']);
Route::get('/login', [HomeController::class, 'login'])->name('login');
Route::get('/logout', [HomeController::class, 'logout'])->name('logout');