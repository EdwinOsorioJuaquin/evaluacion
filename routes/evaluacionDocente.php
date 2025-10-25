<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EvaluacionDocente\AuthController;
use App\Http\Controllers\EvaluacionDocente\EvaluationController;
use App\Http\Controllers\EvaluacionDocente\Admin\QuestionController;

// ============================================
// RUTAS PÚBLICAS
// ============================================
Route::prefix('evaluacion')->name('evaluacion.')->group(function () {


// Página principal (Welcome)
Route::get('/', function () {
    return view('evaluacion.welcome');
})->name('welcome');

// Login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// ============================================
// RUTAS PROTEGIDAS
// ============================================

Route::middleware(['auth'])->group(function () {
    
    // Dashboard principal - redirige según el rol
    Route::get('/dashboard', [EvaluationController::class, 'dashboard'])->name('dashboard');
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ============================================
    // RUTAS DE ADMIN
    // ============================================

    Route::prefix('admin')->name('admin.')->group(function () {
        // Dashboard Admin
        Route::get('/dashboard', function () {
            return view('evaluacion.admin.dashboard');
        })->name('dashboard');
        
        // Gestión de Sesiones de Evaluación
        Route::prefix('sessions')->name('sessions.')->group(function () {
            Route::get('/', [EvaluationController::class, 'index'])->name('index');
            Route::get('/create', [EvaluationController::class, 'create'])->name('create');
            Route::post('/', [EvaluationController::class, 'store'])->name('store');
            Route::get('/{session}/edit', [EvaluationController::class, 'edit'])->name('edit');
            Route::put('/{session}', [EvaluationController::class, 'update'])->name('update');
            Route::delete('/{session}', [EvaluationController::class, 'destroy'])->name('destroy');
            
            // Gestión de Preguntas (anidadas bajo sesiones)
            Route::prefix('{session}/questions')->name('questions.')->group(function () {
                Route::get('/', [QuestionController::class, 'index'])->name('index');
                Route::get('/create', [QuestionController::class, 'create'])->name('create');
                Route::post('/', [QuestionController::class, 'store'])->name('store');
                Route::get('/{question}/edit', [QuestionController::class, 'edit'])->name('edit');
                Route::put('/{question}', [QuestionController::class, 'update'])->name('update');
                Route::delete('/{question}', [QuestionController::class, 'destroy'])->name('destroy');
                Route::post('/update-order', [QuestionController::class, 'updateOrder'])->name('update-order');
                
                // Rutas adicionales
                Route::post('/bulk-action', [QuestionController::class, 'bulkAction'])->name('bulk-action');
                Route::get('/{question}/toggle-status', [QuestionController::class, 'toggleStatus'])->name('toggle-status');
                Route::get('/{question}/clone', [QuestionController::class, 'clone'])->name('clone');
            });
        });

        // Reportes y Resultados
        Route::get('/reports', [EvaluationController::class, 'reports'])->name('reports');
        Route::get('/results/{session}', [EvaluationController::class, 'instructorSessionResults'])->name('session.results');
    });

// ============================================
// RUTAS DE ESTUDIANTE - ORDEN CORREGIDO
// ============================================

Route::prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [EvaluationController::class, 'studentDashboard'])->name('dashboard');
    
    // Evaluaciones disponibles - ORDEN CORREGIDO
    Route::prefix('evaluations')->name('evaluations.')->group(function () {
        // RUTAS SIN PARÁMETROS PRIMERO
        Route::get('/', [EvaluationController::class, 'availableEvaluations'])->name('index');
        Route::get('/history', [EvaluationController::class, 'evaluationHistory'])->name('history');
        
        // RUTAS ESPECÍFICAS CON PARÁMETROS
        Route::get('/{session}/select-instructor', [EvaluationController::class, 'selectInstructor'])->name('select-instructor');
        
        // ⚠️ IMPORTANTE: Ruta POST PRIMERO que la GET
        Route::post('/{session}/submit', [EvaluationController::class, 'submitEvaluation'])->name('submit');
        
        // RUTA GET GENÉRICA - ÚLTIMA
        Route::get('/{session}', [EvaluationController::class, 'showEvaluation'])->name('show');
    });
});

// ============================================
// RUTAS DE INSTRUCTOR - ACTUALIZADAS
// ============================================

 // Rutas específicas para instructores
    Route::prefix('instructor')->name('instructor.')->group(function () {
        Route::get('/dashboard', [EvaluationController::class, 'instructorDashboard'])->name('dashboard');
        Route::get('/results', [EvaluationController::class, 'instructorResults'])->name('results.index');
        Route::get('/results/session/{sessionId}', [EvaluationController::class, 'instructorSessionResults'])->name('results.session');
        Route::get('/results/detail', [EvaluationController::class, 'instructorDetail'])->name('results.detail');
    });

    // Ruta dashboard principal (que ya tienes)
    Route::get('/dashboard', [EvaluationController::class, 'dashboard'])->name('dashboard');
});

});