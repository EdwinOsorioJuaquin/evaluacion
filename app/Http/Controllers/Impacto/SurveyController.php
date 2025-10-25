<?php

namespace App\Http\Controllers\Impacto;

use App\Models\EmpSurvey;
use App\Models\EmpQuestion;
use App\Models\EmpQuestionOption;
use App\Models\EmpGraduateSurvey;
use App\Models\Graduate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class SurveyController extends Controller
{
    /**
     * Guardar una nueva encuesta con sus preguntas y opciones
     */
    public function store(Request $request)
    {
        // Validación básica
        $validator = Validator::make($request->all(), [
            'survey_name' => 'required|string|max:255',
            'survey_description' => 'nullable|string',
            'is_active' => 'boolean',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.question_type' => 'required|in:text,number,option,date',
            'questions.*.is_required' => 'boolean',
        ], [
            'survey_name.required' => 'El nombre de la encuesta es obligatorio',
            'questions.required' => 'Debes agregar al menos una pregunta',
            'questions.*.question_text.required' => 'Todas las preguntas deben tener texto',
            'questions.*.question_type.required' => 'Todas las preguntas deben tener un tipo',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Por favor corrige los errores del formulario.');
        }

        try {
            // Iniciar transacción
            DB::beginTransaction();

            // 1. Crear la encuesta principal
            $survey = EmpSurvey::create([
                'name' => $request->survey_name,
                'description' => $request->survey_description,
                'is_active' => $request->has('is_active') ? true : false,
            ]);

            // 2. Crear las preguntas
            foreach ($request->questions as $index => $questionData) {
                $question = EmpQuestion::create([
                    'survey_id' => $survey->id,
                    'question_text' => $questionData['question_text'],
                    'question_type' => $questionData['question_type'],
                    'question_order' => $index + 1,
                    'is_required' => isset($questionData['is_required']) ? true : false,
                ]);

                // 3. Si la pregunta es de tipo "option", crear las opciones
                if ($questionData['question_type'] === 'option' && isset($questionData['options'])) {
                    foreach ($questionData['options'] as $optionIndex => $optionText) {
                        if (!empty($optionText)) {
                            EmpQuestionOption::create([
                                'question_id' => $question->id,
                                'option_text' => $optionText,
                                'option_value' => $optionText,
                                'option_order' => $optionIndex + 1,
                            ]);
                        }
                    }
                }
            }

            // Confirmar transacción
            DB::commit();

            return redirect()->route('dashboard')
                ->with('success', '✅ Encuesta creada exitosamente con ' . count($request->questions) . ' preguntas.');

        } catch (\Exception $e) {
            // Revertir cambios si hay error
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', '❌ Error al crear la encuesta: ' . $e->getMessage());
        }
    }

    /**
     * Obtener datos para el formulario de asignación
     */
    public function getAssignmentData()
    {
        try {
            // 1. Obtener encuestas activas
            $surveys = EmpSurvey::where('is_active', true)
                ->orderBy('id', 'desc')
                ->get(['id', 'name', 'description']);

            // 2. Obtener graduados con sus datos de usuario y programa
            $graduates = Graduate::with(['user:id,full_name,email', 'program:id,name'])
                ->where('state', 'graduated')
                ->orderBy('graduation_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'surveys' => $surveys,
                'graduates' => $graduates
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Asignar encuesta a graduados seleccionados
     */
    public function assignSurvey(Request $request)
    {
        // Validación
        $validator = Validator::make($request->all(), [
            'survey_id' => 'required|exists:emp_surveys,id',
            'graduate_ids' => 'required|array|min:1',
            'graduate_ids.*' => 'exists:graduates,id',
        ], [
            'survey_id.required' => 'Debes seleccionar una encuesta',
            'survey_id.exists' => 'La encuesta seleccionada no existe',
            'graduate_ids.required' => 'Debes seleccionar al menos un graduado',
            'graduate_ids.min' => 'Debes seleccionar al menos un graduado',
            'graduate_ids.*.exists' => 'Uno o más graduados seleccionados no existen',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Por favor corrige los errores del formulario.');
        }

        try {
            DB::beginTransaction();

            $surveyId = $request->survey_id;
            $graduateIds = $request->graduate_ids;
            
            $assignedCount = 0;
            $alreadyAssignedCount = 0;
            $alreadyAssignedNames = [];

            foreach ($graduateIds as $graduateId) {
                // Verificar si ya está asignada
                $exists = EmpGraduateSurvey::where('graduate_id', $graduateId)
                    ->where('survey_id', $surveyId)
                    ->exists();

                if ($exists) {
                    $alreadyAssignedCount++;
                    // Obtener nombre del graduado para el mensaje
                    $graduate = Graduate::with('user')->find($graduateId);
                    if ($graduate && $graduate->user) {
                        $alreadyAssignedNames[] = $graduate->user->full_name;
                    }
                    continue;
                }

                // Crear asignación
                EmpGraduateSurvey::create([
                    'graduate_id' => $graduateId,
                    'survey_id' => $surveyId,
                    'status' => 'pending',
                    'assigned_date' => now(),
                ]);

                $assignedCount++;
            }

            DB::commit();

            // Construir mensaje de éxito
            $message = "✅ Encuesta asignada exitosamente a {$assignedCount} graduado(s).";
            
            if ($alreadyAssignedCount > 0) {
                $message .= " ⚠️ {$alreadyAssignedCount} graduado(s) ya tenían esta encuesta asignada";
                if (count($alreadyAssignedNames) > 0) {
                    $message .= ": " . implode(', ', $alreadyAssignedNames);
                }
            }

            return redirect()->route('dashboard')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', '❌ Error al asignar la encuesta: ' . $e->getMessage());
        }
    }

        /**
     * Obtener todas las encuestas para gestionar
     */
    public function getSurveys()
    {
        try {
            $surveys = EmpSurvey::withCount(['questions', 'graduateSurveys'])
                ->orderBy('id', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'surveys' => $surveys
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener encuestas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener detalles de una encuesta específica
     */
    public function getSurveyDetails($id)
    {
        try {
            $survey = EmpSurvey::with(['questions.options' => function($query) {
                $query->orderBy('option_order');
            }])->findOrFail($id);

            // Ordenar preguntas por question_order
            $survey->questions = $survey->questions->sortBy('question_order')->values();

            return response()->json([
                'success' => true,
                'survey' => $survey
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener detalles: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar encuesta (solo nombre, descripción y estado)
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'El nombre de la encuesta es obligatorio',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('error', 'Por favor corrige los errores del formulario.');
        }

        try {
            $survey = EmpSurvey::findOrFail($id);

            $survey->update([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->has('is_active') ? true : false,
            ]);

            return redirect()->route('dashboard')
                ->with('success', '✅ Encuesta actualizada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '❌ Error al actualizar la encuesta: ' . $e->getMessage());
        }
    }

public function destroy($id)
{
    try {
        DB::beginTransaction();

        $survey = EmpSurvey::findOrFail($id);

        // Verificar si tiene asignaciones
        $assignmentsCount = EmpGraduateSurvey::where('survey_id', $id)->count();

        if ($assignmentsCount > 0) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar. Esta encuesta tiene ' . $assignmentsCount . ' asignación(es) a graduados.'
            ], 400);
        }

        // Eliminar encuesta (las preguntas y opciones se eliminan en cascada)
        $survey->delete();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Encuesta eliminada exitosamente.'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Error al eliminar la encuesta: ' . $e->getMessage()
        ], 500);
    }
}

public function getReportsData()
{
    try {
        // Obtener TODAS las encuestas con su conteo de respuestas completadas
        $surveys = EmpSurvey::withCount([
            'graduateSurveys as total_assigned' => function($query) {
                // Total de asignaciones
            },
            'graduateSurveys as completed_count' => function($query) {
                $query->where('status', 'completed');
            }
        ])
        ->with('questions') // Incluir preguntas para saber cuántas tiene
        ->orderBy('id', 'desc')
        ->get()
        ->map(function($survey) {
            return [
                'id' => $survey->id,
                'name' => $survey->name,
                'description' => $survey->description,
                'is_active' => $survey->is_active,
                'questions_count' => $survey->questions->count(),
                'total_assigned' => $survey->total_assigned ?? 0,
                'completed_count' => $survey->completed_count ?? 0,
                'has_responses' => ($survey->completed_count ?? 0) > 0
            ];
        });

        return response()->json([
            'success' => true,
            'surveys' => $surveys,
            'total' => $surveys->count()
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener datos: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Generar reporte PDF de una encuesta
 */
public function downloadReport($surveyId)
{
    try {
        // Obtener la encuesta con todas sus relaciones
        $survey = EmpSurvey::with([
            'questions.options',
            'graduateSurveys' => function($query) {
                $query->where('status', 'completed')
                      ->with([
                          'graduate.user',
                          'graduate.program',
                          'responses.question',
                          'responses.option'
                      ]);
            }
        ])->findOrFail($surveyId);

        // Verificar que tenga respuestas
        if ($survey->graduateSurveys->count() === 0) {
            return redirect()->back()->with('error', 'Esta encuesta no tiene respuestas completadas.');
        }

        // Preparar datos para el PDF
        $data = [
            'survey' => $survey,
            'totalResponses' => $survey->graduateSurveys->count(),
            'generatedDate' => now()->format('d/m/Y H:i'),
        ];

        // Generar PDF
        $pdf = Pdf::loadView('reports.survey-pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        // Nombre del archivo
        $fileName = 'Reporte_' . str_replace(' ', '_', $survey->name) . '_' . date('Y-m-d') . '.pdf';

        // Descargar
        return $pdf->download($fileName);

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error al generar el reporte: ' . $e->getMessage());
    }
}

/**
 * Vista previa del reporte (opcional)
 */
public function previewReport($surveyId)
{
    try {
        $survey = EmpSurvey::with([
            'questions.options',
            'graduateSurveys' => function($query) {
                $query->where('status', 'completed')
                      ->with([
                          'graduate.user',
                          'graduate.program',
                          'responses.question',
                          'responses.option'
                      ]);
            }
        ])->findOrFail($surveyId);

        if ($survey->graduateSurveys->count() === 0) {
            return redirect()->back()->with('error', 'Esta encuesta no tiene respuestas completadas.');
        }

        $data = [
            'survey' => $survey,
            'totalResponses' => $survey->graduateSurveys->count(),
            'generatedDate' => now()->format('d/m/Y H:i'),
        ];

        return view('reports.survey-pdf', $data);

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
    }
}
}