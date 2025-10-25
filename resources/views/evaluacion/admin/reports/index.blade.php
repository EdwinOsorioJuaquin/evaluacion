@extends('evaluacion.layouts.app')

@section('title', 'Reportes y Resultados')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white">Reportes y Resultados</h1>
            <p class="text-gray-400 mt-2">Consulta los resultados de las evaluaciones realizadas</p>
        </div>
    </div>

<!-- Tarjetas de Estadísticas - CORREGIDO -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-[#1f2937] rounded-xl border border-gray-800 p-6 text-center">
            <div class="text-2xl font-bold text-blue-400">{{ $totalSessions ?? 0 }}</div>
            <div class="text-gray-400 text-sm mt-1">Total Sesiones</div>
        </div>
        <div class="bg-[#1f2937] rounded-xl border border-gray-800 p-6 text-center">
            <div class="text-2xl font-bold text-green-400">{{ $totalEvaluations ?? 0 }}</div>
            <div class="text-gray-400 text-sm mt-1">Evaluaciones Realizadas</div>
        </div>
        <div class="bg-[#1f2937] rounded-xl border border-gray-800 p-6 text-center">
            <div class="text-2xl font-bold text-yellow-400">{{ $averageRating ?? 0 }}/5</div>
            <div class="text-gray-400 text-sm mt-1">Calificación Promedio</div>
        </div>
    </div>

    <!-- Lista de Sesiones para Reportes -->
    <div class="bg-[#1f2937] rounded-xl border border-gray-800 p-6">
        <h2 class="text-xl font-semibold text-white mb-6">Sesiones de Evaluación</h2>
        
        @if($sessions->count() > 0)
            <div class="space-y-4">
                @foreach($sessions as $session)
                <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700 hover:border-custom-blue transition-colors">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="font-semibold text-white mb-2">{{ $session->title }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-400">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt mr-2 text-custom-blue"></i>
                                    {{ \Carbon\Carbon::parse($session->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($session->end_date)->format('d/m/Y') }}
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-question-circle mr-2 text-custom-blue"></i>
                                    {{ $session->questions_count }} preguntas
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-chart-bar mr-2 text-custom-blue"></i>
                                    {{ $session->evaluations_count ?? 0 }} evaluaciones
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-2 ml-4">
                            <a href="{{ route('evaluacion.admin.session.results', $session->id) }}" 
                               class="bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 px-4 py-2 rounded-lg transition-colors flex items-center text-sm">
                                <i class="fas fa-chart-pie mr-2"></i> Ver Reporte
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-chart-bar text-4xl text-gray-500 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-400 mb-2">No hay sesiones para mostrar</h3>
                <p class="text-gray-500 mb-4">Crea sesiones de evaluación para generar reportes</p>
                <a href="{{ route('evaluacion.admin.sessions.index') }}" class="bg-custom-blue hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors inline-flex items-center">
                    <i class="fas fa-plus-circle mr-2"></i> Crear Sesión
                </a>
            </div>
        @endif
    </div>

    <!-- Información Adicional -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-[#1f2937] rounded-xl border border-gray-800 p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Preguntas Frecuentes</h3>
            <div class="space-y-3">
                <div class="bg-gray-800/50 rounded-lg p-3">
                    <div class="font-medium text-white">¿Cómo ver los resultados?</div>
                    <div class="text-sm text-gray-400 mt-1">Haz clic en "Ver Reporte" en cualquier sesión para ver estadísticas detalladas</div>
                </div>
                <div class="bg-gray-800/50 rounded-lg p-3">
                    <div class="font-medium text-white">¿Qué información incluyen los reportes?</div>
                    <div class="text-sm text-gray-400 mt-1">Calificaciones promedio, distribución de respuestas, comentarios y más</div>
                </div>
            </div>
        </div>

        <div class="bg-[#1f2937] rounded-xl border border-gray-800 p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Acciones Rápidas</h3>
            <div class="space-y-3">
                <a href="{{ route('evaluacion.admin.sessions.index') }}" class="flex items-center p-3 bg-blue-500/10 hover:bg-blue-500/20 rounded-lg border border-blue-500/30 transition-colors">
                    <i class="fas fa-list-alt text-blue-400 mr-3"></i>
                    <span class="text-white">Gestionar Sesiones</span>
                </a>
                <a href="{{ route('evaluacion.admin.sessions.questions.index', $sessions->first()->id ?? '#') }}" class="flex items-center p-3 bg-green-500/10 hover:bg-green-500/20 rounded-lg border border-green-500/30 transition-colors">
                    <i class="fas fa-question-circle text-green-400 mr-3"></i>
                    <span class="text-white">Gestionar Preguntas</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection