<?php

namespace App\Http\Controllers\EvaluacionDocente;

use App\Models\EvaluationSession;
use App\Models\EvaluationResponse;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\EvaluationQuestionOption;

class EvaluationController extends Controller
{
    // Admin - Gestión de sesiones (MANTIENE IGUAL)
    public function index()
    {
        $sessions = EvaluationSession::withCount('questions')->latest()->get();
        return view('evaluacion.admin.sessions.index', compact('sessions'));
    }

    public function create()
    {
        return view('evaluacion.admin.sessions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'academic_period' => 'required|string|max:50'
        ]);

        EvaluationSession::create([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'academic_period' => $request->academic_period,
            'created_by' => auth()->id(),
            'status' => 'active'
        ]);

        return redirect()->route('evaluacion.admin.sessions.index')
                        ->with('success', 'Sesión de evaluación creada exitosamente.');
    }

    public function edit(EvaluationSession $session)
    {
        return view('evaluacion.admin.sessions.edit', compact('session'));
    }

    public function update(Request $request, EvaluationSession $session)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'academic_period' => 'required|string|max:50',
            'status' => 'required|in:active,inactive,completed'
        ]);

        $session->update($request->all());

        return redirect()->route('evaluacion.admin.sessions.index')
                        ->with('success', 'Sesión actualizada exitosamente.');
    }

    public function destroy(EvaluationSession $session)
    {
        $session->delete();

        return redirect()->route('evaluacion.admin.sessions.index')
                        ->with('success', 'Sesión eliminada exitosamente.');
    }

    // Dashboard principal (MANTIENE IGUAL)
    public function dashboard()
    {
        $user = auth()->user();
        
        if ($user->hasRole('admin')) {
            $stats = [
                'activeSessions' => EvaluationSession::where('status', 'active')->count(),
                'totalQuestions' => \App\Models\EvaluationQuestion::count(),
                'totalStudents' => User::where('role', 'like', '%"student"%')->count(),
                'totalInstructors' => User::where('role', 'like', '%"instructor"%')->count(),
                'recentSessions' => EvaluationSession::withCount('questions')
                    ->where('status', 'active')
                    ->latest()
                    ->take(3)
                    ->get()
            ];

            return view('evaluacion.admin.dashboard', compact('stats'));
        } elseif ($user->hasRole('student')) {
            return $this->studentDashboard();
        } elseif ($user->hasRole('instructor')) {
            return $this->instructorDashboard(); // NUEVO: Redirige al dashboard del instructor
        } else {
            return $this->handleUnknownRole($user);
        }
    }

    // ============================================
    // MÉTODOS PARA ESTUDIANTES - MANTIENE IGUAL
    // ============================================

    /**
     * Dashboard específico para estudiantes
     */
  public function studentDashboard()
{
    $user = auth()->user();
    
    \Log::info("📊 Dashboard estudiante - User ID: {$user->id}");

    $allSessions = EvaluationSession::where('status', 'active')
        ->withCount('questions')
        ->with(['questions' => function($query) {
            $query->where('status', 'active')
                  ->orderBy('question_order', 'asc');
        }])
        ->orderBy('end_date', 'asc')
        ->get();

    $pendingSessions = $allSessions->filter(function($session) {
        return now()->between($session->start_date, $session->end_date);
    });

    // DEBUG DETALLADO
    $completedEvaluations = 0;
    try {
        // Buscar student_id
        $student = \DB::table('students')->where('user_id', $user->id)->first();
        
        if ($student) {
            \Log::info("📊 Student ID encontrado: {$student->id} para User ID: {$user->id}");
            
            // Contar evaluaciones completadas
$completedEvaluations = EvaluationResponse::where('student_id', $student->id)
    ->selectRaw('COUNT(DISTINCT instructor_id) as count')
    ->value('count') ?? 0;

\Log::info("📊 Evaluaciones completadas: {$completedEvaluations}");
            
            // DEBUG: Ver sesiones específicas completadas
            $completedSessions = EvaluationResponse::where('student_id', $student->id)
                ->distinct('evaluation_session_id')
                ->pluck('evaluation_session_id');
                
            \Log::info("📊 Sesiones completadas IDs: " . $completedSessions->implode(', '));
        } else {
            \Log::warning("❌ No se encontró student_id para User ID: {$user->id}");
        }
    } catch (\Exception $e) {
        \Log::error("💥 Error contando evaluaciones: " . $e->getMessage());
        $completedEvaluations = 0;
    }

    $stats = [
        'pendingEvaluations' => $pendingSessions->count(),
        'completedEvaluations' => $completedEvaluations,
        'totalSessions' => $allSessions->count(),
        'pendingSessions' => $pendingSessions,
        'allSessions' => $allSessions
    ];

    \Log::info("📊 Stats finales - Pendientes: {$stats['pendingEvaluations']}, Completadas: {$stats['completedEvaluations']}");

    return view('evaluacion.student.dashboard', compact('stats', 'pendingSessions', 'allSessions'));
}
    /**
     * Seleccionar instructor antes de la evaluación
     */
