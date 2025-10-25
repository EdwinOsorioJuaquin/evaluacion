@extends('evaluacion.layouts.app')

@section('title', 'Seleccionar Docente')

@section('content')
<div class="max-w-6xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-white">Evaluación de Docentes</h1>
        <p class="text-gray-400 mt-2">Selecciona un docente para evaluar en esta sesión</p>
    </div>

    <!-- Alertas -->
    @if(session('error'))
        <div class="mb-6 bg-red-500/20 border border-red-500/50 text-red-300 px-4 py-3 rounded-lg flex items-center">
            <i class="fas fa-exclamation-circle mr-3"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(session('success'))
        <div class="mb-6 bg-green-500/20 border border-green-500/50 text-green-300 px-4 py-3 rounded-lg flex items-center">
            <i class="fas fa-check-circle mr-3"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Información de la sesión -->
    <div class="bg-gradient-to-r from-blue-500/10 to-purple-500/10 rounded-xl border border-blue-500/30 p-6 mb-8">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between">
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-white mb-2">{{ $session->title }}</h2>
                <p class="text-gray-300">{{ $session->description }}</p>
                <div class="flex flex-wrap gap-2 mt-3">
                    <span class="bg-blue-500/20 text-blue-400 text-sm px-3 py-1 rounded-full">
                        <i class="fas fa-question-circle mr-1"></i>{{ $session->questions_count }} preguntas
                    </span>
                    <span class="bg-purple-500/20 text-purple-400 text-sm px-3 py-1 rounded-full">
                        <i class="fas fa-calendar mr-1"></i>
                        {{ \Carbon\Carbon::parse($session->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($session->end_date)->format('d/m/Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de instructores -->
    <div class="bg-gray-800/50 rounded-xl border border-gray-700 p-6">
        <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
            <i class="fas fa-list-alt mr-3 text-blue-400"></i>
            Lista de Docentes
        </h2>
        
        @if($instructors->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($instructors as $instructor)
                    @php
                        $alreadyEvaluated = in_array($instructor->id, $evaluatedInstructors);
                    @endphp

                    <div class="relative">
                        @if($alreadyEvaluated)
                            <!-- DOCENTE EVALUADO - NO CLICKABLE -->
                            <div class="bg-gradient-to-br from-green-500/10 to-emerald-500/10 rounded-xl p-5 border-2 border-green-500/40 cursor-not-allowed transition-all duration-300">
                                <!-- Badge superior -->
                                <div class="absolute -top-2 -right-2">
                                    <span class="bg-green-500 text-white text-xs font-semibold px-3 py-1 rounded-full shadow-lg flex items-center">
                                        <i class="fas fa-check mr-1"></i>COMPLETADO
                                    </span>
                                </div>
                                
                                <!-- Contenido -->
                                <div class="text-center">
                                    <!-- Avatar -->
                                    <div class="w-20 h-20 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-green-500/30">
                                        <i class="fas fa-user-check text-green-400 text-2xl"></i>
                                    </div>
                                    
                                    <!-- Información del docente -->
                                    <h3 class="font-bold text-white text-lg mb-2">{{ $instructor->name }}</h3>
                                    
                                    @if($instructor->email)
                                    <p class="text-gray-400 text-sm mb-4 truncate">
                                        <i class="fas fa-envelope mr-1"></i>{{ $instructor->email }}
                                    </p>
                                    @endif

                                    <!-- Estado -->
                                    <div class="space-y-2">
                                        <div class="bg-green-500/20 text-green-300 px-4 py-2 rounded-lg">
                                            <i class="fas fa-check-circle mr-2"></i>
                                            Evaluación Realizada
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- DOCENTE PENDIENTE - CLICKABLE -->
                            <a href="{{ route('evaluacion.student.evaluations.show', ['session' => $session->id, 'instructor_id' => $instructor->id]) }}" 
                               class="block bg-gradient-to-br from-blue-500/10 to-indigo-500/10 rounded-xl p-5 border-2 border-gray-600 hover:border-blue-500 hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-300 group transform hover:-translate-y-1">
                                <!-- Contenido -->
                                <div class="text-center">
                                    <!-- Avatar -->
                                    <div class="w-20 h-20 bg-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-blue-500/30 group-hover:border-blue-400 group-hover:scale-110 transition-all duration-300">
                                        <i class="fas fa-user-edit text-blue-400 text-2xl group-hover:text-blue-300"></i>
                                    </div>
                                    
                                    <!-- Información del docente -->
                                    <h3 class="font-bold text-white text-lg mb-2 group-hover:text-blue-300 transition-colors">
                                        {{ $instructor->name }}
                                    </h3>
                                    
                                    @if($instructor->email)
                                    <p class="text-gray-400 text-sm mb-4 truncate group-hover:text-gray-300 transition-colors">
                                        <i class="fas fa-envelope mr-1"></i>{{ $instructor->email }}
                                    </p>
                                    @endif

                                    <!-- Botón de acción -->
                                    <div class="bg-blue-500/20 text-blue-300 px-4 py-3 rounded-lg group-hover:bg-blue-500/30 group-hover:text-blue-200 transition-all duration-300">
                                        <i class="fas fa-play-circle mr-2"></i>
                                        Iniciar Evaluación
                                    </div>
                                </div>
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <!-- Estado sin docentes -->
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-gray-500 text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-400 mb-2">No hay docentes disponibles</h3>
                <p class="text-gray-500">Contacta con el administrador del sistema</p>
            </div>
        @endif
    </div>

    <!-- Botones de acción -->
    <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center items-center">
        <a href="{{ route('evaluacion.student.dashboard') }}" 
           class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-300 font-semibold">
            <i class="fas fa-arrow-left mr-2"></i>
            Volver al Dashboard
        </a>
        
        @if($evaluatedCount > 0)
            <a href="{{ route('evaluacion.student.evaluations.history') }}" 
               class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-300 font-semibold">
                <i class="fas fa-history mr-2"></i>
                Ver Historial de Evaluaciones
            </a>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Efecto para tarjetas ya evaluadas (solo visual)
    const evaluatedCards = document.querySelectorAll('.cursor-not-allowed');
    evaluatedCards.forEach(card => {
        card.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Crear notificación toast
            showNotification('Ya evaluaste a este docente', 'success');
        });
    });

    // Función para mostrar notificaciones
    function showNotification(message, type = 'info') {
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-500' : 'bg-blue-500';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-info-circle';
        
        toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-4 rounded-xl shadow-2xl z-50 transform transition-transform duration-300 translate-x-full`;
        toast.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${icon} mr-3 text-xl"></i>
                <span class="font-semibold">${message}</span>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Animación de entrada
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 10);
        
        // Remover después de 4 segundos
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 4000);
    }
});
</script>

<style>
    /* Estilos adicionales para mejoras visuales */
    .group:hover .group-hover\:scale-110 {
        transform: scale(1.1);
    }
    
    .transform {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .hover\:-translate-y-1:hover {
        transform: translateY(-4px);
    }
</style>
@endpush
@endsection