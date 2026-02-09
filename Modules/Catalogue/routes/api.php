<?php

use Illuminate\Support\Facades\Route;
use Modules\Catalogue\Http\Controllers\CatalogueController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('catalogues', CatalogueController::class)->names('catalogue');
});