/**
 * Seleccionar instructor antes de la evaluación
 */
public function selectInstructor($sessionId)
{
    $session = EvaluationSession::findOrFail($sessionId);
    
    if ($session->status !== 'active' || now() < $session->start_date || now() > $session->end_date) {
        return redirect()->route('evaluacion.student.dashboard')
                        ->with('error', 'Esta evaluación no está disponible.');
    }

    $instructors = User::where('role', 'like', '%instructor%')
                      ->orWhere('role', 'like', '%"instructor"%')
                      ->get();

    // OBTENER STUDENT_ID
    $userId = auth()->id();
    $student = \DB::table('students')->where('user_id', $userId)->first();
    
    if (!$student) {
        return redirect()->route('evaluacion.student.dashboard')
                        ->with('error', 'Error: No se encontró tu registro de estudiante');
    }

    $studentId = $student->id;

    // OBTENER IDs DE INSTRUCTORES YA EVALUADOS CON MÁS INFORMACIÓN
    $evaluatedInstructors = \DB::table('evaluation_responses')
        ->where('student_id', $studentId)
        ->where('evaluation_session_id', $sessionId)
        ->pluck('instructor_id')
        ->toArray();

    \Log::info("📊 Estudiante {$studentId} - Instructores ya evaluados: " . json_encode($evaluatedInstructors));

    // CONTADORES PARA EL RESUMEN
    $totalInstructors = $instructors->count();
    $evaluatedCount = count($evaluatedInstructors);
    $pendingCount = $totalInstructors - $evaluatedCount;

    return view('evaluacion.student.evaluation.select-instructor', compact(
        'session', 
        'instructors',
        'evaluatedInstructors',
        'totalInstructors',
        'evaluatedCount',
        'pendingCount'
    ));
}

    /**
     * Mostrar formulario de evaluación específica CON instructor
     */
public function showEvaluation(Request $request, EvaluationSession $session)
{
    if ($session->status !== 'active' || now() < $session->start_date || now() > $session->end_date) {
        return redirect()->route('evaluacion.student.dashboard')
                        ->with('error', 'Esta evaluación no está disponible.');
    }

    // Verificar que se haya seleccionado un instructor
    $instructorId = $request->query('instructor_id');
    if (!$instructorId) {
        return redirect()->route('evaluacion.student.evaluations.select-instructor', $session->id)
                        ->with('error', 'Debes seleccionar un docente para continuar.');
    }

    // OBTENER STUDENT_ID
    $userId = auth()->id();
    $student = \DB::table('students')->where('user_id', $userId)->first();
    
    if (!$student) {
        return redirect()->route('evaluacion.student.dashboard')
                        ->with('error', 'Error: No se encontró tu registro de estudiante');
    }

    $studentId = $student->id;

    // ✅ VERIFICAR SI EL ESTUDIANTE YA EVALUÓ A ESTE INSTRUCTOR
    $alreadyEvaluated = \DB::table('evaluation_responses')
        ->where('student_id', $studentId)
        ->where('instructor_id', $instructorId)
        ->where('evaluation_session_id', $session->id)
        ->exists();

    if ($alreadyEvaluated) {
        return redirect()->route('evaluacion.student.evaluations.select-instructor', $session->id)
                        ->with('error', 'Ya has evaluado a este docente en esta sesión.');
    }

    $instructor = User::findOrFail($instructorId);

    $session->load(['questions' => function($query) {
        $query->where('status', 'active')
              ->orderBy('question_order', 'asc');
    }, 'questions.options']);

    return view('evaluacion.student.evaluation.show', compact('session', 'instructor'));
}

/**
 * Procesar envío de evaluación CON instructor
 */
