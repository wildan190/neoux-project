<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Main Dashboard is now in User Module
// Route::get('/dashboard', [\Modules\User\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
// This is actually handled by the User module's web.php now.
