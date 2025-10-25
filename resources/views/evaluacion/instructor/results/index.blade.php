@extends('evaluacion.layouts.app')

@section('title', 'Mis Evaluaciones - Instructor')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-white">Mis Evaluaciones</h1>
                <p class="text-gray-400 mt-2">Todas las evaluaciones recibidas de estudiantes</p>
            </div>
            <a href="{{ route('evaluacion.instructor.dashboard') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                ← Volver al Dashboard
            </a>
        </div>
    </div>

   <!-- Estadísticas Rápidas -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-[#1f2937] rounded-xl border border-blue-500 p-4 text-center">
        <div class="text-2xl font-bold text-blue-400">
            @if(isset($stats))
                {{ $stats['totalEvaluations'] }}
            @else
                {{ $evaluations->total() }}
            @endif
        </div>
        <div class="text-gray-400 text-sm">Total Evaluaciones</div>
    </div>
    
    <div class="bg-[#1f2937] rounded-xl border border-green-500 p-4 text-center">
        <div class="text-2xl font-bold text-green-400">
            @if(isset($stats))
                {{ $stats['totalStudents'] }}
            @else
                {{ $evaluations->count() > 0 ? $evaluations->unique('student_id')->count() : 0 }}
            @endif
        </div>
        <div class="text-gray-400 text-sm">Estudiantes Únicos</div>
    </div>
    
    <div class="bg-[#1f2937] rounded-xl border border-purple-500 p-4 text-center">
        <div class="text-2xl font-bold text-purple-400">
            @if(isset($stats))
                {{ $stats['totalSessions'] }}
            @else
                {{ $evaluations->count() > 0 ? $evaluations->unique('evaluation_session_id')->count() : 0 }}
            @endif
        </div>
        <div class="text-gray-400 text-sm">Sesiones de Evaluación</div>
    </div>
</div>
    <!-- Evaluaciones por Sección -->
    <div class="bg-[#1f2937] rounded-xl border border-gray-800 p-6">
        @if($evaluations->count() > 0)
            @php
                // Agrupar evaluaciones por sesión
                $evaluationsBySession = $evaluations->groupBy('evaluation_session_id');
                $puntuaciones = [1 => 5, 2 => 4, 3 => 3, 4 => 2, 5 => 1];
            @endphp

            <div class="space-y-8">
                @foreach($evaluationsBySession as $sessionId => $sessionEvaluations)
                    @php
                        $session = $sessionEvaluations->first()->session;
                        
                        // Calcular promedio por sesión
                        $totalPuntosSesion = 0;
                        $totalRespuestasSesion = 0;
                        
                        foreach($sessionEvaluations as $eval) {
                            if($eval->question->question_type == 'scale_1_5' && is_numeric($eval->text_response)) {
                                $optionId = (int)$eval->text_response;
                                $option = \App\Models\EvaluationQuestionOption::find($optionId);
                                if($option && isset($puntuaciones[$option->option_value])) {
                                    $totalPuntosSesion += $puntuaciones[$option->option_value];
                                    $totalRespuestasSesion++;
                                }
                            }
                        }
                        
                        $promedioSesion = $totalRespuestasSesion > 0 ? round($totalPuntosSesion / $totalRespuestasSesion, 1) : 0;
                        
                        // Agrupar por pregunta dentro de la sesión
                        $evaluationsByQuestion = $sessionEvaluations->groupBy('question_id');
                    @endphp

                    <!-- SECCIÓN PRINCIPAL -->
                    <div class="bg-gray-800/50 rounded-lg border-2 border-blue-500/30 overflow-hidden">
                        <!-- Header de la Sección -->
                        <div class="bg-gradient-to-r from-blue-600/30 to-purple-600/30 p-6 border-b border-blue-500/30">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="font-bold text-white text-2xl mb-2">
                                        <i class="fas fa-folder-open mr-3 text-blue-400"></i>
                                        {{ $session->title ?? 'Sección sin título' }}
                                    </h3>
                                    <p class="text-gray-300 text-lg mb-4">
                                        {{ $session->description ?? 'Sin descripción' }}
                                    </p>
                                    <div class="flex flex-wrap gap-4 text-sm">
                                        <span class="bg-blue-500/20 text-blue-400 px-3 py-1 rounded-full">
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ $session->start_date->format('d/m/Y') }} - {{ $session->end_date->format('d/m/Y') }}
                                        </span>
                                        <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full">
                                            <i class="fas fa-chart-bar mr-1"></i>
                                            Promedio: {{ $promedioSesion }}/5
                                        </span>
                                        <span class="bg-purple-500/20 text-purple-400 px-3 py-1 rounded-full">
                                            <i class="fas fa-question-circle mr-1"></i>
                                            {{ $evaluationsByQuestion->count() }} preguntas
                                        </span>
                                        <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full">
                                            <i class="fas fa-users mr-1"></i>
                                            {{ $sessionEvaluations->count() }} respuestas
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Preguntas de esta sección -->
                        <div class="p-6 space-y-6">
                            @foreach($evaluationsByQuestion as $questionId => $questionEvaluations)
                                @php
                                    $question = $questionEvaluations->first()->question;
                                    
                                    // Calcular promedio por pregunta
                                    $totalPuntosPregunta = 0;
                                    $totalRespuestasPregunta = 0;
                                    
                                    foreach($questionEvaluations as $eval) {
                                        if($eval->question->question_type == 'scale_1_5' && is_numeric($eval->text_response)) {
                                            $optionId = (int)$eval->text_response;
                                            $option = \App\Models\EvaluationQuestionOption::find($optionId);
                                            if($option && isset($puntuaciones[$option->option_value])) {
                                                $totalPuntosPregunta += $puntuaciones[$option->option_value];
                                                $totalRespuestasPregunta++;
                                            }
                                        }
                                    }
                                    
                                    $promedioPregunta = $totalRespuestasPregunta > 0 ? round($totalPuntosPregunta / $totalRespuestasPregunta, 1) : 0;
                                @endphp

                                <!-- PREGUNTA INDIVIDUAL -->
                                <div class="bg-gray-900/40 rounded-xl p-5 border border-gray-600/50">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-white text-lg mb-3 flex items-center">
                                                <i class="fas fa-question mr-3 text-yellow-400"></i>
                                                {{ $question->question_text }}
                                            </h4>
                                            <div class="flex flex-wrap gap-3 text-sm">
                                                <span class="bg-blue-500/20 text-blue-400 px-2 py-1 rounded text-xs">
                                                    {{ $question->question_type == 'scale_1_5' ? 'Escala 1-5' : 'Texto Libre' }}
                                                </span>
                                                <span class="bg-yellow-500/20 text-yellow-400 px-2 py-1 rounded text-xs">
                                                    <i class="fas fa-chart-line mr-1"></i>
                                                    Promedio: {{ $promedioPregunta }}/5
                                                </span>
                                                <span class="bg-green-500/20 text-green-400 px-2 py-1 rounded text-xs">
                                                    <i class="fas fa-reply mr-1"></i>
                                                    {{ $questionEvaluations->count() }} respuestas
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Respuestas de esta pregunta -->
                                    <div class="space-y-4 mt-4">
                                        @foreach($questionEvaluations as $evaluation)
                                        <div class="bg-gray-800/60 rounded-lg p-4 border border-gray-700/50 hover:border-blue-500/30 transition-colors">
                                            <div class="flex justify-between items-start">
                                                <div class="flex-1">
                                                    <div class="flex items-center space-x-4 mb-3">
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-user text-green-400"></i>
                                                            <span class="font-medium text-white">
                                                                {{ $evaluation->student->first_name ?? 'Estudiante' }} {{ $evaluation->student->last_name ?? '' }}
                                                            </span>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-clock text-gray-400"></i>
                                                            <span class="text-gray-400 text-sm">
                                                                {{ $evaluation->response_date->format('d/m/Y H:i') }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="bg-gray-900/50 rounded p-3">
                                                        <p class="text-gray-300 text-sm">
                                                            <strong class="text-white">Respuesta:</strong> 
                                                            @if($evaluation->question->question_type == 'scale_1_5' && is_numeric($evaluation->text_response))
                                                                @php
                                                                    $optionId = (int)$evaluation->text_response;
                                                                    $option = \App\Models\EvaluationQuestionOption::find($optionId);
                                                                @endphp
                                                                
                                                                @if($option)
                                                                    <span class="text-lg font-semibold {{ $option->option_value == 1 ? 'text-green-400' : ($option->option_value == 2 ? 'text-blue-400' : ($option->option_value == 3 ? 'text-yellow-400' : ($option->option_value == 4 ? 'text-orange-400' : 'text-red-400'))) }}">
                                                                        {{ $option->option_text }}
                                                                    </span>
                                                                    <span class="text-blue-400 ml-3 font-bold">
                                                                        (Puntuación: {{ $puntuaciones[$option->option_value] ?? 0 }}/5)
                                                                    </span>
                                                                @else
                                                                    <span class="text-red-400">Opción ID: {{ $optionId }}</span>
                                                                @endif
                                                            @else
                                                                <span class="text-gray-300">{{ $evaluation->text_response ?? 'Sin respuesta' }}</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
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
            <div class="mt-6">
                {{ $evaluations->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-clipboard-list text-5xl text-gray-500 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-400 mb-2">No hay evaluaciones</h3>
                <p class="text-gray-500">Aún no has recibido evaluaciones de estudiantes.</p>
            </div>
        @endif
    </div>
</div>
@endsection