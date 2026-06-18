<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('business-assets', [\App\Http\Controllers\BusinessAssetController::class, 'index'])->name('web.business-assets.index');
});

require __DIR__.'/settings.php';
