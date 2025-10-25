@extends('evaluacion.layouts.app')

@section('title', 'Gestión de Preguntas - Admin')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white">Gestión de Preguntas</h1>
            <p class="text-gray-400 mt-2">Sesión: {{ $session->title }}</p>
            <p class="text-gray-400 text-sm">Crea las preguntas para evaluar a los docentes</p>
        </div>
        <a href="{{ route('evaluacion.admin.sessions.questions.create', $session->id) }}" 
           class="bg-custom-blue hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
            <i class="fas fa-plus mr-2"></i>Nueva Pregunta
        </a>
    </div>

    <!-- Botón para volver a sesiones -->
    <div class="mb-6">
        <a href="{{ route('evaluacion.admin.sessions.index') }}" 
           class="text-custom-blue hover:text-blue-400 transition-colors flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Volver a Sesiones de Evaluación
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-500/20 border border-green-500 text-green-400 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-500/20 border border-red-500 text-red-400 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-[#111115] p-4 rounded-xl border border-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Total Preguntas</p>
                    <p class="text-xl font-bold text-white mt-1">{{ $session->questions->count() }}</p>
                </div>
                <i class="fas fa-question-circle text-blue-400 text-xl"></i>
            </div>
        </div>
        
        <div class="bg-[#111115] p-4 rounded-xl border border-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Escala 1-5</p>
                    <p class="text-xl font-bold text-white mt-1">
                        {{ $session->questions->where('question_type', 'scale_1_5')->count() }}
                    </p>
                </div>
                <i class="fas fa-list-ol text-green-400 text-xl"></i>
            </div>
        </div>
        
        <div class="bg-[#111115] p-4 rounded-xl border border-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Texto Libre</p>
                    <p class="text-xl font-bold text-white mt-1">
                        {{ $session->questions->where('question_type', 'text')->count() }}
                    </p>
                </div>
                <i class="fas fa-font text-yellow-400 text-xl"></i>
            </div>
        </div>
        
        <div class="bg-[#111115] p-4 rounded-xl border border-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Activas</p>
                    <p class="text-xl font-bold text-white mt-1">
                        {{ $session->questions->where('status', 'active')->count() }}
                    </p>
                </div>
                <i class="fas fa-check-circle text-green-400 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Lista de Preguntas -->
    <div class="bg-[#111115] rounded-xl border border-gray-800 overflow-hidden">
        @if($session->questions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Orden
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Pregunta
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Tipo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Requerida
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($session->questions as $question)
                        <tr class="hover:bg-gray-800/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-300">{{ $question->question_order }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-white font-medium max-w-md">
                                    {{ $question->question_text }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($question->question_type == 'scale_1_5')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-500/20 text-blue-400">
                                        <i class="fas fa-list-ol mr-1"></i>Escala 1-5
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-500/20 text-yellow-400">
                                        <i class="fas fa-font mr-1"></i>Texto Libre
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($question->is_required)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/20 text-green-400">
                                        <i class="fas fa-check mr-1"></i>Sí
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-500/20 text-gray-400">
                                        <i class="fas fa-times mr-1"></i>No
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($question->status == 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/20 text-green-400">
                                        <i class="fas fa-play mr-1"></i>Activa
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500/20 text-red-400">
                                        <i class="fas fa-pause mr-1"></i>Inactiva
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('evaluacion.admin.sessions.questions.edit', [$session->id, $question->id]) }}" 
                                       class="text-yellow-400 hover:text-yellow-300 transition-colors"
                                       title="Editar pregunta">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    @if($question->status == 'active')
                                        <a href="{{ route('evaluacion.admin.sessions.questions.toggle-status', [$session->id, $question->id]) }}" 
                                           class="text-orange-400 hover:text-orange-300 transition-colors"
                                           title="Desactivar pregunta"
                                           onclick="return confirm('¿Desactivar esta pregunta?')">
                                            <i class="fas fa-pause"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('evaluacion.admin.sessions.questions.toggle-status', [$session->id, $question->id]) }}" 
                                           class="text-green-400 hover:text-green-300 transition-colors"
                                           title="Activar pregunta"
                                           onclick="return confirm('¿Activar esta pregunta?')">
                                            <i class="fas fa-play"></i>
                                        </a>
                                    @endif
                                    
                                    <a href="{{ route('evaluacion.admin.sessions.questions.clone', [$session->id, $question->id]) }}" 
                                       class="text-blue-400 hover:text-blue-300 transition-colors"
                                       title="Clonar pregunta"
                                       onclick="return confirm('¿Clonar esta pregunta?')">
                                        <i class="fas fa-copy"></i>
                                    </a>
                                    
                                    <form action="{{ route('evaluacion.admin.sessions.questions.destroy', [$session->id, $question->id]) }}" 
                                          method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-400 hover:text-red-300 transition-colors"
                                                title="Eliminar pregunta"
                                                onclick="return confirm('¿Eliminar esta pregunta permanentemente?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Información de la sesión -->
            <div class="bg-gray-800/30 px-6 py-4 border-t border-gray-700">
                <div class="flex justify-between items-center text-sm text-gray-400">
                    <div>
                        <strong>Total de preguntas:</strong> {{ $session->questions->count() }}
                    </div>
                    <div>
                        <strong>Sesión:</strong> {{ $session->title }} | 
                        <strong>Estado:</strong> 
                        <span class="capitalize {{ $session->status == 'active' ? 'text-green-400' : 'text-red-400' }}">
                            {{ $session->status }}
                        </span>
                    </div>
                </div>
            </div>

        @else
            <div class="text-center py-12">
                <div class="text-gray-400 text-6xl mb-4">
                    <i class="fas fa-question-circle"></i>
                </div>
                <h3 class="text-xl font-medium text-white mb-2">No hay preguntas</h3>
                <p class="text-gray-400 mb-6">Esta sesión de evaluación no tiene preguntas configuradas.</p>
                <a href="{{ route('evaluacion.admin.sessions.questions.create', $session->id) }}" 
                   class="bg-custom-blue hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>Crear Primera Pregunta
                </a>
            </div>
        @endif
    </div>

    <!-- Información adicional -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-[#111115] rounded-xl border border-gray-800 p-6">
            <div class="flex items-center">
                <div class="bg-blue-500/20 p-3 rounded-lg mr-4">
                    <i class="fas fa-list-ol text-blue-400 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-white font-semibold">Preguntas de Escala</h3>
                    <p class="text-2xl font-bold text-white mt-1">
                        {{ $session->questions->where('question_type', 'scale_1_5')->count() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-[#111115] rounded-xl border border-gray-800 p-6">
            <div class="flex items-center">
                <div class="bg-yellow-500/20 p-3 rounded-lg mr-4">
                    <i class="fas fa-font text-yellow-400 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-white font-semibold">Preguntas de Texto</h3>
                    <p class="text-2xl font-bold text-white mt-1">
                        {{ $session->questions->where('question_type', 'text')->count() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-[#111115] rounded-xl border border-gray-800 p-6">
            <div class="flex items-center">
                <div class="bg-green-500/20 p-3 rounded-lg mr-4">
                    <i class="fas fa-check-circle text-green-400 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-white font-semibold">Preguntas Activas</h3>
                    <p class="text-2xl font-bold text-white mt-1">
                        {{ $session->questions->where('status', 'active')->count() }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script para confirmaciones -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Agregar confirmación a todos los formularios de eliminación
    const deleteForms = document.querySelectorAll('form[method="POST"]');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('¿Estás seguro de que quieres eliminar esta pregunta?')) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endsection