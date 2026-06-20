<?php

use App\Http\Controllers\BusinessAssetController;
use App\Http\Controllers\DataIssueController;
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

    Route::get('data-issues', [DataIssueController::class, 'index'])->name('web.data-issues.index');
    Route::get('data-issues/create', [DataIssueController::class, 'create'])->name('web.data-issues.create');
    Route::post('data-issues', [DataIssueController::class, 'store'])->name('web.data-issues.store');
    Route::get('data-issues/{dataIssue}', [DataIssueController::class, 'show'])->name('web.data-issues.show');
    Route::get('data-issues/{dataIssue}/edit', [DataIssueController::class, 'edit'])->name('web.data-issues.edit');
    Route::put('data-issues/{dataIssue}', [DataIssueController::class, 'update'])->name('web.data-issues.update');
    Route::delete('data-issues/{dataIssue}', [DataIssueController::class, 'destroy'])->name('web.data-issues.destroy');
});

require __DIR__.'/settings.php';
