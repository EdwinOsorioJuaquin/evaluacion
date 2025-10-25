@extends('evaluacion.layouts.app')

@section('title', 'Historial de Evaluaciones')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white">Historial de Evaluaciones</h1>
            <p class="text-gray-400 mt-2">Consulta todas las evaluaciones que has completado</p>
        </div>
    </div>
    <div class="mb-6 flex items-center text-sm text-gray-400">
    <a href="{{ route('evaluacion.student.dashboard') }}" class="hover:text-blue-400 transition-colors">
        <i class="fas fa-home mr-1"></i>Dashboard
    </a>
    <span class="mx-2">/</span>
    <span class="text-blue-400">Historial de Evaluaciones</span>
</div>

    <!-- Tarjetas de Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-[#1f2937] rounded-xl border border-gray-800 p-6 text-center">
            <div class="text-2xl font-bold text-blue-400">{{ $stats['totalEvaluations'] ?? 0 }}</div>
            <div class="text-gray-400 text-sm mt-1">Total Evaluaciones</div>
        </div>
        <div class="bg-[#1f2937] rounded-xl border border-gray-800 p-6 text-center">
            <div class="text-2xl font-bold text-green-400">{{ $stats['totalInstructors'] ?? 0 }}</div>
            <div class="text-gray-400 text-sm mt-1">Docentes Evaluados</div>
        </div>
        <div class="bg-[#1f2937] rounded-xl border border-gray-800 p-6 text-center">
            <div class="text-2xl font-bold text-purple-400">{{ $stats['totalSessions'] ?? 0 }}</div>
            <div class="text-gray-400 text-sm mt-1">Sesiones Completadas</div>
        </div>
    </div>

    <!-- Lista de Evaluaciones por Sesión -->
    <div class="bg-[#1f2937] rounded-xl border border-gray-800 p-6">
        <h2 class="text-xl font-semibold text-white mb-6">Evaluaciones Realizadas</h2>
        
        @if($evaluationsBySession->count() > 0)
            <div class="space-y-6">
                @foreach($evaluationsBySession as $sessionId => $sessionEvaluations)
                    @php
                        $session = $sessionEvaluations->first()->session;
                        $sessionInstructors = $sessionEvaluations->groupBy('instructor_id');
                    @endphp

                    <!-- Sección por Sesión -->
                    <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <h3 class="font-semibold text-white text-lg mb-2">{{ $session->title }}</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-400">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar-alt mr-2 text-blue-400"></i>
                                        {{ \Carbon\Carbon::parse($session->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($session->end_date)->format('d/m/Y') }}
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-users mr-2 text-green-400"></i>
                                        {{ $sessionInstructors->count() }} docentes evaluados
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-list-check mr-2 text-purple-400"></i>
                                        {{ $sessionEvaluations->count() }} evaluaciones
                                    </div>
                                </div>
                            </div>
                            <span class="bg-green-500/20 text-green-400 text-xs px-3 py-1 rounded-full">
                                Completada
                            </span>
                        </div>

                        <!-- Lista de Docentes Evaluados en esta Sesión -->
                        <div class="space-y-4">
                            @foreach($sessionInstructors as $instructorId => $instructorEvaluations)
                                @php
                                    $evaluation = $instructorEvaluations->first();
                                    $evaluationDate = $evaluation->response_date;
                                    $instructorName = $evaluation->instructor_name ?? 'Docente';
                                    $instructorEmail = $evaluation->instructor_email ?? '';
                                @endphp

                                <!-- Tarjeta de Docente - MÁS DESTACADA -->
                                <div class="bg-gray-700/30 rounded-lg p-4 border-l-4 border-blue-500">
                                    <!-- ENCABEZADO DEL DOCENTE - MÁS VISIBLE -->
                                    <div class="bg-blue-500/10 rounded-lg p-3 mb-4 border border-blue-500/20">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="w-12 h-12 bg-blue-500/20 rounded-full flex items-center justify-center mr-3">
                                                    <i class="fas fa-user-tie text-blue-400 text-lg"></i>
                                                </div>
                                                <div>
                                                    <h4 class="font-bold text-white text-lg">📋 Docente Evaluado:</h4>
                                                    <p class="text-blue-300 font-semibold text-lg">{{ $instructorName }}</p>
                                                    @if($instructorEmail)
                                                    <p class="text-gray-400 text-sm">
                                                        <i class="fas fa-envelope mr-1"></i>{{ $instructorEmail }}
                                                    </p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <span class="bg-green-500/20 text-green-400 text-sm px-3 py-1 rounded-full">
                                                    <i class="fas fa-calendar mr-1"></i>
                                                    Evaluado el {{ $evaluationDate->format('d/m/Y') }}
                                                </span>
                                                <p class="text-gray-400 text-xs mt-1">
                                                    {{ $instructorEvaluations->count() }} preguntas respondidas
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Preguntas y Respuestas -->
                                    <div class="space-y-3">
                                        <h5 class="text-white font-semibold mb-3 flex items-center">
                                            <i class="fas fa-list-check mr-2 text-yellow-400"></i>
                                            Preguntas y Respuestas:
                                        </h5>
                                        
                                        @foreach($instructorEvaluations as $evaluation)
                                            <div class="bg-gray-600/20 rounded-lg p-3 border border-gray-600">
                                                <div class="flex justify-between items-start mb-2">
                                                    <p class="text-white text-sm font-medium">
                                                        <span class="text-blue-400 mr-2">•</span>
                                                        {{ $evaluation->question->question_text ?? 'Pregunta general' }}
                                                    </p>
                                                    @if($evaluation->question->question_type == 'multiple_choice')
                                                        <span class="bg-green-500/20 text-green-400 text-xs px-2 py-1 rounded-full">
                                                            Opción múltiple
                                                        </span>
                                                    @elseif($evaluation->question->question_type == 'scale_1_5')
                                                        <span class="bg-yellow-500/20 text-yellow-400 text-xs px-2 py-1 rounded-full">
                                                            Escala 1-5
                                                        </span>
                                                    @else
                                                        <span class="bg-purple-500/20 text-purple-400 text-xs px-2 py-1 rounded-full">
                                                            Texto abierto
                                                        </span>
                                                    @endif
                                                </div>
                                                
                                                <!-- Mostrar Respuesta Real MEJORADA -->
                                                <div class="bg-gray-700/50 rounded p-3">
                                                    <p class="text-gray-300 text-sm">
                                                        <strong class="text-blue-300">Tu respuesta:</strong><br>
                                                        @php
                                                            $responseText = $evaluation->text_response;
                                                            $questionType = $evaluation->question->question_type ?? 'text';
                                                            
                                                            // DEBUG DETALLADO
                                                            \Log::info("Evaluación ID: {$evaluation->id}, Tipo: {$questionType}, Respuesta ID: {$responseText}");
                                                        @endphp

                                                        @if($questionType == 'multiple_choice')
                                                            @php
                                                                // Buscar la opción seleccionada por su ID
                                                                $selectedOption = \App\Models\EvaluationQuestionOption::find($responseText);
                                                            @endphp
                                                            @if($selectedOption)
                                                                <span class="text-green-400 flex items-center mt-1">
                                                                    <i class="fas fa-check-circle mr-2"></i>
                                                                    {{ $selectedOption->option_text }}
                                                                </span>
                                                            @else
                                                                <span class="text-yellow-400 flex items-center mt-1">
                                                                    <i class="fas fa-question-circle mr-2"></i>
                                                                    Opción no encontrada (ID: {{ $responseText }})
                                                                </span>
                                                            @endif
                                                        @elseif($questionType == 'scale_1_5')
                                                            <!-- BUSCAR EL VALOR DE LA ESCALA A TRAVÉS DE LA OPCIÓN -->
                                                            @php
                                                                $selectedOption = \App\Models\EvaluationQuestionOption::find($responseText);
                                                                if ($selectedOption) {
                                                                    $scaleValue = $selectedOption->option_value;
                                                                    $scaleTexts = [
                                                                        1 => '😠 Totalmente en desacuerdo (1)',
                                                                        2 => '🙁 En desacuerdo (2)', 
                                                                        3 => '😐 Neutral (3)',
                                                                        4 => '🙂 De acuerdo (4)',
                                                                        5 => '😄 Totalmente de acuerdo (5)'
                                                                    ];
                                                                    $scaleText = $scaleTexts[$scaleValue] ?? 'Valor no reconocido: ' . $scaleValue;
                                                                } else {
                                                                    $scaleText = 'Opción no encontrada (ID: ' . $responseText . ')';
                                                                }
                                                                
                                                                \Log::info("Escala - Opción ID: {$responseText}, Valor: " . ($selectedOption ? $selectedOption->option_value : 'No encontrado'));
                                                            @endphp
                                                            <span class="text-yellow-400 flex items-center mt-1">
                                                                <i class="fas fa-chart-bar mr-2"></i>
                                                                {{ $scaleText }}
                                                            </span>
                                                        @else
                                                            <!-- TEXTO ABIERTO -->
                                                            <span class="text-white mt-1 block bg-gray-800/50 p-2 rounded border-l-4 border-green-500">
                                                                <i class="fas fa-comment mr-2 text-green-400"></i>
                                                                "{{ $responseText }}"
                                                            </span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginación -->
            @if($evaluations->hasPages())
            <div class="mt-6">
                {{ $evaluations->links() }}
            </div>
            @endif

        @else
            <!-- Estado vacío -->
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-clipboard-list text-gray-500 text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-400 mb-2">No hay evaluaciones realizadas</h3>
                <p class="text-gray-500 mb-4">Completa algunas evaluaciones para ver tu historial</p>
                <a href="{{ route('evaluacion.student.dashboard') }}" 
                   class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Volver al Dashboard
                </a>
            </div>
        @endif
    </div>

    <!-- Información Adicional -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-[#1f2937] rounded-xl border border-gray-800 p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Información del Historial</h3>
            <div class="space-y-3">
                <div class="bg-gray-800/50 rounded-lg p-3">
                    <div class="font-medium text-white flex items-center">
                        <i class="fas fa-info-circle text-blue-400 mr-2"></i>
                        ¿Qué información ves aquí?
                    </div>
                    <div class="text-sm text-gray-400 mt-1">Todas las evaluaciones que has completado, organizadas por sesión y docente</div>
                </div>
                <div class="bg-gray-800/50 rounded-lg p-3">
                    <div class="font-medium text-white flex items-center">
                        <i class="fas fa-eye text-green-400 mr-2"></i>
                        Tus respuestas
                    </div>
                    <div class="text-sm text-gray-400 mt-1">Puedes revisar todas las respuestas que diste a cada docente</div>
                </div>
            </div>
        </div>

        <div class="bg-[#1f2937] rounded-xl border border-gray-800 p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Acciones Rápidas</h3>
            <div class="space-y-3">
                <a href="{{ route('evaluacion.student.dashboard') }}" class="flex items-center p-3 bg-blue-500/10 hover:bg-blue-500/20 rounded-lg border border-blue-500/30 transition-colors">
                    <i class="fas fa-tachometer-alt text-blue-400 mr-3"></i>
                    <span class="text-white">Volver al Dashboard</span>
                </a>
                @if($stats['totalEvaluations'] > 0)
                <div class="flex items-center p-3 bg-green-500/10 rounded-lg border border-green-500/30">
                    <i class="fas fa-check-circle text-green-400 mr-3"></i>
                    <span class="text-white">Has completado {{ $stats['totalEvaluations'] }} evaluaciones</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Estilos para la paginación */
    .pagination {
        display: flex;
        justify-content: center;
        list-style: none;
        padding: 0;
        margin-top: 2rem;
    }
    
    .pagination li {
        margin: 0 2px;
    }
    
    .pagination li a,
    .pagination li span {
        display: block;
        padding: 8px 16px;
        background: #374151;
        color: #d1d5db;
        border-radius: 6px;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 1px solid #4b5563;
    }
    
    .pagination li a:hover {
        background: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }
    
    .pagination li.active span {
        background: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }
    
    .pagination li.disabled span {
        background: #1f2937;
        color: #6b7280;
        border-color: #374151;
    }
</style>
@endpush
@endsection