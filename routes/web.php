<?php

use App\Http\Controllers\admin\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
        Route::post('/profile', [ProfileController::class, 'store'])->name('profile.store');
    });

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [ProfileController::class, 'index'])->name('profile.index');
});

require __DIR__.'/settings.php';
