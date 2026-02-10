<?php

use Illuminate\Support\Facades\Route;
use Modules\Company\Http\Controllers\CompanyController;
use Modules\Company\Http\Controllers\CompanyDashboardController;
use Modules\Company\Http\Controllers\TeamController;
use Modules\Company\Http\Controllers\WarehouseController;

// Invitation (Public/Auth)
Route::get('/invitation/{token}', [TeamController::class, 'acceptInvitation'])->name('team.accept-invitation');
Route::post('/invitation/process', [TeamController::class, 'processAcceptInvitation'])->name('team.process-acceptance');

Route::middleware('auth')->group(function () {
    Route::get('/company-dashboard', [CompanyDashboardController::class, 'index'])->name('company.dashboard');

    Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
    Route::get('/companies/create', [CompanyController::class, 'create'])->name('companies.create');
    Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
    Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('companies.show');
    Route::get('/companies/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
    Route::put('/companies/{company}', [CompanyController::class, 'update'])->name('companies.update');

    // Team Management
    Route::prefix('team')->name('team.')->middleware('company.selected')->group(function () {
        Route::get('/', [TeamController::class, 'index'])->name('index');
        Route::post('/invite', [TeamController::class, 'invite'])->name('invite');
        Route::post('/{user}/remove', [TeamController::class, 'removeMember'])->name('remove');
        Route::put('/{user}/role', [TeamController::class, 'updateRole'])->name('update-role');
    });

    // Warehouse Management (Procurement Context)
    Route::prefix('procurement/warehouses')->name('procurement.warehouse.')->middleware('company.selected')->group(function () {
        Route::get('/', [WarehouseController::class, 'index'])->name('index');
        Route::get('/create', [WarehouseController::class, 'create'])->name('create');
        Route::post('/', [WarehouseController::class, 'store'])->name('store');
        Route::get('/{warehouse}', [WarehouseController::class, 'show'])->name('show');
        Route::get('/{warehouse}/edit', [WarehouseController::class, 'edit'])->name('edit');
        Route::put('/{warehouse}', [WarehouseController::class, 'update'])->name('update');
        Route::delete('/{warehouse}', [WarehouseController::class, 'destroy'])->name('destroy');
    });
});
