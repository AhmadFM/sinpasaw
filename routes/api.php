<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DenahApiController;

// User
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Denah API Routes (v1/denah prefix)
Route::prefix('denah')->name('denah.')->controller(DenahApiController::class)->group(function () {
    // Public endpoints
    Route::get('/', 'index')->name('index');
    Route::get('/list', 'list')->name('list');
    Route::get('/data', 'data')->name('data'); // Legacy compatibility
    Route::get('/{id}', 'show')->name('show');
    Route::get('/blok/{blok}', 'byBlok')->name('byBlok');
    Route::get('/search/status/{status}', 'searchByStatus')->name('searchByStatus');
    Route::post('/route', 'calculateRoute')->name('calculateRoute');
    Route::get('/statistics', 'statistics')->name('statistics');
    
    // Protected endpoints (admin only)
    Route::post('/invalidate-cache', 'invalidateCache')
        ->middleware(['auth:sanctum', 'admin']) // Assuming admin middleware exists
        ->name('invalidateCache');
});
