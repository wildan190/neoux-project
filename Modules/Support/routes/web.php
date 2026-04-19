<?php

use Illuminate\Support\Facades\Route;
use Modules\Support\Http\Controllers\SupportController;
use Modules\Support\Http\Controllers\AdminSupportController;

// User-facing support routes
Route::middleware(['auth', 'company.selected'])->prefix('support')->name('support.')->group(function () {
    Route::get('/', [SupportController::class, 'index'])->name('index');
    Route::get('/create', [SupportController::class, 'create'])->name('create');
    Route::post('/', [SupportController::class, 'store'])->name('store');
    Route::get('/{ticket}', [SupportController::class, 'show'])->name('show');
});

// Admin-facing support routes (use admin middleware)
Route::middleware(['admin'])->prefix('admin/support')->name('admin.support.')->group(function () {
    Route::get('/', [AdminSupportController::class, 'index'])->name('index');
    Route::get('/{ticket}', [AdminSupportController::class, 'show'])->name('show');
    Route::patch('/{ticket}', [AdminSupportController::class, 'update'])->name('update');
});
