<?php

use Illuminate\Support\Facades\Route;

use App\Modules\Auth\Presentation\Http\Controllers\LoginController;
use App\Modules\Auth\Presentation\Http\Controllers\RegisterController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisterController::class, 'index'])->name('register');
    Route::post('register', [RegisterController::class, 'store']);

    Route::get('login', [LoginController::class, 'index'])->name('login');
    Route::post('login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/companies', [\App\Modules\Company\Presentation\Http\Controllers\CompanyController::class, 'index'])->name('companies.index');
    Route::get('/companies/create', [\App\Modules\Company\Presentation\Http\Controllers\CompanyController::class, 'create'])->name('companies.create');
    Route::post('/companies', [\App\Modules\Company\Presentation\Http\Controllers\CompanyController::class, 'store'])->name('companies.store');
    Route::get('/companies/{company}', [\App\Modules\Company\Presentation\Http\Controllers\CompanyController::class, 'show'])->name('companies.show');
});
