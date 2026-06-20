<?php

use App\Http\Controllers\BusinessAssetController;
use App\Http\Controllers\DomainController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('business-assets', [BusinessAssetController::class, 'index'])->name('web.business-assets.index');
    Route::get('business-assets/{businessAsset}', [BusinessAssetController::class, 'show'])->name('web.business-assets.show');

    Route::get('domains', [DomainController::class, 'index'])->name('web.domains.index');
    Route::get('domains/create', [DomainController::class, 'create'])->name('web.domains.create');
    Route::post('domains', [DomainController::class, 'store'])->name('web.domains.store');
    Route::get('domains/{domain}', [DomainController::class, 'show'])->name('web.domains.show');
    Route::get('domains/{domain}/edit', [DomainController::class, 'edit'])->name('web.domains.edit');
    Route::put('domains/{domain}', [DomainController::class, 'update'])->name('web.domains.update');
    Route::delete('domains/{domain}', [DomainController::class, 'destroy'])->name('web.domains.destroy');
});

require __DIR__.'/settings.php';
