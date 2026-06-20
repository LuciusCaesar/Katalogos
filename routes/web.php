<?php

use App\Http\Controllers\BusinessAssetController;
use App\Http\Controllers\DomainController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('business-assets', [BusinessAssetController::class, 'index'])->name('web.business-assets.index');
    Route::get('business-assets/create', [BusinessAssetController::class, 'create'])->name('web.business-assets.create');
    Route::post('business-assets', [BusinessAssetController::class, 'store'])->name('web.business-assets.store');
    Route::get('business-assets/{businessAsset}', [BusinessAssetController::class, 'show'])->name('web.business-assets.show');
    Route::get('business-assets/{businessAsset}/edit', [BusinessAssetController::class, 'edit'])->name('web.business-assets.edit');
    Route::put('business-assets/{businessAsset}', [BusinessAssetController::class, 'update'])->name('web.business-assets.update');
    Route::delete('business-assets/{businessAsset}', [BusinessAssetController::class, 'destroy'])->name('web.business-assets.destroy');

    Route::get('domains', [DomainController::class, 'index'])->name('web.domains.index');
    Route::get('domains/create', [DomainController::class, 'create'])->name('web.domains.create');
    Route::post('domains', [DomainController::class, 'store'])->name('web.domains.store');
    Route::get('domains/{domain}', [DomainController::class, 'show'])->name('web.domains.show');
    Route::get('domains/{domain}/edit', [DomainController::class, 'edit'])->name('web.domains.edit');
    Route::put('domains/{domain}', [DomainController::class, 'update'])->name('web.domains.update');
    Route::delete('domains/{domain}', [DomainController::class, 'destroy'])->name('web.domains.destroy');
});

require __DIR__.'/settings.php';
