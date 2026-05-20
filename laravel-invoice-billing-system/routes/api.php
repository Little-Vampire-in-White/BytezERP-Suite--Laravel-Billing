<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SyncController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Sync Bytez-ERP data
Route::post('/sync/clients/bytez', [SyncController::class, 'syncClientsToBytezERP']);

// Sync Routes (accessible without session authentication)
Route::prefix('sync')->group(function () {
    Route::get('pull', [SyncController::class, 'syncClientsFromBytezERP'])->name('api.sync.pull');
    Route::get('push', [SyncController::class, 'syncClientsToBytezERP'])->name('api.sync.push');
});