public function submitEvaluation(Request $request, $sessionId)
{
    \Log::info('🎯 ===== MÉTODO SUBMIT EJECUTADO =====');
    
    try {
        // OBTENER STUDENT_ID
        $userId = auth()->id();
        $student = \DB::table('students')->where('user_id', $userId)->first();
        
        if (!$student) {
            \Log::error('❌ ERROR: No se encontró student_id para el usuario: ' . $userId);
            return redirect()->route('evaluacion.student.dashboard')
                            ->with('error', 'Error: No se encontró tu registro de estudiante');
        }

        $studentId = $student->id;

        // OBTENER INSTRUCTOR_ID CORRECTO (de la tabla instructors)
        $instructorUser = \DB::table('instructors')->where('user_id', $request->instructor_id)->first();
        
        if (!$instructorUser) {
            \Log::error('❌ ERROR: No se encontró instructor_id para el user_id: ' . $request->instructor_id);
            return redirect()->route('evaluacion.student.dashboard')
                            ->with('error', 'Error: Instructor no válido');
        }

        $instructorId = $instructorUser->id;
        \Log::info('🎯 Instructor ID encontrado: ' . $instructorId);

        // ✅ VERIFICAR SI EL ESTUDIANTE YA EVALUÓ A ESTE INSTRUCTOR (DOBLE VERIFICACIÓN)
        $alreadyEvaluated = \DB::table('evaluation_responses')
            ->where('student_id', $studentId)
            ->where('instructor_id', $instructorId)
            ->where('evaluation_session_id', $sessionId)
            ->exists();

        if ($alreadyEvaluated) {
            \Log::warning('⚠️ Intento de evaluación duplicada - Student: ' . $studentId . ', Instructor: ' . $instructorId);
            return redirect()->route('evaluacion.student.evaluations.select-instructor', $sessionId)
                            ->with('error', 'Ya has evaluado a este docente. No puedes enviar múltiples evaluaciones.');
        }

        // ✅ VALIDAR QUE HAY RESPUESTAS
        if (!$request->has('responses') || empty($request->responses)) {
            return redirect()->back()
                            ->with('error', 'Debes responder al menos una pregunta.')
                            ->withInput();
        }

        // GUARDAR RESPUESTAS
        $respuestasGuardadas = 0;
        foreach ($request->responses as $questionId => $response) {
            // ✅ VALIDAR QUE LA RESPUESTA NO ESTÉ VACÍA
            if (empty(trim($response))) {
                continue; // Saltar respuestas vacías
            }

            $result = \DB::table('evaluation_responses')->insert([
                'student_id' => $studentId,
                'instructor_id' => $instructorId,
                'evaluation_session_id' => $sessionId,
                'question_id' => $questionId,
                'text_response' => $response,
                'response_date' => now()
            ]);

            if ($result) {
                $respuestasGuardadas++;
                \Log::info("✅ Respuesta guardada - Pregunta: {$questionId}, Estudiante: {$studentId}");
            }
        }

        \Log::info("🎯 Evaluación completada - Respuestas: {$respuestasGuardadas}, Estudiante: {$studentId}, Instructor: {$instructorId}");

        return redirect()->route('evaluacion.student.dashboard')
                        ->with('success', "✅ ¡Evaluación completada! {$respuestasGuardadas} respuestas guardadas.");

    } catch (\Exception $e) {
        \Log::error('💥 ERROR: ' . $e->getMessage());
        \Log::error('💥 TRACE: ' . $e->getTraceAsString());
        return redirect()->route('evaluacion.student.dashboard')
                        ->with('error', 'Error al procesar la evaluación: ' . $e->getMessage());
    }
}
    /**
     * Evaluaciones disponibles para estudiantes
     */
    public function availableEvaluations()
    {
        $sessions = EvaluationSession::where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->withCount('questions')
                    ->get();
        
        return view('evaluacion.student.evaluation.index', compact('sessions'));
    }

    /**
     * Historial de evaluaciones completadas
     */
/**
 * Historial de evaluaciones completadas - CORREGIDO
 */
/**
 * Historial de evaluaciones completadas - VERSIÓN SIMPLE
 */
