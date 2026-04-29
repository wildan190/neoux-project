<?php

use Illuminate\Support\Facades\Route;
use Modules\Procurement\Http\Controllers\ProcurementController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('procurements', ProcurementController::class)->names('procurement');
});

// Midtrans Webhook Callback (No auth, uses signature verification)
Route::post('/midtrans/callback', [\Modules\Procurement\Http\Controllers\MidtransWebhookController::class, 'handleCallback'])->name('api.midtrans.callback');
