<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte - {{ $survey->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }
        .header {
            background: #26BBFF;
            color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .header h1 {
            font-size: 20px;
            margin-bottom: 8px;
        }
        .header p {
            font-size: 10px;
            opacity: 0.9;
        }
        .info-box {
            background: #f5f5f5;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #26BBFF;
            border-radius: 4px;
        }
        .info-box h3 {
            color: #26BBFF;
            font-size: 13px;
            margin-bottom: 8px;
        }
        .info-box p {
            margin-bottom: 4px;
            font-size: 10px;
        }
        .response-item {
            border: 1px solid #e0e0e0;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 6px;
            page-break-inside: avoid;
            background: white;
        }
        .response-header {
            background: #f8f9fa;
            padding: 10px;
            margin: -15px -15px 15px -15px;
            border-bottom: 2px solid #26BBFF;
            border-radius: 6px 6px 0 0;
        }
        .response-header h3 {
            color: #26BBFF;
            font-size: 13px;
            margin-bottom: 5px;
        }
        .response-header .meta {
            font-size: 9px;
            color: #666;
        }
        .question-block {
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px dashed #e0e0e0;
        }
        .question-block:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .question-text {
            font-weight: 600;
            color: #333;
            margin-bottom: 6px;
            font-size: 11px;
        }
        .answer-text {
            background: #f8f9fa;
            padding: 8px 10px;
            border-radius: 4px;
            font-size: 10px;
            color: #555;
        }
        .answer-text.empty {
            color: #999;
            font-style: italic;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e0e0e0;
            text-align: center;
            font-size: 9px;
            color: #999;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .stat-item {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 10px;
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
        }
        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #26BBFF;
        }
        .stat-label {
            font-size: 9px;
            color: #666;
            margin-top: 4px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- ENCABEZADO -->
    <div class="header">
        <h1>REPORTE DE ENCUESTA</h1>
        <p>{{ $survey->name }}</p>
    </div>

    <!-- INFORMACIÓN GENERAL -->
    <div class="info-box">
        <h3>Información General</h3>
        <p><strong>Encuesta:</strong> {{ $survey->name }}</p>
        @if($survey->description)
        <p><strong>Descripción:</strong> {{ $survey->description }}</p>
        @endif
        <p><strong>Estado:</strong> {{ $survey->is_active ? 'Activa' : 'Inactiva' }}</p>
        <p><strong>Total de Preguntas:</strong> {{ $survey->questions->count() }}</p>
        <p><strong>Fecha de Generación:</strong> {{ $generatedDate }}</p>
    </div>

    <!-- ESTADÍSTICAS -->
    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-value">{{ $totalResponses }}</div>
            <div class="stat-label">Respuestas Completadas</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $survey->questions->count() }}</div>
            <div class="stat-label">Total Preguntas</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $survey->graduateSurveys->pluck('graduate.program_id')->unique()->count() }}</div>
            <div class="stat-label">Programas Participantes</div>
        </div>
    </div>

    <!-- RESPUESTAS DE GRADUADOS -->
    <h2 style="color: #26BBFF; margin: 25px 0 15px 0; font-size: 15px; border-bottom: 2px solid #26BBFF; padding-bottom: 8px;">
        Respuestas de Graduados ({{ $totalResponses }})
    </h2>

    @foreach($survey->graduateSurveys as $index => $graduateSurvey)
        <div class="response-item">
            <!-- HEADER DE LA RESPUESTA -->
            <div class="response-header">
                <h3>Respuesta #{{ $index + 1 }}</h3>
                <div class="meta">
                    <strong>Graduado:</strong> {{ $graduateSurvey->graduate->user->full_name }} |
                    <strong>Programa:</strong> {{ $graduateSurvey->graduate->program->name }} |
                    <strong>Completada:</strong> {{ $graduateSurvey->completed_at ? $graduateSurvey->completed_at->format('d/m/Y H:i') : 'N/A' }}
                </div>
            </div>

            <!-- RESPUESTAS A LAS PREGUNTAS -->
            @foreach($survey->questions->sortBy('question_order') as $question)
                @php
                    $response = $graduateSurvey->responses->firstWhere('question_id', $question->id);
                @endphp
                
                <div class="question-block">
                    <div class="question-text">
                        {{ $question->question_order }}. {{ $question->question_text }}
                        @if($question->is_required)
                            <span style="color: #EF4444;">*</span>
                        @endif
                    </div>
                    
                    <div class="answer-text {{ !$response ? 'empty' : '' }}">
                        @if($response)
                            @if($question->question_type === 'option' && $response->option)
                                ✓ {{ $response->option->option_text }}
                            @elseif($question->question_type === 'number')
                                {{ $response->number_response ?? 'Sin respuesta' }}
                            @elseif($question->question_type === 'date')
                                {{ $response->date_response ? $response->date_response->format('d/m/Y') : 'Sin respuesta' }}
                            @else
                                {{ $response->text_response ?? 'Sin respuesta' }}
                            @endif
                        @else
                            Sin respuesta
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if(($index + 1) % 2 === 0 && ($index + 1) < $totalResponses)
            <div class="page-break"></div>
        @endif
    @endforeach

    <!-- PIE DE PÁGINA -->
    <div class="footer">
        <p><strong>INCADEV - Sistema de Gestión de Encuestas</strong></p>
        <p>Reporte generado el {{ $generatedDate }}</p>
        <p>Este documento contiene {{ $totalResponses }} respuesta(s) completada(s)</p>
    </div>
</body>
</html>