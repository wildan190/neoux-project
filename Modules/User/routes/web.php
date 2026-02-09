<?php

use Modules\User\Http\Controllers\SettingsController;
use Modules\User\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [\Modules\User\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/select/{companyId}', [\Modules\User\Http\Controllers\DashboardController::class, 'selectCompany'])->name('dashboard.select-company');
    Route::get('/notifications', [\Modules\User\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/latest', [\Modules\User\Http\Controllers\NotificationController::class, 'getLatestNotifications'])->name('notifications.latest');
    Route::get('/notifications/unread-count', [\Modules\User\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/mark-as-read/{id}', [\Modules\User\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-as-read', [\Modules\User\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.notifications.update');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile/details', [ProfileController::class, 'updateDetails'])->name('profile.details.update');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto'])->name('profile.photo.delete');
});
