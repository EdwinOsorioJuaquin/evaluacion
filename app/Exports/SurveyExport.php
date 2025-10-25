<?php

namespace App\Exports;

use App\Models\Survey;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class SurveyExport implements FromCollection, WithHeadings, WithStyles
{
    protected $surveyId;

    public function __construct($surveyId)
    {
        $this->surveyId = $surveyId;
    }

    public function collection()
    {
        // 🔹 Cargamos encuesta con preguntas, respuestas y estudiantes
        $survey = Survey::with('questions.responses.student')->find($this->surveyId);

        $data = collect();

        // 🔹 KPIs generales
        $totalQuestions = $survey->questions->count();
        $totalResponses = $survey->questions->flatMap->responses->count();

        // ✅ Tasa de respuesta considerando estudiantes únicos
        $totalStudents = Student::count(); // Total de estudiantes
        $studentsAnswered = $survey->questions->flatMap->responses
            ->pluck('id_student')
            ->unique()
            ->count(); // Estudiantes únicos que respondieron

        $responseRate = $totalStudents > 0 ? round(($studentsAnswered / $totalStudents) * 100, 1) : 0;

        // 🔹 Fechas de primera y última respuesta
        $firstResponseDate = $survey->questions->flatMap->responses->min('response_date') 
            ?? $survey->creation_date;
        $lastResponseDate = $survey->questions->flatMap->responses->max('response_date') 
            ?? $survey->creation_date;

        // 🔹 Fila de KPIs
        $data->push([
            '#' => '',
            'Pregunta' => '📊 KPIs Generales',
            'Total Preguntas' => $totalQuestions,
            'Total Respuestas' => $totalResponses,
            'Tasa de Respuesta (%)' => $responseRate,
            'Resumen de Respuestas' => '-',
            'Detalle de Respuestas' => '-',
        ]);

        // 🔹 Detalle por pregunta
        foreach ($survey->questions as $index => $question) {
            $total = $question->responses->count();

            // 🔹 Contar opciones múltiples
            $options = [];
            foreach ($question->responses as $response) {
                $selected = is_array($response->response_text)
                    ? $response->response_text
                    : explode(',', $response->response_text);
                foreach ($selected as $opt) {
                    $opt = trim($opt);
                    if ($opt) $options[$opt] = ($options[$opt] ?? 0) + 1;
                }
            }

            // 🔹 Resumen por opción
            $resumen = '';
            foreach ($options as $answer => $count) {
                $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                $resumen .= "• $answer — $count ($percentage%)\n";
            }
            if ($resumen === '') $resumen = 'Sin respuestas registradas';

            // 🔹 Detalle resumido
            $detalle = $total > 0
                ? "Encuesta respondida desde " 
                  . Carbon::parse($firstResponseDate)->format('d/m/Y H:i') 
                  . " hasta " 
                  . Carbon::parse($lastResponseDate)->format('d/m/Y H:i')
                : "No se han registrado respuestas aún.";

            $data->push([
                '#' => $index + 1,
                'Pregunta' => $question->question_text,
                'Total Preguntas' => '',
                'Total Respuestas' => $total,
                'Tasa de Respuesta (%)' => '',
                'Resumen de Respuestas' => $resumen,
                'Detalle de Respuestas' => $detalle,
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            '#',
            'Pregunta',
            'Total Preguntas',
            'Total Respuestas',
            'Tasa de Respuesta (%)',
            'Resumen de Respuestas',
            'Detalle de Respuestas',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Negrita para encabezados y fila de KPIs
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A2:G2')->getFont()->setBold(true);

        // Ajuste de columnas automático
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}




