<?php

use Illuminate\Support\Facades\Route;
use Modules\Catalogue\Http\Controllers\CatalogueController;
use Modules\Catalogue\Http\Controllers\WarehouseController;
use Modules\Catalogue\Http\Controllers\WarehouseReportController;

Route::middleware(['auth', 'company.selected'])->group(function () {

    // Catalogue Routes
    Route::prefix('catalogue')->name('catalogue.')->group(function () {
        Route::get('/import/template', [CatalogueController::class, 'downloadTemplate'])->name('download-template');
        Route::post('/import/preview', [CatalogueController::class, 'previewImport'])->name('import.preview');
        Route::post('/import', [CatalogueController::class, 'import'])->name('import');
        Route::get('/import/status/{id}', [CatalogueController::class, 'checkImportStatus'])->name('import.status');

        Route::post('/bulk-delete', [CatalogueController::class, 'bulkDelete'])->name('bulk-delete');

        Route::get('/', [CatalogueController::class, 'index'])->name('index');
        Route::get('/create', [CatalogueController::class, 'create'])->name('create');
        Route::post('/', [CatalogueController::class, 'store'])->name('store');
        Route::get('/{product}', [CatalogueController::class, 'show'])->name('show');
        Route::post('/{product}/sku', [CatalogueController::class, 'storeSku'])->name('store-sku');
        Route::get('/{product}/edit', [CatalogueController::class, 'edit'])->name('edit');
        Route::put('/{product}', [CatalogueController::class, 'update'])->name('update');
        Route::delete('/{product}', [CatalogueController::class, 'destroy'])->name('destroy');
        Route::delete('/sku/{item}', [CatalogueController::class, 'destroySku'])->name('destroy-sku');
        Route::post('/generate-sku', [CatalogueController::class, 'generateSku'])->name('generate-sku');
    });

    // Warehouse Routes
    Route::get('/warehouse', [WarehouseController::class, 'index'])->name('warehouse.index');
    Route::get('/warehouse/scan', [WarehouseController::class, 'scan'])->name('warehouse.scan');
    Route::post('/warehouse/scan', [WarehouseController::class, 'processScan'])->name('warehouse.process-scan');
    Route::post('/warehouse/adjust', [WarehouseController::class, 'adjustStock'])->name('warehouse.adjust');
    Route::get('/warehouse/report', [WarehouseReportController::class, 'index'])->name('warehouse.report');
    Route::get('/warehouse/qr/{id}', [WarehouseController::class, 'generateQr'])->name('warehouse.generate-qr');

});
