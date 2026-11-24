<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::inertia('mentions-legales', 'public/Legal')->name('legal');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

Route::inertia('a-propos', 'public/About')->name('about');

require __DIR__.'/settings.php';
