<?php

use App\Http\Controllers\HomeController;
use App\Livewire\Users\Profile;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/profile',Profile::class);
Route::get('/welcome', [HomeController::class, 'welcome']);
Route::post('/login', [HomeController::class, 'authenticate']);
Route::get('/login', [HomeController::class, 'login'])->name('login');
Route::get('/logout', [HomeController::class, 'logout'])->name('logout');