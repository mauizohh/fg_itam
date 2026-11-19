<?php

use App\Http\Controllers\Api\AssetApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
});

// FG IT Helpdesk Asset Integration endpoints
Route::prefix('v1')->group(function () {
    Route::get('/assets', [AssetApiController::class, 'index']);
    Route::get('/assets/{id}', [AssetApiController::class, 'show']);
    Route::post('/assets/lookup', [AssetApiController::class, 'lookup']);
    Route::get('/stats', [AssetApiController::class, 'stats']);
});
