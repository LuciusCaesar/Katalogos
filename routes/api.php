<?php

use App\Http\Controllers\Api\BusinessAssetController;
use App\Http\Controllers\Api\DataInitiativeController;
use App\Http\Controllers\Api\DataIssueController;
use App\Http\Controllers\Api\RootCauseController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::apiResource('data-initiatives', DataInitiativeController::class);
    Route::apiResource('business-assets', BusinessAssetController::class);
    Route::apiResource('data-issues', DataIssueController::class);
    Route::apiResource('root-causes', RootCauseController::class);
});
