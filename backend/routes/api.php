<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BillChargebackController;
use App\Http\Controllers\Api\ChargebackLookupController;

Route::get('/chargebacks', [BillChargebackController::class, 'index']);
Route::post('/chargebacks', [BillChargebackController::class, 'store']);
Route::put('/chargebacks/{id}', [BillChargebackController::class, 'update']);
Route::delete('/chargebacks/{id}', [BillChargebackController::class, 'destroy']);

Route::get('/chargebacks/lookups', [ChargebackLookupController::class, 'lookups']);
