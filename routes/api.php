<?php

use App\Http\Controllers\SheetSyncController;

Route::get('/sheet-data', [SheetSyncController::class, 'getData']);
Route::post('/sheet-data', [SheetSyncController::class, 'updateData']);
