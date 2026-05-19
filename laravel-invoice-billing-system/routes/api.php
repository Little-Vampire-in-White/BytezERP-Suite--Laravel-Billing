<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SyncController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Sync Bytez-ERP data
Route::post('/sync/clients/bytez', [SyncController::class, 'syncClientsToBytezERP']);

