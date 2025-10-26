<?php

namespace App\Http\Controllers\Satisfaccion;

use Illuminate\Http\Request;
use App\Models\Survey;
use App\Models\Response;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\SurveyExport;

class ReportController extends Controller
{
    // Lista de encuestas para reportes
    public function index()
    {
        $surveys = Survey::all();
        return view('satisfaccion.reports.index', compact('surveys'));
    }

    // Vista del reporte
    public function generate($surveyId)
    {
        $survey = Survey::with(['questions.responses'])->findOrFail($surveyId);


        $labels = [];
        $values = [];

        foreach ($survey->questions as $question) {
            $labels[] = $question->question_text;
            $values[] = $question->responses->count();
        }

        return view('satisfaccion.reports.survey_report', compact('survey', 'labels', 'values'));
    }

    // Mostrar reporte individual
    public function show($surveyId)
    {
        $survey = Survey::with(['questions', 'responses'])->findOrFail($surveyId);
        return view('satisfaccion.reports.show', compact('survey'));
    }

    // Descargar PDF
   public function downloadPdf($surveyId)
{
    // 1️⃣ Obtener la encuesta con sus relaciones
    $survey = \App\Models\Survey::with('questions.responses')->findOrFail($surveyId);

    // 2️⃣ Definir nombre y ruta del archivo
    $fileName = 'reporte_encuesta_' . $surveyId . '.pdf';
    $directory = public_path('reports');
    $filePath = $directory . '/' . $fileName;

    // 3️⃣ Crear carpeta si no existe
    if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
    }

    // 4️⃣ Generar el PDF usando la vista Blade
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('satisfaccion.reports.pdf', compact('survey'));
    $pdf->save($filePath);

    // 5️⃣ Registrar en la tabla survey_reports
    \App\Models\SurveyReport::create([
        
        'id_survey'     => $survey->id,
        'report_type'   => 'PDF',
        'file_path'     => '/reports/' . $fileName,
        'creation_date' => now(),
    ]);

    // 6️⃣ Descargar el archivo
    return response()->download($filePath);
}


    // Descargar Excel
    public function downloadExcel($surveyId)
{
    $survey = \App\Models\Survey::findOrFail($surveyId);

    $fileName = 'reporte_encuesta_' . $surveyId . '.xlsx';
    $directory = storage_path('app/public/reports');
    $filePath = $directory . '/' . $fileName;

    // Crear la carpeta si no existe
    if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
    }

    // Guardar el Excel en storage/app/public/reports
    \Maatwebsite\Excel\Facades\Excel::store(
        new \App\Exports\SurveyExport($surveyId),
        'reports/' . $fileName,
        'public'
    );

    // Registrar en BD
    \App\Models\SurveyReport::create([
        'id_survey'     => $survey->id,
        'report_type'   => 'EXCEL',
        'file_path'     => '/storage/reports/' . $fileName, // ruta accesible desde navegador
        'creation_date' => now(),
    ]);

    // Descargar el archivo
    return response()->download($filePath);
}


}
