<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Impacto\AuthController;
use App\Http\Controllers\Impacto\DashboardController;
use App\Http\Controllers\Impacto\SurveyController;

Route::prefix('impacto')->name('impacto.')->group(function () {


    // Ruta principal
    Route::get('/', function () {
        return view('impacto.welcome');
    })->name('welcome');

    // Rutas de autenticación (sin login)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.post');
        
        Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    });

    // Rutas protegidas (requieren autenticación)
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/survey/{graduateId}', [DashboardController::class, 'showSurveyForm'])->name('survey.show');
        Route::post('/survey/{graduateId}', [DashboardController::class, 'submitSurvey'])->name('survey.submit');
        
        // Rutas de encuestas
        Route::post('/surveys/store', [SurveyController::class, 'store'])->name('surveys.store');
        Route::get('/surveys/assignment-data', [SurveyController::class, 'getAssignmentData'])->name('surveys.assignment.data');
        Route::post('/surveys/assign', [SurveyController::class, 'assignSurvey'])->name('surveys.assign');

            // ⬇️ NUEVAS RUTAS PARA GESTIONAR ENCUESTAS
        Route::get('/surveys/list', [SurveyController::class, 'getSurveys'])->name('surveys.list');
        Route::get('/surveys/{id}/details', [SurveyController::class, 'getSurveyDetails'])->name('surveys.details');
        Route::post('/surveys/{id}/update', [SurveyController::class, 'update'])->name('surveys.update');
        Route::delete('/surveys/{id}/delete', [SurveyController::class, 'destroy'])->name('surveys.delete');
        
            // Rutas de reportes (para admin)
        Route::middleware('auth')->group(function () {
            Route::get('/reports/data', [SurveyController::class, 'getReportsData'])->name('reports.data');
            Route::get('/reports/survey/{id}/download', [SurveyController::class, 'downloadReport'])->name('reports.download');
            Route::get('/reports/survey/{id}/preview', [SurveyController::class, 'previewReport'])->name('reports.preview');
        });
        
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });


});