@extends('evaluacion.layouts.app')

@section('title', 'Dashboard Estudiante')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white">Panel del Estudiante</h1>
        <p class="text-gray-400 mt-2">Bienvenido, {{ auth()->user()->name }}</p>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-[#1f2937] rounded-xl border border-gray-800 p-6 text-center hover:border-blue-500 transition-colors">
            <div class="text-2xl font-bold text-blue-400">{{ $stats['pendingEvaluations'] }}</div>
            <div class="text-gray-400 text-sm mt-1">Evaluaciones Pendientes</div>
        </div>
        <div class="bg-[#1f2937] rounded-xl border border-gray-800 p-6 text-center hover:border-green-500 transition-colors">
            <div class="text-2xl font-bold text-green-400">{{ $stats['completedEvaluations'] }}</div>
            <div class="text-gray-400 text-sm mt-1">Evaluaciones Completadas</div>
        </div>
        <div class="bg-[#1f2937] rounded-xl border border-gray-800 p-6 text-center hover:border-yellow-500 transition-colors">
            <div class="text-2xl font-bold text-yellow-400">{{ $stats['totalSessions'] }}</div>
            <div class="text-gray-400 text-sm mt-1">Sesiones Totales</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Evaluaciones Pendientes -->
        <div class="bg-[#1f2937] rounded-xl border border-gray-800 p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-white">Evaluaciones Pendientes</h2>
                <span class="bg-yellow-500/20 text-yellow-400 text-xs px-3 py-1 rounded-full">
                    {{ $pendingSessions->count() }} disponibles
                </span>
            </div>

            @if($pendingSessions->count() > 0)
                <div class="space-y-4">
                    @foreach($pendingSessions as $session)
                    <div class="bg-gray-800/50 rounded-lg p-4 border border-yellow-500/30 hover:border-yellow-500 transition-colors">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="font-semibold text-white text-lg">{{ $session->title }}</h3>
                            <span class="bg-yellow-500/20 text-yellow-400 text-xs px-2 py-1 rounded-full">
                                Disponible
                            </span>
                        </div>
                        
                        <div class="text-sm text-gray-300 space-y-2 mb-3">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-alt mr-2 text-yellow-400"></i>
                                <span class="text-gray-400">Vence:</span>
                                <span class="ml-1 font-medium">{{ \Carbon\Carbon::parse($session->end_date)->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-question-circle mr-2 text-yellow-400"></i>
                                <span class="text-gray-400">Preguntas:</span>
                                <span class="ml-1 font-medium">{{ $session->questions_count }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-clock mr-2 text-yellow-400"></i>
                                <span class="text-gray-400">Periodo:</span>
                                <span class="ml-1 font-medium">{{ $session->academic_period }}</span>
                            </div>
                        </div>

                        <!-- Información de preguntas -->
                        @if($session->questions->count() > 0)
                        <div class="mb-4 p-3 bg-gray-900/50 rounded border border-gray-700">
                            <p class="text-xs text-gray-400 mb-2 font-semibold">📝 PREGUNTAS DE ESTA EVALUACIÓN:</p>
                            <div class="space-y-2">
                                @foreach($session->questions as $index => $question)
                                <div class="flex items-start text-sm">
                                    <span class="text-blue-400 mr-2 text-xs mt-1">•</span>
                                    <span class="text-gray-300">
                                        {{ $question->question_text }}
                                        @if($question->question_type == 'multiple_choice')
                                            <span class="text-green-400 text-xs ml-2">(Opción múltiple)</span>
                                        @else
                                            <span class="text-purple-400 text-xs ml-2">(Texto abierto)</span>
                                        @endif
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="mt-4">
                            <a href="{{ route('evaluacion.student.evaluations.select-instructor', $session->id) }}" 
                               class="bg-yellow-500 hover:bg-yellow-600 text-gray-900 font-semibold px-4 py-3 rounded-lg transition-colors flex items-center justify-center text-sm w-full group">
                                <i class="fas fa-play mr-2 group-hover:scale-110 transition-transform"></i> 
                                COMENZAR EVALUACIÓN
                            </a>
                            <p class="text-xs text-gray-500 text-center mt-2">
                                Tiempo estimado: {{ $session->questions_count * 2 }} minutos
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-check-circle text-4xl text-gray-500 mb-3"></i>
                    <p class="text-gray-400">No tienes evaluaciones pendientes</p>
                    <p class="text-gray-500 text-sm mt-1">Las sesiones creadas en el módulo admin aparecerán aquí cuando estén activas</p>
                </div>
            @endif
        </div>

        <!-- Todas las Sesiones -->
        <div class="bg-[#1f2937] rounded-xl border border-gray-800 p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-white">Todas las Sesiones</h2>
                <span class="text-custom-blue text-sm font-medium">
                    {{ $stats['totalSessions'] }} total
                </span>
            </div>

            @if($stats['totalSessions'] > 0)
                <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                    @foreach($allSessions as $session)
                    @php
                        $isAvailable = now()->between($session->start_date, $session->end_date);
                        $isUpcoming = now() < $session->start_date;
                        $isExpired = now() > $session->end_date;
                    @endphp
                    
                    <div class="bg-gray-800/50 rounded-lg p-4 border 
                        {{ $isAvailable ? 'border-green-500/30 hover:border-green-500' : 
                           ($isUpcoming ? 'border-blue-500/30 hover:border-blue-500' : 
                           'border-gray-600 hover:border-gray-500') }} 
                        transition-colors">
                        
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="font-semibold text-white text-sm">{{ $session->title }}</h3>
                            <span class="text-xs px-2 py-1 rounded-full 
                                {{ $isAvailable ? 'bg-green-500/20 text-green-400' : 
                                   ($isUpcoming ? 'bg-blue-500/20 text-blue-400' : 
                                   'bg-gray-500/20 text-gray-400') }}">
                                {{ $isAvailable ? '🟢 Disponible' : 
                                   ($isUpcoming ? '🔵 Próxima' : '🔴 Finalizada') }}
                            </span>
                        </div>
                        
                        <div class="text-sm text-gray-400 space-y-1 mb-3">
                            <div class="flex items-center justify-between">
                                <span>
                                    <i class="fas fa-question-circle mr-1"></i>
                                    {{ $session->questions_count }} preguntas
                                </span>
                                <span class="text-xs bg-gray-700 px-2 py-1 rounded">
                                    {{ $session->academic_period }}
                                </span>
                            </div>
                            <div class="flex items-center text-xs">
                                <i class="fas fa-calendar mr-1"></i>
                                {{ \Carbon\Carbon::parse($session->start_date)->format('d/m') }} - 
                                {{ \Carbon\Carbon::parse($session->end_date)->format('d/m/Y') }}
                            </div>
                        </div>

                        @if($isAvailable)
                            <a href="{{ route('evaluacion.student.evaluations.select-instructor', $session->id) }}" 
                               class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded text-sm transition-colors flex items-center justify-center w-full">
                                <i class="fas fa-play mr-2"></i> Comenzar Evaluación
                            </a>
                        @elseif($isUpcoming)
                            <div class="bg-blue-500/20 text-blue-400 px-3 py-2 rounded text-sm text-center">
                                <i class="fas fa-clock mr-1"></i>
                                Disponible el {{ \Carbon\Carbon::parse($session->start_date)->format('d/m/Y') }}
                            </div>
                        @else
                            <div class="bg-gray-700 text-gray-400 px-3 py-2 rounded text-sm text-center">
                                <i class="fas fa-ban mr-1"></i>
                                Evaluación finalizada
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-clipboard-list text-4xl text-gray-500 mb-3"></i>
                    <p class="text-gray-400">No hay sesiones disponibles</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <!-- Acciones Rápidas -->
<div class="mt-8 bg-[#1f2937] rounded-xl border border-gray-800 p-6">
    <h2 class="text-xl font-semibold text-white mb-6">Acciones Rápidas</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- BOTÓN 1: Ver Todas las Evaluaciones -->
        <a href="{{ route('evaluacion.student.evaluations.index') }}" 
           class="flex items-center p-4 bg-blue-500/10 hover:bg-blue-500/20 rounded-lg border border-blue-500/30 transition-colors group">
            <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center mr-4">
                <i class="fas fa-list-alt text-blue-400 text-xl"></i>
            </div>
            <div class="flex-1">
                <div class="font-semibold text-white">Ver Todas las Sesiones</div>
                <div class="text-sm text-blue-300">Explora todas las sesiones disponibles</div>
            </div>
            <i class="fas fa-arrow-right text-blue-400 group-hover:translate-x-1 transition-transform"></i>
        </a>

        
        <a href="{{ route('evaluacion.student.evaluations.history') }}" 
           class="flex items-center p-4 bg-green-500/10 hover:bg-green-500/20 rounded-lg border border-green-500/30 transition-colors group">
            <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center mr-4">
                <i class="fas fa-history text-green-400 text-xl"></i>
            </div>
            <div class="flex-1">
                <div class="font-semibold text-white">Historial de Sesiones</div>
                <div class="text-sm text-green-300">Revisa tus sesiones completadas</div>
            </div>
            <i class="fas fa-arrow-right text-green-400 group-hover:translate-x-1 transition-transform"></i>
        </a>
    </div>
</div>

@if($pendingSessions->count() > 0)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Efectos hover para las tarjetas de sesión
    const sessionCards = document.querySelectorAll('.bg-gray-800\\/50');
    sessionCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.transition = 'all 0.2s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>
@endpush
@endif
@endsection