<?php

use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/select-company/{company}', [\App\Http\Controllers\DashboardController::class, 'selectCompany'])->name('dashboard.select-company');
    Route::get('/company-dashboard', function () {
        return view('company-dashboard');
    })->name('company.dashboard');

    Route::get('/companies', [\App\Modules\Company\Presentation\Http\Controllers\CompanyController::class, 'index'])->name('companies.index');
    Route::get('/companies/create', [\App\Modules\Company\Presentation\Http\Controllers\CompanyController::class, 'create'])->name('companies.create');
    Route::post('/companies', [\App\Modules\Company\Presentation\Http\Controllers\CompanyController::class, 'store'])->name('companies.store');
    Route::get('/companies/{company}', [\App\Modules\Company\Presentation\Http\Controllers\CompanyController::class, 'show'])->name('companies.show');
    Route::get('/companies/{company}/edit', [\App\Modules\Company\Presentation\Http\Controllers\CompanyController::class, 'edit'])->name('companies.edit');
    Route::put('/companies/{company}', [\App\Modules\Company\Presentation\Http\Controllers\CompanyController::class, 'update'])->name('companies.update');

    Route::prefix('catalogue')->name('catalogue.')->group(function () {
        Route::get('/', [\App\Modules\Catalogue\Presentation\Http\Controllers\CatalogueController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\Catalogue\Presentation\Http\Controllers\CatalogueController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\Catalogue\Presentation\Http\Controllers\CatalogueController::class, 'store'])->name('store');
        Route::get('/{item}', [\App\Modules\Catalogue\Presentation\Http\Controllers\CatalogueController::class, 'show'])->name('show');
        Route::get('/{item}/edit', [\App\Modules\Catalogue\Presentation\Http\Controllers\CatalogueController::class, 'edit'])->name('edit');
        Route::put('/{item}', [\App\Modules\Catalogue\Presentation\Http\Controllers\CatalogueController::class, 'update'])->name('update');
        Route::delete('/{item}', [\App\Modules\Catalogue\Presentation\Http\Controllers\CatalogueController::class, 'destroy'])->name('destroy');
        Route::post('/generate-sku', [\App\Modules\Catalogue\Presentation\Http\Controllers\CatalogueController::class, 'generateSku'])->name('generate-sku');
    });

    Route::get('/profile', [\App\Modules\User\Presentation\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
});
