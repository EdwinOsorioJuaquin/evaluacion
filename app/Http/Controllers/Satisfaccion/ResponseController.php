<?php

namespace App\Http\Controllers\Satisfaccion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Response;
use App\Models\Survey;

class ResponseController extends Controller
{
    public function showSurvey($survey_id)
    {
        $survey = Survey::with('questions')->findOrFail($survey_id);
        return view('satisfaccion.responses.survey', compact('survey'));
    }

    public function store(Request $request, $survey_id)
    {
        $survey = Survey::with('questions')->findOrFail($survey_id);
        $user = Auth::user();

        foreach ($survey->questions as $question) {
            $answer = $request->input('question_'.$question->id);
            if ($answer !== null) {
                Response::create([
                    'user_id' => $user->id,
                    'survey_id' => $survey->id,
                    'question_id' => $question->id,
                    'response_text' => $answer,
                    'response_date'=> now()

                ]);
            }
        }
        $survey->update([
        'id_satisfaction_response' => $survey->id
    ]);

        return redirect()->route('satisfaccion.dashboard.student')->with('success', 'Encuesta respondida correctamente');
    }
}
