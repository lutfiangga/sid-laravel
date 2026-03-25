<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/population.php';
require __DIR__.'/correspondence.php';
require __DIR__.'/public-service.php';
require __DIR__.'/finance.php';
