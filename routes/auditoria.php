<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auditoria\AuthController;
use App\Http\Controllers\Auditoria\DashboardController;
use App\Http\Controllers\Auditoria\AuditController;
use App\Http\Controllers\Auditoria\AuditReportController;
use App\Http\Controllers\Auditoria\FindingController;
use App\Http\Controllers\Auditoria\CorrectiveActionController;
use App\Http\Controllers\Auditoria\UserController;
use App\Http\Controllers\Auditoria\SettingsController;
use App\Http\Controllers\Auditoria\ProfileController;


/*
|--------------------------------------------------------------------------
| MÓDULO: AUDITORÍA
|--------------------------------------------------------------------------
| Todas las rutas del módulo Auditoría se agrupan con el prefijo /auditoria.
| El login se maneja directamente aquí, sin usar auth.php.
|--------------------------------------------------------------------------
*/

Route::prefix('auditoria')->name('auditoria.')->group(function () {

    // === 🔐 LOGIN & LOGOUT ===
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('auth.login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('auth.register');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register.post');

    // === 🔒 RUTAS PROTEGIDAS ===
    Route::middleware(['auth'])->group(function () {

        /** DASHBOARD */
        Route::prefix('dashboard')->group(function () {
            Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
            Route::get('/create', [DashboardController::class, 'create'])->name('dashboard.create-audit');
            Route::post('/store', [DashboardController::class, 'storeAudit'])->name('dashboard.store-audit');
        });

        /** AUDITORÍAS */
        Route::prefix('audits')->group(function () {
            Route::get('/', [AuditController::class, 'index'])->name('audits.index');
            Route::get('/{audit}/show', [AuditController::class, 'show'])->name('audits.show');
            Route::post('/{audit}/start', [AuditController::class, 'start'])->name('audits.start');
            Route::get('/{audit}/edit', [AuditController::class, 'edit'])->name('audits.edit');
            Route::put('/{audit}/update', [AuditController::class, 'update'])->name('audits.update');
            Route::post('/{audit}/complete', [AuditController::class, 'complete'])->name('audits.complete');
            Route::delete('/{audit}', [AuditController::class, 'destroy'])->name('audits.destroy');

            /** REPORTES */
            Route::prefix('/{audit}/report')->group(function () {
                Route::get('/', [AuditReportController::class, 'create'])->name('reports.create');
                Route::get('/preview', [AuditReportController::class, 'preview'])->name('reports.preview');
                Route::get('/download', [AuditReportController::class, 'download'])->name('reports.download');
                Route::get('/pdf', [AuditController::class, 'generateReportPDF'])->name('reports.pdf');
                Route::post('/', [AuditController::class, 'storeRecommendations'])->name('reports.store');
            });

            /** HALLAZGOS */
            Route::prefix('/{audit}/findings')->group(function () {
                Route::get('/', [FindingController::class, 'index'])->name('findings.index');
                Route::get('/{finding}/show', [FindingController::class, 'show'])->name('findings.show');
                Route::post('/', [FindingController::class, 'store'])->name('findings.store');
                Route::get('/{finding}/edit', [FindingController::class, 'edit'])->name('findings.edit');
                Route::put('/{finding}/update', [FindingController::class, 'update'])->name('findings.update');
                Route::delete('/{finding}', [FindingController::class, 'destroy'])->name('findings.destroy');

                /** ACCIONES CORRECTIVAS */
                Route::prefix('/{finding}/actions')->group(function () {
                    Route::get('/', [CorrectiveActionController::class, 'index'])->name('actions.index');
                    Route::post('/', [CorrectiveActionController::class, 'store'])->name('actions.store');
                    Route::get('/{action}/show', [CorrectiveActionController::class, 'show'])->name('actions.show');
                    Route::get('/{action}/edit', [CorrectiveActionController::class, 'edit'])->name('actions.edit');
                    Route::put('/{action}/update', [CorrectiveActionController::class, 'update'])->name('actions.update');
                    Route::delete('/{action}', [CorrectiveActionController::class, 'destroy'])->name('actions.delete');

                    // Actualización de estado y fechas
                    Route::patch('/{action}/status', [CorrectiveActionController::class, 'updateStatus'])->name('actions.status');
                    Route::patch('/{action}/dates', [CorrectiveActionController::class, 'updateDates'])->name('actions.dates');
                    Route::patch('/{action}/execution', [CorrectiveActionController::class, 'updateExecutionDate'])->name('actions.execution');
                    Route::patch('/{action}/completion', [CorrectiveActionController::class, 'updateCompletionDate'])->name('actions.completion');
                });
            });
        });

        /** GESTIÓN DE AUDITORES */
        Route::prefix('auditores')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('auditores.index');
            Route::get('/create', [UserController::class, 'create'])->name('auditores.create');
            Route::post('/', [UserController::class, 'store'])->name('auditores.store');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('auditores.edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('auditores.update');
            Route::post('/{user}/toggle', [UserController::class, 'toggleStatus'])->name('auditores.toggle');
        });

        /** CONFIGURACIÓN PERSONAL */
        Route::prefix('settings')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('settings.index');
            Route::put('/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
            Route::put('/preferences', [SettingsController::class, 'updatePreferences'])->name('settings.preferences.update');
            Route::put('/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.notifications.update');
            Route::post('/logout-others', [SettingsController::class, 'logoutOthers'])->name('settings.logoutOthers');
            Route::post('/export', [SettingsController::class, 'exportData'])->name('settings.data.export');
            Route::post('/deactivate', [SettingsController::class, 'deactivate'])->name('settings.account.deactivate');
        });

        /** PERFIL DE USUARIO */
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
        Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        /** RUTA DE PRUEBA */
        Route::get('/test', fn() => view('dashboard'))->name('test');
    });
});
