<?php

use App\Modules\Admin\Application\Http\Controllers\AdminDashboardController;
use App\Modules\Admin\Application\Http\Controllers\AdminManagementController;
use App\Modules\Admin\Application\Http\Controllers\Auth\AdminLoginController;
use App\Modules\Admin\Application\Http\Controllers\CompanyReviewController;
use App\Modules\Admin\Application\Http\Controllers\UserManagementController;
use App\Modules\Catalogue\Presentation\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminLoginController::class, 'login']);
    });

    Route::middleware(['admin'])->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Company Review
        Route::prefix('companies')->name('companies.')->group(function () {
            Route::get('/', [CompanyReviewController::class, 'index'])->name('index');
            Route::get('/{company}', [CompanyReviewController::class, 'show'])->name('show');
            Route::post('/{company}/approve', [CompanyReviewController::class, 'approve'])->name('approve');
            Route::post('/{company}/decline', [CompanyReviewController::class, 'decline'])->name('decline');
        });

        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserManagementController::class, 'index'])->name('index');
            Route::get('/create', [UserManagementController::class, 'create'])->name('create');
            Route::post('/', [UserManagementController::class, 'store'])->name('store');
            Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
        });

        // Admin Management
        Route::get('/admins', [AdminManagementController::class, 'index'])->name('admins.index');
        Route::get('/admins/create', [AdminManagementController::class, 'create'])->name('admins.create');
        Route::post('/admins', [AdminManagementController::class, 'store'])->name('admins.store');
        Route::delete('/admins/{admin}', [AdminManagementController::class, 'destroy'])->name('admins.destroy');

        // Category Management
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });
});
