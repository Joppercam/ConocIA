<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NewsController;
use Illuminate\Support\Facades\Route;

// Rutas de autenticación para admin
Route::prefix('admin')->name('admin.')->group(function () {
    // Rutas para usuarios no autenticados
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AuthController::class, 'login']);
    });

    // Rutas protegidas por autenticación y middleware admin
    Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        
        // Cerrar sesión
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        // Gestión de noticias
        Route::resource('news', NewsController::class);
        Route::post('news/bulk-actions', [NewsController::class, 'bulkActions'])->name('news.bulk-actions');
        Route::get('news/export', [NewsController::class, 'export'])->name('news.export');
    });
});