public function evaluationHistory()
{
    // OBTENER STUDENT_ID
    $userId = auth()->id();
    $student = \DB::table('students')->where('user_id', $userId)->first();
    
    if (!$student) {
        return redirect()->route('evaluacion.student.dashboard')
                        ->with('error', 'Error: No se encontró tu registro de estudiante');
    }

    $studentId = $student->id;

    // OBTENER EVALUACIONES - SOLO CON FULL_NAME
    $evaluations = EvaluationResponse::where('student_id', $studentId)
        ->select('evaluation_responses.*')
        ->with(['session', 'question'])
        ->join('instructors', 'evaluation_responses.instructor_id', '=', 'instructors.id')
        ->join('users', 'instructors.user_id', '=', 'users.id')
        ->select('evaluation_responses.*', 
                 'users.full_name as instructor_name',
                 'users.email as instructor_email')
        ->orderBy('evaluation_responses.response_date', 'desc')
        ->paginate(10);

    // AGRUPAR POR SESIÓN
    $evaluationsBySession = EvaluationResponse::where('student_id', $studentId)
        ->select('evaluation_responses.*')
        ->with(['session'])
        ->join('instructors', 'evaluation_responses.instructor_id', '=', 'instructors.id')
        ->join('users', 'instructors.user_id', '=', 'users.id')
        ->select('evaluation_responses.*', 
                 'users.full_name as instructor_name',
                 'users.email as instructor_email')
        ->get()
        ->groupBy('evaluation_session_id');

    $stats = [
        'totalEvaluations' => $evaluations->total(),
        'totalInstructors' => EvaluationResponse::where('student_id', $studentId)->distinct('instructor_id')->count(),
        'totalSessions' => EvaluationResponse::where('student_id', $studentId)->distinct('evaluation_session_id')->count(),
    ];

    return view('evaluacion.student.evaluation.history', compact('evaluations', 'stats', 'evaluationsBySession'));
}

    // ============================================
    // NUEVOS MÉTODOS PARA INSTRUCTOR
    // ============================================

    /**
     * Dashboard del instructor
     */
/**
 * Dashboard del instructor
 */
public function instructorDashboard()
{
    // OBTENER EL INSTRUCTOR_ID CORRECTO
    $user = auth()->user();
    $instructor = \DB::table('instructors')->where('user_id', $user->id)->first();
    
    if (!$instructor) {
        return redirect()->route('evaluacion.dashboard')
                        ->with('error', 'Error: No se encontró tu registro de instructor');
    }

    $instructorId = $instructor->id;

    // OBTENER EVALUACIONES RECIENTES
    $recentEvaluations = EvaluationResponse::where('instructor_id', $instructorId)
        ->with(['student', 'session', 'question', 'question.options'])
        ->orderBy('response_date', 'desc')
        ->take(5)
        ->get();

    // CALCULAR PROMEDIO - BUSCAR POR ID DE OPCIÓN
    $scaleResponses = EvaluationResponse::with(['question', 'question.options'])
        ->where('instructor_id', $instructorId)
        ->whereHas('question', function($query) {
            $query->where('question_type', 'scale_1_5');
        })
        ->get();

    $totalPuntos = 0;
    $totalRespuestas = 0;
    $puntuaciones = [1 => 5, 2 => 4, 3 => 3, 4 => 2, 5 => 1];

    foreach ($scaleResponses as $response) {
        // El text_response contiene el ID de la opción seleccionada
        if (!empty($response->text_response) && is_numeric($response->text_response)) {
            $optionId = (int)$response->text_response;
            
            // Buscar la opción por ID
            $selectedOption = EvaluationQuestionOption::find($optionId);
            
            if ($selectedOption && isset($puntuaciones[$selectedOption->option_value])) {
                $valorEscala = $selectedOption->option_value;
                $totalPuntos += $puntuaciones[$valorEscala];
                $totalRespuestas++;
                
                \Log::info("✅ Respuesta ID: {$response->id}, Option ID: {$optionId}, Valor: {$valorEscala}, Puntos: {$puntuaciones[$valorEscala]}");
            } else {
                \Log::warning("⚠️ Opción no encontrada - Response ID: {$response->id}, Option ID: {$optionId}");
            }
        }
    }

    $averageRating = $totalRespuestas > 0 ? round($totalPuntos / $totalRespuestas, 1) : 0;

    \Log::info("📊 RESUMEN - Total puntos: {$totalPuntos}, Respuestas escala: {$totalRespuestas}, Promedio: {$averageRating}");

        // AGRUPAR EVALUACIONES POR SESIÓN (SECCIÓN)
    $evaluationsBySession = $recentEvaluations->groupBy('evaluation_session_id');
    // ESTADÍSTICAS BÁSICAS
    $stats = [
        'totalEvaluations' => EvaluationResponse::where('instructor_id', $instructorId)->count(),
        'totalStudents' => EvaluationResponse::where('instructor_id', $instructorId)->distinct('student_id')->count(),
        'averageRating' => $averageRating,
        'recentEvaluations' => $recentEvaluations
    ];

    return view('evaluacion.instructor.dashboard', compact('stats'));
}

   /**
 * Resultados generales del instructor
 */
