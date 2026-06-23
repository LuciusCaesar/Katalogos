<?php

use App\Http\Controllers\BusinessAssetController;
use App\Http\Controllers\BusinessRuleController;
use App\Http\Controllers\DataIssueController;
use App\Http\Controllers\DataQualityCheckController;
use App\Http\Controllers\DataSourceController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\RootCauseController;
use App\Http\Controllers\SolutionController;
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

    Route::get('business-rules', [BusinessRuleController::class, 'index'])->name('web.business-rules.index');
    Route::get('business-rules/create', [BusinessRuleController::class, 'create'])->name('web.business-rules.create');
    Route::post('business-rules', [BusinessRuleController::class, 'store'])->name('web.business-rules.store');
    Route::get('business-rules/{businessRule}', [BusinessRuleController::class, 'show'])->name('web.business-rules.show');
    Route::get('business-rules/{businessRule}/edit', [BusinessRuleController::class, 'edit'])->name('web.business-rules.edit');
    Route::put('business-rules/{businessRule}', [BusinessRuleController::class, 'update'])->name('web.business-rules.update');
    Route::delete('business-rules/{businessRule}', [BusinessRuleController::class, 'destroy'])->name('web.business-rules.destroy');

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

    Route::get('data-sources', [DataSourceController::class, 'index'])->name('web.data-sources.index');
    Route::get('data-sources/create', [DataSourceController::class, 'create'])->name('web.data-sources.create');
    Route::post('data-sources', [DataSourceController::class, 'store'])->name('web.data-sources.store');
    Route::get('data-sources/{dataSource}', [DataSourceController::class, 'show'])->name('web.data-sources.show');
    Route::get('data-sources/{dataSource}/edit', [DataSourceController::class, 'edit'])->name('web.data-sources.edit');
    Route::put('data-sources/{dataSource}', [DataSourceController::class, 'update'])->name('web.data-sources.update');
    Route::delete('data-sources/{dataSource}', [DataSourceController::class, 'destroy'])->name('web.data-sources.destroy');

    Route::get('data-quality-checks', [DataQualityCheckController::class, 'index'])->name('web.data-quality-checks.index');
    Route::get('data-quality-checks/create', [DataQualityCheckController::class, 'create'])->name('web.data-quality-checks.create');
    Route::post('data-quality-checks', [DataQualityCheckController::class, 'store'])->name('web.data-quality-checks.store');
    Route::get('data-quality-checks/{dataQualityCheck}', [DataQualityCheckController::class, 'show'])->name('web.data-quality-checks.show');
    Route::get('data-quality-checks/{dataQualityCheck}/edit', [DataQualityCheckController::class, 'edit'])->name('web.data-quality-checks.edit');
    Route::put('data-quality-checks/{dataQualityCheck}', [DataQualityCheckController::class, 'update'])->name('web.data-quality-checks.update');
    Route::delete('data-quality-checks/{dataQualityCheck}', [DataQualityCheckController::class, 'destroy'])->name('web.data-quality-checks.destroy');

    Route::get('root-causes', [RootCauseController::class, 'index'])->name('web.root-causes.index');
    Route::get('root-causes/create', [RootCauseController::class, 'create'])->name('web.root-causes.create');
    Route::post('root-causes', [RootCauseController::class, 'store'])->name('web.root-causes.store');
    Route::get('root-causes/{rootCause}', [RootCauseController::class, 'show'])->name('web.root-causes.show');
    Route::get('root-causes/{rootCause}/edit', [RootCauseController::class, 'edit'])->name('web.root-causes.edit');
    Route::put('root-causes/{rootCause}', [RootCauseController::class, 'update'])->name('web.root-causes.update');
    Route::delete('root-causes/{rootCause}', [RootCauseController::class, 'destroy'])->name('web.root-causes.destroy');

    Route::get('solutions', [SolutionController::class, 'index'])->name('web.solutions.index');
    Route::get('solutions/create', [SolutionController::class, 'create'])->name('web.solutions.create');
    Route::post('solutions', [SolutionController::class, 'store'])->name('web.solutions.store');
    Route::get('solutions/{solution}', [SolutionController::class, 'show'])->name('web.solutions.show');
    Route::get('solutions/{solution}/edit', [SolutionController::class, 'edit'])->name('web.solutions.edit');
    Route::put('solutions/{solution}', [SolutionController::class, 'update'])->name('web.solutions.update');
    Route::delete('solutions/{solution}', [SolutionController::class, 'destroy'])->name('web.solutions.destroy');
});

require __DIR__.'/settings.php';
