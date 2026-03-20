<?php

use App\Http\Controllers\Api\Admin\AuditLogController;
use App\Http\Controllers\Api\Admin\ContentController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\InboxController;
use App\Http\Controllers\Api\Admin\ServicesController;
use App\Http\Controllers\Api\Admin\SettingsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\SiteDataController;
use App\Http\Controllers\Api\VisitorVisitController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('/validate', [AuthController::class, 'validateToken']);
        Route::put('/account', [AuthController::class, 'updateAccount']);
        Route::put('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::prefix('site')->group(function (): void {
    Route::get('/settings', [SiteDataController::class, 'settings']);
    Route::get('/bootstrap', [SiteDataController::class, 'bootstrap']);
    Route::get('/learn-more', [SiteDataController::class, 'learnMore']);
    Route::get('/services/{slug}', [SiteDataController::class, 'serviceDetail']);
    Route::post('/contact-messages', [ContactMessageController::class, 'store']);
    Route::post('/traffic-visits', [VisitorVisitController::class, 'store']);
});

Route::middleware(['auth:sanctum', 'admin.access'])->prefix('admin')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'show']);
    Route::get('/content', [ContentController::class, 'show']);
    Route::put('/content', [ContentController::class, 'update']);

    Route::get('/inbox', [InboxController::class, 'index']);
    Route::patch('/inbox/{contactMessage}/read', [InboxController::class, 'markAsRead']);
    Route::patch('/inbox/{contactMessage}/workflow', [InboxController::class, 'updateWorkflow']);

    Route::get('/audit-logs', [AuditLogController::class, 'index']);

    Route::get('/services', [ServicesController::class, 'show']);
    Route::put('/services', [ServicesController::class, 'update']);

    Route::get('/settings', [SettingsController::class, 'show']);
    Route::put('/settings', [SettingsController::class, 'update']);
    Route::post('/settings/logo', [SettingsController::class, 'uploadLogo']);
});
