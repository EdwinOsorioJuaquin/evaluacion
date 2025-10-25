<?php

namespace App\Http\Controllers\EvaluacionDocente\Admin;

use App\Http\Controllers\Controller;
use App\Models\EvaluationSession;
use App\Models\EvaluationQuestion;
use App\Models\EvaluationQuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    /**
     * Display a listing of the questions for a session.
     */
    public function index($sessionId)
    {
        $session = EvaluationSession::with(['questions' => function($query) {
            $query->orderBy('question_order', 'asc');
        }, 'questions.options'])->findOrFail($sessionId);
        
        return view('evaluacion.admin.questions.index', compact('session'));
    }

    /**
     * Show the form for creating a new question.
     */
    public function create($sessionId)
    {
        $session = EvaluationSession::findOrFail($sessionId);
        return view('evaluacion.admin.questions.create', compact('session'));
    }

    /**
     * Store a newly created question in storage.
     */
   public function store(Request $request, $sessionId)
{
    $request->validate([
        'question_text' => 'required|string|max:1000',
        'question_type' => 'required|in:scale_1_5,text',
        'is_required' => 'boolean',
        'options' => 'required_if:question_type,scale_1_5|array',
        'options.*.value' => 'required_if:question_type,scale_1_5|integer|between:1,5',
        'options.*.text' => 'required_if:question_type,scale_1_5|string|max:255'
    ]);

    try {
        DB::transaction(function () use ($request, $sessionId) {
            // Obtener el siguiente order (manejar caso cuando no hay preguntas)
            $maxOrder = EvaluationQuestion::where('evaluation_session_id', $sessionId)->max('question_order');
            $nextOrder = $maxOrder ? $maxOrder + 1 : 1;

            // Crear la pregunta
            $question = EvaluationQuestion::create([
                'evaluation_session_id' => $sessionId,
                'question_text' => $request->question_text,
                'question_type' => $request->question_type,
                'question_order' => $nextOrder,
                'is_required' => $request->boolean('is_required', true),
                'status' => 'active'
            ]);

            // Si es tipo escala, crear las opciones
            if ($request->question_type === 'scale_1_5' && $request->has('options')) {
                foreach ($request->options as $optionData) {
                    // Asegurarnos de que los datos existen
                    if (isset($optionData['value']) && isset($optionData['text'])) {
                        EvaluationQuestionOption::create([
                            'question_id' => $question->id,
                            'option_value' => $optionData['value'],
                            'option_text' => $optionData['text']
                        ]);
                    }
                }
            }
        });

        // CAMBIA ESTA LÍNEA - usa la misma ruta que en tu vista
        return redirect()->route('evaluacion.admin.sessions.questions.index', $sessionId)
                        ->with('success', 'Pregunta creada exitosamente.');

    } catch (\Exception $e) {
        // Debug: para ver el error real
        \Log::error('Error creating question: ' . $e->getMessage());
        
        return redirect()->back()
                        ->withInput()
                        ->with('error', 'Error al crear la pregunta: ' . $e->getMessage());
    }
}

    /**
     * Show the form for editing the specified question.
     */
    public function edit($sessionId, $questionId)
    {
        $session = EvaluationSession::findOrFail($sessionId);
        $question = EvaluationQuestion::with('options')->findOrFail($questionId);
        
        // Ordenar opciones por valor
        if ($question->options) {
            $question->options = $question->options->sortBy('option_value');
        }
        
        return view('evaluacion.admin.questions.edit', compact('session', 'question'));
    }

    /**
     * Update the specified question in storage.
     */
    public function update(Request $request, $sessionId, $questionId)
    {
        $request->validate([
            'question_text' => 'required|string|max:1000',
            'question_type' => 'required|in:scale_1_5,text',
            'is_required' => 'boolean',
            'options' => 'required_if:question_type,scale_1_5|array',
            'options.*.value' => 'required_if:question_type,scale_1_5|integer|between:1,5',
            'options.*.text' => 'required_if:question_type,scale_1_5|string|max:255'
        ]);

        try {
            DB::transaction(function () use ($request, $questionId) {
                $question = EvaluationQuestion::findOrFail($questionId);
                
                $question->update([
                    'question_text' => $request->question_text,
                    'question_type' => $request->question_type,
                    'is_required' => $request->boolean('is_required', true),
                ]);

                // Eliminar opciones existentes y crear nuevas si es tipo escala
                if ($request->question_type === 'scale_1_5') {
                    $question->options()->delete();
                    
                    foreach ($request->options as $optionData) {
                        EvaluationQuestionOption::create([
                            'question_id' => $question->id,
                            'option_value' => $optionData['value'],
                            'option_text' => $optionData['text']
                        ]);
                    }
                } else {
                    // Si cambia a tipo texto, eliminar opciones
                    $question->options()->delete();
                }
            });

            return redirect()->route('evaluacion.admin.questions.index', $sessionId)
                            ->with('success', 'Pregunta actualizada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Error al actualizar la pregunta: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified question from storage.
     */
    public function destroy($sessionId, $questionId)
    {
        try {
            $question = EvaluationQuestion::findOrFail($questionId);
            $question->delete();

            // Reordenar las preguntas restantes
            $this->reorderQuestions($sessionId);

            return redirect()->route('evaluacion.admin.questions.index', $sessionId)
                            ->with('success', 'Pregunta eliminada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->route('evaluacion.admin.questions.index', $sessionId)
                            ->with('error', 'Error al eliminar la pregunta: ' . $e->getMessage());
        }
    }

    /**
     * Update the order of questions.
     */
    public function updateOrder(Request $request, $sessionId)
    {
        $request->validate([
            'questions' => 'required|array'
        ]);

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->questions as $order => $questionId) {
                    EvaluationQuestion::where('id', $questionId)
                                    ->update(['question_order' => $order + 1]);
                }
            });

            return response()->json(['success' => true, 'message' => 'Orden actualizado exitosamente.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar el orden: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Toggle question status (active/inactive).
     */
    public function toggleStatus($sessionId, $questionId)
    {
        try {
            $question = EvaluationQuestion::findOrFail($questionId);
            $newStatus = $question->status === 'active' ? 'inactive' : 'active';
            $question->update(['status' => $newStatus]);

            return redirect()->route('evaluacion.admin.questions.index', $sessionId)
                            ->with('success', 'Estado de la pregunta actualizado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->route('evaluacion.admin.questions.index', $sessionId)
                            ->with('error', 'Error al cambiar el estado: ' . $e->getMessage());
        }
    }

    /**
     * Reorder questions after deletion.
     */
    private function reorderQuestions($sessionId)
    {
        $questions = EvaluationQuestion::where('evaluation_session_id', $sessionId)
            ->orderBy('question_order')
            ->get();

        $order = 1;
        foreach ($questions as $question) {
            $question->update(['question_order' => $order]);
            $order++;
        }
    }

    /**
     * Show question details (for modal or quick view).
     */
    public function show($sessionId, $questionId)
    {
        $question = EvaluationQuestion::with(['options', 'session'])
            ->where('evaluation_session_id', $sessionId)
            ->findOrFail($questionId);

        return response()->json([
            'success' => true,
            'question' => $question,
            'options' => $question->options
        ]);
    }

    /**
     * Clone a question.
     */
    public function clone($sessionId, $questionId)
    {
        try {
            DB::transaction(function () use ($sessionId, $questionId) {
                $originalQuestion = EvaluationQuestion::with('options')
                    ->where('evaluation_session_id', $sessionId)
                    ->findOrFail($questionId);

                // Obtener el siguiente order
                $nextOrder = EvaluationQuestion::where('evaluation_session_id', $sessionId)
                    ->max('question_order') + 1;

                // Crear nueva pregunta (clon)
                $clonedQuestion = EvaluationQuestion::create([
                    'evaluation_session_id' => $sessionId,
                    'question_text' => $originalQuestion->question_text . ' (Copia)',
                    'question_type' => $originalQuestion->question_type,
                    'question_order' => $nextOrder,
                    'is_required' => $originalQuestion->is_required,
                    'status' => $originalQuestion->status
                ]);

                // Clonar opciones si existen
                if ($originalQuestion->options->count() > 0) {
                    foreach ($originalQuestion->options as $option) {
                        EvaluationQuestionOption::create([
                            'question_id' => $clonedQuestion->id,
                            'option_value' => $option->option_value,
                            'option_text' => $option->option_text
                        ]);
                    }
                }
            });

            return redirect()->route('evaluacion.admin.questions.index', $sessionId)
                            ->with('success', 'Pregunta clonada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->route('evaluacion.admin.questions.index', $sessionId)
                            ->with('error', 'Error al clonar la pregunta: ' . $e->getMessage());
        }
    }

    /**
     * Bulk actions for questions.
     */
    public function bulkAction(Request $request, $sessionId)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'questions' => 'required|array',
            'questions.*' => 'exists:evaluation_questions,id'
        ]);

        try {
            DB::transaction(function () use ($request, $sessionId) {
                $questionIds = $request->questions;

                switch ($request->action) {
                    case 'activate':
                        EvaluationQuestion::whereIn('id', $questionIds)
                            ->update(['status' => 'active']);
                        break;

                    case 'deactivate':
                        EvaluationQuestion::whereIn('id', $questionIds)
                            ->update(['status' => 'inactive']);
                        break;

                    case 'delete':
                        EvaluationQuestion::whereIn('id', $questionIds)->delete();
                        // Reordenar después de eliminar
                        $this->reorderQuestions($sessionId);
                        break;
                }
            });

            $actionMessages = [
                'activate' => 'preguntas activadas',
                'deactivate' => 'preguntas desactivadas',
                'delete' => 'preguntas eliminadas'
            ];

            return redirect()->route('evaluacion.admin.questions.index', $sessionId)
                            ->with('success', count($request->questions) . ' ' . $actionMessages[$request->action] . ' exitosamente.');

        } catch (\Exception $e) {
            return redirect()->route('evaluacion.admin.questions.index', $sessionId)
                            ->with('error', 'Error al realizar la acción: ' . $e->getMessage());
        }
    }
}