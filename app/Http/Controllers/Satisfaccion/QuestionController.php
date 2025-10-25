<?php

namespace App\Http\Controllers\Satisfaccion;

use Illuminate\Http\Request;
use App\Models\Survey;
use App\Models\Question;

class QuestionController extends Controller
{
        // ✅ Formulario para agregar preguntas
    public function addQuestions(Survey $survey)
    {
        return view('satisfaccion.admin.surveys.add_questions', compact('survey'));
    }

    // ✅ Guardar preguntas y opciones
    public function storeQuestions(Request $request, Survey $survey)
    {
        $request->validate([
            'questions' => 'required|array',
            'questions.*.text' => 'required|string',
            'questions.*.options' => 'required|array|min:1',
            'questions.*.options.*.text' => 'required|string'
        ]);

        foreach ($request->questions as $q) {
    $question = $survey->questions()->create([
        'id_satisfaction_question' => $q['id_satisfaction_question'] ?? null,
        'type' => $q['type'] ?? null,
        'question_text' => $q['text']
    ]);

    // ✅ Solo crear opciones si el tipo es opción múltiple
    if (isset($q['type']) && $q['type'] === 'opcion_multiple') {
        foreach ($q['options'] as $opt) {
            $question->options()->create([
                'id_satisfaction_option' => random_int(1000, 9999), 
                'option_text' => $opt['text']
            ]);
        }
    }
}


        return redirect()->route('satisfaccion.admin.dashboard')->with('success', 'Encuesta y preguntas creadas correctamente.');

    }

    public function destroy($id)
{
    $question = Question::findOrFail($id);
    $survey_id = $question->id_survey;

    // 1️⃣ Eliminar respuestas relacionadas
    \DB::table('satisfaction_responses')
        ->where('id_question', $question->id)
        ->delete();

    // 2️⃣ Eliminar opciones relacionadas (si existen)
    \DB::table('satisfaction_options')
        ->where('id_question', $question->id)
        ->delete();

    // 3️⃣ Finalmente eliminar la pregunta
    $question->delete();

    // 4️⃣ Redirigir correctamente
    return redirect()->route('satisfaccion.admin.surveys.edit', ['survey' => $survey_id])
        ->with('success', 'Pregunta, opciones y respuestas eliminadas correctamente.');
}



}

