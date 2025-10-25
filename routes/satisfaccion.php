<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Satisfaccion\AuthController;
use App\Http\Controllers\Satisfaccion\SurveyController;
use App\Http\Controllers\Satisfaccion\QuestionController;
use App\Http\Controllers\Satisfaccion\ResponseController;
use App\Http\Controllers\Satisfaccion\ReportController;
use App\Http\Middleware\RoleMiddleware;

Route::prefix('satisfaccion')->name('satisfaccion.')->group(function () {


    // Página de inicio
    Route::get('/', function () {
        return view('satisfaccion.welcome'); 
    })->name('welcome');

    // ==========================
    // 🔐 Autenticación
    // ==========================
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    // Listado de encuestas (Index)
    Route::get('/admin/surveys', [SurveyController::class, 'adminIndex'])->name('admin.surveys.index');


    // ==========================
    // 🧑‍🎓 Estudiantes
    // ==========================
        Route::middleware(['auth', RoleMiddleware::class . ':student'])->group(function () {
        Route::get('/student/dashboard', [SurveyController::class, 'studentIndex'])->name('student.dashboard');
        Route::get('/student/surveys/{id}', [SurveyController::class, 'show'])->name('student.surveys.show');
        Route::post('/student/surveys/{id}/submit', [SurveyController::class, 'submit'])->name('student.surveys.submit');
    });

    // ==========================
    // 🧑‍💼 Administrador
    // ==========================
        Route::middleware(['auth', RoleMiddleware::class . ':admin'])->group(function () {

        // Dashboard
        Route::get('/admin/dashboard', [SurveyController::class, 'adminIndex'])->name('admin.dashboard');

        // Gestión de encuestas
        Route::get('/admin/surveys/create', [SurveyController::class, 'create'])->name('admin.surveys.create');
        Route::post('/admin/surveys', [SurveyController::class, 'store'])->name('admin.surveys.store');
        Route::get('/admin/surveys/{survey}/edit', [SurveyController::class, 'edit'])->name('admin.surveys.edit');
        Route::put('/admin/surveys/{survey}', [SurveyController::class, 'update'])->name('admin.surveys.update');
        Route::delete('/admin/surveys/{survey}', [SurveyController::class, 'destroy'])->name('admin.surveys.destroy');

        // Añadir preguntas a una encuesta existente
        Route::get('/admin/surveys/{survey}/questions', [QuestionController::class, 'addQuestions'])->name('admin.surveys.add_Questions');
        Route::post('/admin/surveys/{survey}/questions', [QuestionController::class, 'storeQuestions'])->name('admin.surveys.storeQuestions');

        // Gestión de preguntas (individual)
        Route::post('/admin/surveys/{survey}/questions/store', [QuestionController::class, 'storeQuestions'])->name('admin.questions.store');
        Route::delete('/admin/questions/{id}', [QuestionController::class, 'destroy'])->name('admin.questions.destroy');

        // Reportes
        Route::get('/admin/reports/{survey}', [ReportController::class, 'generate'])->name('admin.reports.generate');
        Route::get('/admin/reports/{survey}/pdf', [ReportController::class, 'downloadPdf'])->name('admin.reports.pdf');
        Route::get('/admin/reports/{survey}/excel', [ReportController::class, 'downloadExcel'])->name('admin.reports.excel');
        
    });


});