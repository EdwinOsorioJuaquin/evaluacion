@extends('evaluacion.layouts.app')

@section('title', 'Evaluaciones Disponibles')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white">Evaluaciones Disponibles</h1>
            <p class="text-gray-400 mt-2">Completa las evaluaciones asignadas</p>
        </div>
        <a href="{{ route('evaluacion.student.dashboard') }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
    </div>

    <!-- Lista de Evaluaciones -->
    <div class="space-y-6">
        @forelse($sessions as $session)
        <div class="bg-[#1f2937] rounded-xl border border-gray-800 p-6 hover:border-custom-blue transition-all duration-300">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold text-white">{{ $session->title }}</h3>
                        <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-sm font-medium">
                            Disponible
                        </span>
                    </div>
                    
                    @if($session->description)
                    <p class="text-gray-400 mb-4">{{ $session->description }}</p>
                    @endif
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-400">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-alt mr-2 text-custom-blue"></i>
                            <span>Inicio: {{ \Carbon\Carbon::parse($session->start_date)->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-clock mr-2 text-custom-blue"></i>
                            <span>Fin: {{ \Carbon\Carbon::parse($session->end_date)->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-question-circle mr-2 text-custom-blue"></i>
                            <span>{{ $session->questions_count }} preguntas</span>
                        </div>
                    </div>
                    
                    <div class="mt-4 text-sm text-gray-400">
                        <i class="fas fa-graduation-cap mr-2 text-custom-blue"></i>
                        Período: {{ $session->academic_period }}
                    </div>
                </div>
            </div>
            
            <!-- Acción -->
            <div class="flex justify-end mt-6 pt-6 border-t border-gray-700">
                <a href="{{ route('evaluacion.student.evaluations.show', $session->id) }}" 
                   class="bg-custom-blue hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors flex items-center shadow-lg shadow-blue-500/25">
                    <i class="fas fa-play-circle mr-2"></i> Comenzar Evaluación
                </a>
            </div>
        </div>
        @empty
        <div class="bg-[#1f2937] rounded-xl border border-gray-800 p-12 text-center">
            <i class="fas fa-check-circle text-4xl text-gray-500 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-400 mb-2">No hay evaluaciones disponibles</h3>
            <p class="text-gray-500">No tienes evaluaciones pendientes en este momento.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection