@extends('evaluacion.layouts.app')

@section('title', 'Evaluación - ' . $session->title)

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header con información del instructor -->
    <div class="bg-[#1f2937] rounded-xl border border-green-500/30 p-6 mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-green-500/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-tie text-green-400 text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">
                        Evaluando a: {{ $instructor->first_name }} {{ $instructor->last_name }}
                    </h1>
                    <p class="text-gray-400">Sesión: {{ $session->title }}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="bg-blue-500/20 text-blue-400 text-sm px-3 py-1 rounded-full">
                    {{ $session->questions->count() }} preguntas
                </span>
            </div>
        </div>
    </div>

    <!-- Formulario de evaluación SIN JavaScript -->
    <form action="{{ route('evaluacion.student.evaluations.submit', $session->id) }}" method="POST">
        @csrf
        <input type="hidden" name="instructor_id" value="{{ $instructor->id }}">

        <div class="space-y-6">
            @foreach($session->questions as $index => $question)
            <div class="bg-[#1f2937] rounded-xl border border-gray-800 p-6">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-500/20 rounded-full flex items-center justify-center">
                        <span class="text-blue-400 font-bold text-lg">{{ $index + 1 }}</span>
                    </div>
                    
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-white mb-4">
                            {{ $question->question_text }}
                        </h3>

                        @if($question->question_type == 'scale_1_5' && $question->options->count() > 0)
                            <div class="space-y-3">
                                @foreach($question->options as $option)
                                <label class="flex items-center space-x-3 p-3 bg-gray-800/50 rounded-lg border border-gray-700 hover:border-blue-500 transition-colors cursor-pointer">
                                    <input type="radio" 
                                           name="responses[{{ $question->id }}]" 
                                           value="{{ $option->id }}"
                                           class="text-blue-500 focus:ring-blue-500"
                                           required>
                                    <span class="text-gray-300">{{ $option->option_text }}</span>
                                </label>
                                @endforeach
                            </div>
                        @else
                            <textarea name="responses[{{ $question->id }}]" 
                                      rows="4"
                                      class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:border-blue-500 focus:ring-blue-500"
                                      placeholder="Escribe tu respuesta aquí..."
                                      required></textarea>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Botones de acción -->
        <div class="mt-8 flex justify-between items-center">
            <a href="{{ route('evaluacion.student.evaluations.select-instructor', $session->id) }}" 
               class="flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Cambiar Docente
            </a>

            <button type="submit" 
                    class="flex items-center px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors font-semibold">
                <i class="fas fa-paper-plane mr-2"></i>
                Enviar Evaluación
            </button>
        </div>
    </form>
</div>

{{-- SIN JAVASCRIPT - ELIMINAR TODO LO DE ABAJO --}}
@endsection