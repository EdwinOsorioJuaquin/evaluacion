<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Encuesta</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 20px;
        }
        p {
            margin: 4px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
        }
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .kpi-table th, .kpi-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        .kpi-table th {
            background-color: #d9edf7;
        }
        .table-title {
            font-weight: bold;
            margin-top: 20px;
            font-size: 14px;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    {{-- Cabecera --}}
    <div class="flex items-center gap-3">
        <img src="{{ asset('storage/incadev/incadev_logo_navbar_400h.png') }}"
             alt="INCADEV"
             class="h-10">
    </div>

    <h1>Reporte de Encuesta: {{ $survey->qualification ?? 'Sin título' }}</h1>

    <p><strong>ID:</strong> {{ $survey->id }}</p>
    <p><strong>Descripción:</strong> {{ $survey->description ?? 'Sin descripción' }}</p>
    <p><strong>Estado:</strong> {{ $survey->state ?? 'N/A' }}</p>
    @if($survey->creation_date)
        <p><strong>Fecha de creación:</strong> {{ \Carbon\Carbon::parse($survey->creation_date)->format('d/m/Y H:i') }}</p>
    @endif

    {{-- KPIs --}}
    <p class="table-title"> KPIs Generales de la Encuesta</p>

    @php
        use App\Models\Student;

        $totalQuestions = $survey->questions->count();
        $totalResponses = $survey->questions->flatMap->responses->count();

        // ✅ Tasa de respuesta por estudiantes únicos
        $totalStudents = Student::count(); // Total de estudiantes
        $studentsAnswered = $survey->questions->flatMap->responses
            ->pluck('id_student')
            ->unique()
            ->count(); // Estudiantes únicos que respondieron

        $responseRate = $totalStudents > 0 ? round(($studentsAnswered / $totalStudents) * 100, 1) : 0;

        // Fechas de la primera y última respuesta
        $firstResponseDate = $survey->questions->flatMap->responses->min('response_date');
        $lastResponseDate = $survey->questions->flatMap->responses->max('response_date');
    @endphp

    <table class="kpi-table">
        <thead>
            <tr>
                <th>Total Preguntas</th>
                <th>Total Respuestas</th>
                <th>Tasa de Respuesta (%)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $totalQuestions }}</td>
                <td>{{ $totalResponses }}</td>
                <td>{{ $responseRate }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Detalle preguntas --}}
    <p class="table-title"> Detalle de Preguntas y Respuestas</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Pregunta</th>
                <th>Resumen de Respuestas (Conteo y %)</th>
                <th>Detalle de Respuestas</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($survey->questions as $index => $question)
                @php
                    $total = $question->responses->count();
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
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $question->question_text }}</td>
                    <td>
                        @if(count($options) > 0)
                            @foreach ($options as $answer => $count)
                                @php
                                    $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                                @endphp
                                • {{ $answer ?? 'Sin respuesta' }} — {{ $count }} ({{ $percentage }}%)<br>
                            @endforeach
                        @else
                            Sin respuestas registradas
                        @endif
                    </td>
                    <td>
                        @if($total > 0)
                            Encuesta respondida desde 
                            {{ $firstResponseDate ? \Carbon\Carbon::parse($firstResponseDate)->format('d/m/Y H:i') : 'N/A' }} 
                            hasta 
                            {{ $lastResponseDate ? \Carbon\Carbon::parse($lastResponseDate)->format('d/m/Y H:i') : 'N/A' }}.
                        @else
                            No se han registrado respuestas aún.
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Total de respuestas --}}
    <p style="margin-top: 15px;">
        <strong>Total general de respuestas registradas:</strong> {{ $totalResponses }}
    </p>

    {{-- Pie de página --}}
    <div class="footer">
        Generado automáticamente el {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>