public function instructorResults()
{
    // OBTENER EL INSTRUCTOR_ID CORRECTO
    $user = auth()->user();
    $instructor = \DB::table('instructors')->where('user_id', $user->id)->first();
    
    if (!$instructor) {
        return redirect()->route('evaluacion.dashboard')
                        ->with('error', 'Error: No se encontró tu registro de instructor');
    }

    $instructorId = $instructor->id;

    $evaluations = EvaluationResponse::where('instructor_id', $instructorId)
        ->with(['student', 'session', 'question'])
        ->orderBy('response_date', 'desc')
        ->paginate(10);

    return view('evaluacion.instructor.results.index', compact('evaluations'));
}

    /**
     * Resultados por sesión específica
     */
    public function instructorSessionResults($sessionId)
    {
        $instructorId = auth()->id();
        
        $session = EvaluationSession::findOrFail($sessionId);
        $evaluations = EvaluationResponse::where('instructor_id', $instructorId)
            ->where('evaluation_session_id', $sessionId)
            ->with(['student', 'question'])
            ->orderBy('response_date', 'desc')
            ->get();

        return view('evaluacion.instructor.results.index', compact('session', 'evaluations'));
    }

    /**
     * Detalle del instructor (sus propias estadísticas)
     */
    public function instructorDetail()
{
    $user = auth()->user();
    $instructor = \DB::table('instructors')->where('user_id', $user->id)->first();
    
    if (!$instructor) {
        return redirect()->route('evaluacion.dashboard')
                        ->with('error', 'Error: No se encontró tu registro de instructor');
    }

    $instructorId = $instructor->id;

    // Obtener todas las evaluaciones con relaciones completas
    $allEvaluations = EvaluationResponse::where('instructor_id', $instructorId)
        ->with(['question', 'question.options', 'session'])
        ->get();

    // Cálculos básicos
    $totalEvaluations = $allEvaluations->count();
    $totalStudents = $allEvaluations->unique('student_id')->count();
    $totalSessions = $allEvaluations->unique('evaluation_session_id')->count();

    // Calcular promedio REAL y distribución
    $scaleResponses = EvaluationResponse::with(['question', 'question.options'])
        ->where('instructor_id', $instructorId)
        ->whereHas('question', function($query) {
            $query->where('question_type', 'scale_1_5');
        })
        ->get();

    $totalPuntos = 0;
    $totalRespuestas = 0;
    $puntuaciones = [1 => 5, 2 => 4, 3 => 3, 4 => 2, 5 => 1];
    $ratingDistribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

    foreach ($scaleResponses as $response) {
        if (is_numeric($response->text_response)) {
            $optionId = (int)$response->text_response;
            $option = \App\Models\EvaluationQuestionOption::find($optionId);
            
            if ($option && isset($puntuaciones[$option->option_value])) {
                $valor = $option->option_value;
                $ratingDistribution[$valor]++;
                $totalPuntos += $puntuaciones[$valor];
                $totalRespuestas++;
            }
        }
    }

    $averageRating = $totalRespuestas > 0 ? round($totalPuntos / $totalRespuestas, 1) : 0;

    // Calcular porcentajes
    $positiveCount = $ratingDistribution[1] + $ratingDistribution[2];
    $neutralCount = $ratingDistribution[3];
    $negativeCount = $ratingDistribution[4] + $ratingDistribution[5];
    
    $positivePercentage = $totalRespuestas > 0 ? round(($positiveCount / $totalRespuestas) * 100, 1) : 0;
    $neutralPercentage = $totalRespuestas > 0 ? round(($neutralCount / $totalRespuestas) * 100, 1) : 0;
    $negativePercentage = $totalRespuestas > 0 ? round(($negativeCount / $totalRespuestas) * 100, 1) : 0;

    // ============================================
    // NUEVO: CALCULAR DESEMPEÑO POR CATEGORÍA
    // ============================================
    $bestQuestions = collect([]);
    $worstQuestions = collect([]);

    if ($totalRespuestas > 0) {
        // Agrupar evaluaciones por pregunta
        $evaluationsByQuestion = $allEvaluations->where('question.question_type', 'scale_1_5')
            ->groupBy('question_id');

        $questionStats = [];
        
        foreach ($evaluationsByQuestion as $questionId => $questionEvaluations) {
            $question = $questionEvaluations->first()->question;
            $questionPuntos = 0;
            $questionRespuestas = 0;
            
            foreach ($questionEvaluations as $eval) {
                if (is_numeric($eval->text_response)) {
                    $optionId = (int)$eval->text_response;
                    $option = \App\Models\EvaluationQuestionOption::find($optionId);
                    
                    if ($option && isset($puntuaciones[$option->option_value])) {
                        $questionPuntos += $puntuaciones[$option->option_value];
                        $questionRespuestas++;
                    }
                }
            }
            
            if ($questionRespuestas > 0) {
                $questionAverage = round($questionPuntos / $questionRespuestas, 1);
                $questionStats[] = (object)[
                    'question_text' => $question->question_text,
                    'average_rating' => $questionAverage,
                    'response_count' => $questionRespuestas
                ];
            }
        }
        
        // Ordenar y tomar las mejores y peores
        if (count($questionStats) > 0) {
            $sortedQuestions = collect($questionStats)->sortByDesc('average_rating');
            $bestQuestions = $sortedQuestions->take(2);
            $worstQuestions = $sortedQuestions->take(-2)->reverse();
        }
    }

    // ============================================
    // NUEVO: CALCULAR EVOLUCIÓN TEMPORAL
    // ============================================
    $monthlyProgress = [];

    if ($allEvaluations->count() > 0) {
        // Agrupar por mes
        $evaluationsByMonth = $allEvaluations->where('question.question_type', 'scale_1_5')
            ->groupBy(function($eval) {
                return $eval->response_date->format('Y-m');
            });

        foreach ($evaluationsByMonth as $month => $monthEvaluations) {
            $monthPuntos = 0;
            $monthRespuestas = 0;
            
            foreach ($monthEvaluations as $eval) {
                if (is_numeric($eval->text_response)) {
                    $optionId = (int)$eval->text_response;
                    $option = \App\Models\EvaluationQuestionOption::find($optionId);
                    
                    if ($option && isset($puntuaciones[$option->option_value])) {
                        $monthPuntos += $puntuaciones[$option->option_value];
                        $monthRespuestas++;
                    }
                }
            }
            
            if ($monthRespuestas > 0) {
                $monthAverage = round($monthPuntos / $monthRespuestas, 1);
                $monthName = \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y');
                
                $monthlyProgress[$monthName] = [
                    'average' => $monthAverage,
                    'evaluations' => $monthRespuestas
                ];
            }
        }
    }

    // ============================================
    // GENERAR RECOMENDACIONES
    // ============================================
    $recommendations = [];
    
    if ($totalRespuestas == 0) {
        $recommendations[] = "Aún no hay suficientes evaluaciones para generar recomendaciones";
    } else {
        if ($averageRating >= 4.0) {
            $recommendations[] = "Excelente desempeño! Los estudiantes valoran positivamente tu trabajo";
        } elseif ($averageRating >= 3.0) {
            $recommendations[] = "Buen desempeño. Hay oportunidades para mejorar en algunas áreas";
        } else {
            $recommendations[] = "Considera solicitar feedback específico para identificar áreas de mejora";
        }

        if ($positivePercentage > 70) {
            $recommendations[] = "La mayoría de estudiantes están satisfechos con tu enseñanza";
        }
        if ($negativePercentage > 20) {
            $recommendations[] = "Algunos estudiantes muestran desacuerdo, podrías revisar tu enfoque";
        }
        if ($neutralPercentage > 40) {
            $recommendations[] = "Muchos estudiantes se mantienen neutrales, considera estrategias para aumentar el engagement";
        }

        // Recomendaciones basadas en preguntas específicas
        if ($bestQuestions->count() > 0) {
            $bestQuestion = $bestQuestions->first();
            $recommendations[] = "Destacas en: '{$bestQuestion->question_text}'";
        }
        
        if ($worstQuestions->count() > 0) {
            $worstQuestion = $worstQuestions->first();
            if ($worstQuestion->average_rating < 3.0) {
                $recommendations[] = "Podrías mejorar en: '{$worstQuestion->question_text}'";
            }
        }
    }

    // ============================================
    // PREPARAR DATOS FINALES
    // ============================================
    $stats = [
        'totalEvaluations' => $totalEvaluations,
        'totalStudents' => $totalStudents,
        'totalSessions' => $totalSessions,
        'averageRating' => $averageRating,
        'positivePercentage' => $positivePercentage,
        'neutralPercentage' => $neutralPercentage,
        'negativePercentage' => $negativePercentage,
        'recommendations' => $recommendations,
        'ratingDistribution' => $ratingDistribution,
        'totalScaleResponses' => $totalRespuestas,
        'bestQuestions' => $bestQuestions,
        'worstQuestions' => $worstQuestions,
        'monthlyProgress' => $monthlyProgress
    ];

    \Log::info("📊 Stats finales - Mejores preguntas: " . $bestQuestions->count() . ", Progreso mensual: " . count($monthlyProgress));

    return view('evaluacion.instructor.results.detail', compact('stats'));
}

    // ============================================
    // MÉTODOS EXISTENTES (MANTIENE IGUAL)
    // ============================================

    private function handleUnknownRole($user)
    {
        $primaryRole = method_exists($user, 'getPrimaryRole') ? $user->getPrimaryRole() : ($user->role ?? 'student');
        
        switch ($primaryRole) {
            case 'admin':
                return redirect()->route('evaluacion.admin.dashboard');
            case 'student':
                return redirect()->route('evaluacion.student.dashboard');
            case 'instructor':
                return redirect()->route('evaluacion.instructor.dashboard');
            default:
                return view('evaluacion.student.dashboard')->with('warning', 'Rol no específico asignado.');
        }
    }

    private function fallbackRoleRedirect($user)
    {
        $role = $user->getAttributes()['role'] ?? 'student';
        $cleanRole = $this->cleanRoleValue($role);
        
        switch ($cleanRole) {
            case 'admin':
                return redirect()->route('evaluacion.admin.dashboard');
            case 'student':
                return redirect()->route('evaluacion.student.dashboard');
            case 'instructor':
                return redirect()->route('evaluacion.instructor.dashboard');
            default:
                return view('evaluacion.dashboard')->with('error', 'Error al determinar el rol del usuario.');
        }
    }

    private function cleanRoleValue($role)
    {
        if (is_array($role)) {
            return $role[0] ?? 'student';
        }
        
        if (is_string($role)) {
            $clean = trim($role, '"\'');
            if (strpos($clean, '[') === 0) {
                $decoded = json_decode($clean, true);
                return is_array($decoded) ? ($decoded[0] ?? 'student') : 'student';
            }
            return $clean;
        }
        
        return 'student';
    }

