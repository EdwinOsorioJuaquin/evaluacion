@extends('evaluacion.layouts.app')

@section('title', 'Mi Estadística Personal - Instructor')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-white">Mi Estadística Personal</h1>
                <p class="text-gray-400 mt-2">Análisis detallado de mi desempeño docente</p>
            </div>
            <a href="{{ route('evaluacion.instructor.dashboard') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                ← Volver al Dashboard
            </a>
        </div>
    </div>

    <!-- Tarjetas de Métricas Principales -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-[#1f2937] rounded-xl border border-blue-500 p-6 text-center">
            <div class="text-3xl font-bold text-blue-400">{{ number_format($stats['averageRating'] ?? 0, 1) }}</div>
            <div class="text-gray-400 text-sm mt-1">Rating General</div>
            <div class="text-blue-300 text-xs mt-1">/5.0</div>
        </div>
        
        <div class="bg-[#1f2937] rounded-xl border border-green-500 p-6 text-center">
            <div class="text-3xl font-bold text-green-400">{{ $stats['totalEvaluations'] ?? 0 }}</div>
            <div class="text-gray-400 text-sm mt-1">Total Evaluaciones</div>
        </div>
        
        <div class="bg-[#1f2937] rounded-xl border border-yellow-500 p-6 text-center">
            <div class="text-3xl font-bold text-yellow-400">{{ $stats['totalStudents'] ?? 0 }}</div>
            <div class="text-gray-400 text-sm mt-1">Estudiantes Únicos</div>
        </div>
        
        <div class="bg-[#1f2937] rounded-xl border border-purple-500 p-6 text-center">
            <div class="text-3xl font-bold text-purple-400">{{ $stats['totalSessions'] ?? 0 }}</div>
            <div class="text-gray-400 text-sm mt-1">Sesiones Evaluadas</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Distribución de Puntuaciones -->
        <div class="bg-[#1f2937] rounded-xl border border-gray-800 p-6">
            <h2 class="text-xl font-semibold text-white mb-4">Distribución de Puntuaciones</h2>
            
            @if(isset($stats['ratingDistribution']) && count($stats['ratingDistribution']) > 0)
                <div class="space-y-4">
                    @foreach($stats['ratingDistribution'] as $rating => $count)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                                @if($rating == 1) bg-green-500/20 text-green-400
                                @elseif($rating == 2) bg-blue-500/20 text-blue-400
                                @elseif($rating == 3) bg-yellow-500/20 text-yellow-400
                                @elseif($rating == 4) bg-orange-500/20 text-orange-400
                                @else bg-red-500/20 text-red-400
                                @endif">
                                {{ $rating }}
                            </span>
                            <span class="text-gray-300 text-sm">
                                @if($rating == 1) Totalmente de Acuerdo
                                @elseif($rating == 2) De Acuerdo
                                @elseif($rating == 3) Neutral
                                @elseif($rating == 4) En Desacuerdo
                                @else Totalmente en Desacuerdo
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-32 bg-gray-700 rounded-full h-3">
                                <div class="bg-gradient-to-r from-green-500 to-red-500 h-3 rounded-full" 
                                     style="width: {{ ($count / $stats['totalEvaluations']) * 100 }}%"></div>
                            </div>
                            <span class="text-gray-400 text-sm w-8 text-right">{{ $count }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 text-center py-4">No hay datos de distribución disponibles</p>
            @endif
        </div>

        <!-- Mejores y Peores Preguntas -->
        <div class="bg-[#1f2937] rounded-xl border border-gray-800 p-6">
            <h2 class="text-xl font-semibold text-white mb-4">Desempeño por Categoría</h2>
            
            @if(isset($stats['bestQuestions']) && $stats['bestQuestions']->count() > 0)
                <div class="space-y-4">
                    <div>
                        <h3 class="text-green-400 font-semibold mb-2">🏅 Mejor Evaluadas</h3>
                        <div class="space-y-2">
                            @foreach($stats['bestQuestions']->take(2) as $question)
                            <div class="bg-green-500/10 rounded p-3 border border-green-500/20">
                                <p class="text-white text-sm font-medium">{{ $question->question_text }}</p>
                                <p class="text-green-400 text-xs">Promedio: {{ $question->average_rating }}/5</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    @if(isset($stats['worstQuestions']) && $stats['worstQuestions']->count() > 0)
                    <div>
                        <h3 class="text-red-400 font-semibold mb-2">📈 Áreas de Mejora</h3>
                        <div class="space-y-2">
                            @foreach($stats['worstQuestions']->take(2) as $question)
                            <div class="bg-red-500/10 rounded p-3 border border-red-500/20">
                                <p class="text-white text-sm font-medium">{{ $question->question_text }}</p>
                                <p class="text-red-400 text-xs">Promedio: {{ $question->average_rating }}/5</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            @else
                <p class="text-gray-400 text-center py-4">No hay suficientes datos para análisis</p>
            @endif
        </div>
    </div>

    <!-- Progreso Temporal -->
    <div class="bg-[#1f2937] rounded-xl border border-gray-800 p-6 mb-8">
        <h2 class="text-xl font-semibold text-white mb-4">Evolución Temporal</h2>
        
        @if(isset($stats['monthlyProgress']) && count($stats['monthlyProgress']) > 0)
            <div class="space-y-4">
                @foreach($stats['monthlyProgress'] as $month => $data)
                <div class="flex items-center justify-between p-3 bg-gray-800/50 rounded border border-gray-700">
                    <span class="text-gray-300 font-medium">{{ $month }}</span>
                    <div class="flex items-center space-x-4">
                        <span class="text-blue-400 text-sm">{{ $data['evaluations'] }} evaluaciones</span>
                        <span class="text-yellow-400 font-bold">{{ $data['average'] }}/5</span>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-400 text-center py-4">No hay datos de progreso temporal</p>
        @endif
    </div>

    <!-- Resumen General -->
    <div class="bg-[#1f2937] rounded-xl border border-gray-800 p-6">
        <h2 class="text-xl font-semibold text-white mb-4">Resumen de Desempeño</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
            <div class="bg-green-500/10 rounded-lg p-4 border border-green-500/20">
                <div class="text-2xl font-bold text-green-400">{{ $stats['positivePercentage'] ?? 0 }}%</div>
                <div class="text-green-300 text-sm">Evaluaciones Positivas</div>
                <div class="text-green-400 text-xs">(4-5 estrellas)</div>
            </div>
            
            <div class="bg-yellow-500/10 rounded-lg p-4 border border-yellow-500/20">
                <div class="text-2xl font-bold text-yellow-400">{{ $stats['neutralPercentage'] ?? 0 }}%</div>
                <div class="text-yellow-300 text-sm">Evaluaciones Neutrales</div>
                <div class="text-yellow-400 text-xs">(3 estrellas)</div>
            </div>
            
            <div class="bg-red-500/10 rounded-lg p-4 border border-red-500/20">
                <div class="text-2xl font-bold text-red-400">{{ $stats['negativePercentage'] ?? 0 }}%</div>
                <div class="text-red-300 text-sm">Evaluaciones Negativas</div>
                <div class="text-red-400 text-xs">(1-2 estrellas)</div>
            </div>
        </div>
        
        <!-- Recomendaciones -->
        @if(isset($stats['recommendations']) && count($stats['recommendations']) > 0)
        <div class="mt-6 p-4 bg-blue-500/10 rounded-lg border border-blue-500/20">
            <h3 class="text-blue-400 font-semibold mb-2">💡 Recomendaciones</h3>
            <ul class="text-gray-300 text-sm space-y-1">
                @foreach($stats['recommendations'] as $recommendation)
                <li>• {{ $recommendation }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>
@endsection