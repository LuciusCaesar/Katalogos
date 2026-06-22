<?php

use App\Http\Controllers\Api\BusinessAssetController;
use App\Http\Controllers\Api\BusinessRuleController;
use App\Http\Controllers\Api\DataInitiativeController;
use App\Http\Controllers\Api\DataIssueController;
use App\Http\Controllers\Api\DataSourceController;
use App\Http\Controllers\Api\RootCauseController;
use App\Http\Controllers\Api\SolutionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::apiResource('business-rules', BusinessRuleController::class);
    Route::apiResource('data-initiatives', DataInitiativeController::class);
    Route::apiResource('business-assets', BusinessAssetController::class);
    Route::apiResource('data-issues', DataIssueController::class);
    Route::apiResource('data-sources', DataSourceController::class);
    Route::apiResource('root-causes', RootCauseController::class);
    Route::apiResource('solutions', SolutionController::class);
});
