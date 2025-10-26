<?php
namespace App\Http\Controllers\Satisfaccion;

use App\Models\SurveyCategory;
use App\Models\Survey;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SurveyReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class SurveyController extends Controller
{
    // ✅ Listado de encuestas en admin
    public function adminIndex()
{
    $surveys = Survey::all();
    return view('satisfaccion.admin.surveys.index', compact('surveys'));
}


    // ✅ Crear encuesta
    public function create()
    {
        $categories = SurveyCategory::all();
        return view('satisfaccion.admin.surveys.create', compact('categories'));
    }

    // ✅ Guardar nueva encuesta
    // ✅ Guardar nueva encuesta
public function store(Request $request)
{
    // Validación
    $request->validate([
        'qualification' => 'required|string|max:255',
        'description'   => 'nullable|string',
        'state'         => 'required|string',
        'id_category'   => 'required|integer'
    ]);

    // Crear encuesta (sin el id_satisfaction_survey aún)
    $survey = Survey::create([
        'qualification' => $request->qualification,
        'description'   => $request->description,
        'state'         => $request->state,
        'id_category'   => $request->id_category,
        'creation_date' => now()
    ]);

    // Asignar el mismo id al campo id_satisfaction_survey
    $survey->update([
        'id_satisfaction_survey' => $survey->id
    ]);

    return redirect()->route('satisfaccion.admin.surveys.add_Questions', $survey->id)
        ->with('success', 'Encuesta creada correctamente.');
}

    // ✅ Editar encuesta
    public function edit(Survey $survey)
    {
        $categories = SurveyCategory::all();
        return view('satisfaccion.admin.surveys.edit', compact('survey', 'categories'));
    }

    // ✅ Actualizar encuesta
    public function update(Request $request, Survey $survey)
    {
        $request->validate([
            'qualification' => 'required|string|max:255',
            'description'   => 'nullable|string',
            'state'         => 'required|string',
            'id_category'   => 'required|integer|exists:satisfaction_survey_categories,id'
        ]);

        $survey->update([
            'qualification' => $request->qualification,
            'description'   => $request->description,
            'state'         => $request->state,
            'id_category'   => $request->id_category
        ]);

        return redirect()->route('satisfaccion.admin.surveys.index')->with('success', 'Encuesta actualizada correctamente.');
    }

    // ✅ Eliminar encuesta
   public function destroy(Survey $survey)
{
    DB::transaction(function() use ($survey) {
        DB::table('surveys_assigned')->where('id_survey', $survey->id)->delete();
        DB::table('survey_reports')->where('id_survey', $survey->id)->delete();

        foreach ($survey->questions as $question) {
            // 🧽 PRIMERO ELIMINAR RESPUESTAS
            $question->responses()->delete();

            // 🧹 LUEGO ELIMINAR OPCIONES
            $question->options()->delete();
        }

        // 🔥 Luego eliminar las preguntas
        $survey->questions()->delete();

        // 🗑 Finalmente eliminar la encuesta
        $survey->delete();
    });

    return redirect()->route('satisfaccion.admin.dashboard')->with('success', 'Encuesta eliminada correctamente.');
}

    // ✅ Listado para estudiantes
    public function studentIndex()
{
    // Obtener el modelo Student relacionado con el usuario autenticado
    $user = auth()->user();
    echo $user->id;
    $student = $user->student ?? null;

    // Si no existe registro en students, podrías redirigir o mostrar avisos en la vista
    $studentId = $student ? $student->id : null;

    $surveys = \App\Models\Survey::where('state', 'Activa')
        ->with(['questions.options', 'questions.responses' => function($q) use ($studentId) {
            // usamos el id de students, no el id de users
            if ($studentId) {
                $q->where('id_student', $studentId);
            } else {
                // si no hay studentId, no traer respuestas (evita errores)
                $q->whereRaw('1 = 0');
            }
        }])
        ->get()
        ->map(function ($survey) {
            // Verifica si hay al menos una respuesta asociada al estudiante
            $survey->answered = $survey->questions->flatMap->responses->isNotEmpty();
            return $survey;
        });

    return view('satisfaccion.student.dashboard', compact('surveys', 'studentId'));
}
    // ✅ Mostrar encuesta
    public function show($id)
{
    $survey = Survey::with('questions.options')->findOrFail($id);

    if ($survey->state !== 'Activa') {
        return redirect()->route('satisfaccion.student.dashboard')
                         ->with('error', 'Encuesta no disponible.');
    }

    return view('satisfaccion.surveys.show', compact('survey'));
}


    // ✅ Guardar respuestas del estudiante
    // ✅ Guardar respuestas del estudiante
public function submit(Request $request, $id)
{
    $user = auth()->user();
    $student = $user->student ?? null;

    if (!$student) {
        return redirect()->route('satisfaccion.student.dashboard')
            ->with('error', 'No se encontró su registro de estudiante. Contacte al administrador.');
    }

    $studentId = $student->id;

    // Traemos la encuesta y las respuestas del student (si existen)
    $survey = \App\Models\Survey::with(['questions.responses' => function($q) use ($studentId) {
        $q->where('id_student', $studentId);
    }])->findOrFail($id);

    // Evitar doble envío
    $alreadyAnswered = $survey->questions->flatMap->responses->isNotEmpty();
    if ($alreadyAnswered) {
        return redirect()->route('satisfaccion.student.dashboard')
            ->with('info', 'Ya has respondido esta encuesta anteriormente.');
    }

    // Guardar respuestas
    foreach ($survey->questions as $question) {
        if (isset($request->answers[$question->id])) {
            $responseText = $request->answers[$question->id];
            $optionId = $request->input('option_'.$question->id) ?? null;

            $question->responses()->create([
                'response_text' => $responseText,
                'id_opcion' => $optionId,
                'id_student' => $studentId,
                'response_date' => now(),
                'id_satisfaction_response' => random_int(1000, 9999),
            ]);
        }
    }

    // 🔹 Aquí está lo que faltaba: redirigir con mensaje de éxito
    return redirect()->route('satisfaccion.student.dashboard')
                     ->with('success', 'Encuesta enviada correctamente.');
}

}

