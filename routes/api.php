<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('data-initiatives', \App\Http\Controllers\Api\DataInitiativeController::class);
});
