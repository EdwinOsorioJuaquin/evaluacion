<?php

namespace App\Http\Controllers\Impacto;

// ✅ MODELOS ACTUALIZADOS
use App\Models\User;
use App\Models\Program;
use App\Models\Graduate;
use App\Models\EmpSurvey;
use App\Models\EmpQuestion;
use App\Models\EmpGraduateSurvey;
use App\Models\EmpSurveyResponse;
use App\Models\EmpQuestionOption;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
           // ✅ FIX: Manejar role como array o string
            $userRole = is_array($user->role) ? ($user->role[0] ?? 'student') : trim($user->role, '"');
        
        if ($userRole === 'admin') {
            return $this->adminDashboard();
        } else {
            return $this->studentDashboard();
        }
    }

    private function adminDashboard()
    {
        try {
            // ✅ ESTADÍSTICAS ACTUALIZADAS
            $totalUsers = User::count();
            $totalGraduates = Graduate::where('state', 'graduated')->count();
            $totalSurveys = EmpSurvey::count();
            $totalPrograms = Program::count();
            
            // Estadísticas de encuestas
            $totalAssignedSurveys = EmpGraduateSurvey::count();
            $completedSurveys = EmpGraduateSurvey::where('status', 'completed')->count();
            $employmentRate = $totalAssignedSurveys > 0 ? ($completedSurveys / $totalAssignedSurveys) * 100 : 0;
            
            // ✅ Últimas encuestas completadas (NUEVA LÓGICA)
            $recentSurveys = EmpGraduateSurvey::with(['graduate.user', 'graduate.program', 'survey'])
                ->where('status', 'completed')
                ->orderBy('completed_at', 'desc')
                ->take(5)
                ->get();

            // ✅ Programas con más graduados
            $topPrograms = Program::withCount(['graduates' => function($query) {
                $query->where('state', 'graduated');
            }])->orderBy('graduates_count', 'desc')->take(5)->get();

        } catch (\Exception $e) {
            // Valores por defecto en caso de error
            $totalUsers = $totalGraduates = $totalSurveys = $totalPrograms = 0;
            $employmentRate = 0;
            $recentSurveys = collect([]);
            $topPrograms = collect([]);
        }

        return view('impacto.dashboard', compact(
            'totalUsers', 
            'totalGraduates', 
            'totalSurveys', 
            'totalPrograms',
            'employmentRate',
            'recentSurveys',
            'topPrograms'
        ));
    }

    private function studentDashboard()
    {
            $user = Auth::user();
            
            // ✅ Obtener programas graduados del usuario
            $graduates = Graduate::where('user_id', $user->id)
                ->where('state', 'graduated')
                ->with('program')
                ->get();

            // ✅ Obtener encuestas asignadas con información completa
            $assignedSurveys = collect([]);
            $completedSurveys = 0;
            $pendingSurveys = 0;
            $inProgressSurveys = 0;

            if ($graduates->count() > 0) {
                $graduateIds = $graduates->pluck('id');
                
                $assignedSurveys = EmpGraduateSurvey::whereIn('graduate_id', $graduateIds)
                    ->with(['survey', 'graduate.program'])
                    ->orderBy('assigned_date', 'desc')
                    ->get();

                $completedSurveys = $assignedSurveys->where('status', 'completed')->count();
                $pendingSurveys = $assignedSurveys->where('status', 'pending')->count();
                $inProgressSurveys = $assignedSurveys->where('status', 'in_progress')->count();
            }

            return view('impacto.dashboard', compact(
                'graduates', 
                'assignedSurveys',
                'completedSurveys', 
                'pendingSurveys',
                'inProgressSurveys'
            ));
    }

    // ✅ MÉTODO ACTUALIZADO para mostrar formulario
    public function showSurveyForm($graduateSurveyId)
    {
        $user = Auth::user();
        
        // Verificar que la encuesta asignada pertenece al usuario
        $graduateSurvey = EmpGraduateSurvey::where('id', $graduateSurveyId)
            ->with(['graduate', 'survey.questions.options'])
            ->firstOrFail();

        // Verificar que el graduado pertenece al usuario
        if ($graduateSurvey->graduate->user_id !== $user->id) {
            abort(403, 'No tienes permiso para acceder a esta encuesta.');
        }

        // Verificar si ya está completada
        if ($graduateSurvey->status === 'completed') {
            return redirect()->route('impacto.dashboard')->with('error', 'Ya completaste esta encuesta.');
        }

        // Actualizar a "en progreso" si aún está pendiente
        if ($graduateSurvey->status === 'pending') {
            $graduateSurvey->update([
                'status' => 'in_progress',
                'started_at' => now()
            ]);
        }

        $survey = $graduateSurvey->survey;
        $questions = $survey->questions->sortBy('question_order');

        return view('impacto.survey-form', compact('graduateSurvey', 'survey', 'questions'));
    }

    // ✅ MÉTODO ACTUALIZADO para enviar encuesta
    public function submitSurvey(Request $request, $graduateSurveyId)
    {
        $user = Auth::user();
        
        // Verificar que la encuesta asignada pertenece al usuario
        $graduateSurvey = EmpGraduateSurvey::where('id', $graduateSurveyId)
            ->with(['graduate', 'survey.questions'])
            ->firstOrFail();

        if ($graduateSurvey->graduate->user_id !== $user->id) {
            abort(403, 'No tienes permiso.');
        }

        // Verificar si ya está completada
        if ($graduateSurvey->status === 'completed') {
            return redirect()->route('impacto.dashboard')->with('error', 'Ya completaste esta encuesta.');
        }

        // ✅ GUARDAR RESPUESTAS CON NUEVA ESTRUCTURA
        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'question_')) {
                $questionId = str_replace('question_', '', $key);
                
                // Buscar la pregunta
                $question = $graduateSurvey->survey->questions->firstWhere('id', $questionId);
                
                if ($question && !empty($value)) {
                    $responseData = [
                        'graduate_survey_id' => $graduateSurvey->id,
                        'question_id' => $questionId,
                        'responded_at' => now(),
                    ];

                    // Asignar respuesta según el tipo
                    if ($question->question_type === 'number') {
                        $responseData['number_response'] = $value;
                    } elseif ($question->question_type === 'option') {
                        $responseData['option_id'] = $value;
                    } elseif ($question->question_type === 'date') {
                        $responseData['date_response'] = $value;
                    } else {
                        $responseData['text_response'] = $value;
                    }

                    EmpSurveyResponse::create($responseData);
                }
            }
        }

        // ✅ Marcar como completada
        $graduateSurvey->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        return redirect()->route('impacto.dashboard')->with('success', '¡Encuesta completada exitosamente!');
    }

    
}