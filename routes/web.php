<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('business-assets', [\App\Http\Controllers\BusinessAssetController::class, 'index'])->name('web.business-assets.index');
    Route::get('business-assets/{businessAsset}', [\App\Http\Controllers\BusinessAssetController::class, 'show'])->name('web.business-assets.show');
});

require __DIR__.'/settings.php';
