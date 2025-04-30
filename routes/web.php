<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SheetSyncController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/sheet-data', [SheetSyncController::class, 'getData']);
Route::post('/sheet-data', [SheetSyncController::class, 'updateData']);