/**
 * Reportes y resultados - Versión mejorada
 */
public function reports()
{
    try {
        // OBTENER SESIONES CON ESTADÍSTICAS DETALLADAS
        $sessions = EvaluationSession::withCount([
            'questions',
            'responses as evaluations_count'
        ])->latest()->get();

        // CALCULAR ESTADÍSTICAS GLOBALES
        $totalSessions = $sessions->count();
        $totalEvaluations = EvaluationResponse::count();
        $averageRating = EvaluationResponse::whereNotNull('rating')->avg('rating') ?? 0;

        // DEBUG DETALLADO
        \Log::info("📊 REPORTES CALCULADOS:");
        \Log::info("Total Sesiones: " . $totalSessions);
        \Log::info("Total Evaluaciones: " . $totalEvaluations);
        \Log::info("Rating Promedio: " . $averageRating);
        
        foreach($sessions as $session) {
            \Log::info("Sesión {$session->id}: {$session->questions_count} preguntas, {$session->evaluations_count} evaluaciones");
        }

        return view('evaluacion.admin.reports.index', [
            'sessions' => $sessions,
            'totalSessions' => $totalSessions,
            'totalEvaluations' => $totalEvaluations,
            'averageRating' => number_format($averageRating, 1)
        ]);

    } catch (\Exception $e) {
        \Log::error("💥 ERROR en reports(): " . $e->getMessage());
        
        return view('evaluacion.admin.reports.index', [
            'sessions' => collect(),
            'totalSessions' => 0,
            'totalEvaluations' => 0,
            'averageRating' => 0
        ]);
    }
}
}