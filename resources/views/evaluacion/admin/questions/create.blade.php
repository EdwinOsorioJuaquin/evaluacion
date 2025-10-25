@extends('evaluacion.layouts.app')

@section('title', 'Crear Nueva Pregunta')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white">Crear Nueva Pregunta</h1>
            <p class="text-gray-400 mt-2">Sesión: {{ $session->title }}</p>
        </div>
        <a href="{{ route('evaluacion.admin.sessions.questions.index', $session->id) }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
    </div>

    <!-- Formulario Mejorado -->
    <div class="bg-[#111115] rounded-xl border border-gray-800 p-6">
        <form action="{{ route('evaluacion.admin.sessions.questions.store', $session->id) }}" method="POST" id="questionForm">
            @csrf
            
            <!-- Texto de la Pregunta -->
            <div class="mb-6">
                <label for="question_text" class="block text-sm font-medium text-gray-300 mb-3">
                    <i class="fas fa-question-circle mr-2 text-custom-blue"></i>
                    Texto de la Pregunta *
                </label>
                <textarea 
                    name="question_text" 
                    id="question_text" 
                    rows="4"
                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-custom-blue focus:ring-2 focus:ring-custom-blue/20 transition-all duration-200"
                    placeholder="Ej: ¿El profesor explica los temas de manera clara y comprensible?"
                    required
                    oninput="updateCharCount(this)"
                >{{ old('question_text') }}</textarea>
                <div class="flex justify-between items-center mt-2">
                    <div class="text-sm text-gray-400">
                        <span id="charCount">0</span> caracteres
                    </div>
                    @error('question_text')
                        <p class="text-red-400 text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Configuración de la Pregunta -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Tipo de Pregunta -->
                <div>
                    <label for="question_type" class="block text-sm font-medium text-gray-300 mb-3">
                        <i class="fas fa-list-alt mr-2 text-custom-blue"></i>
                        Tipo de Pregunta *
                    </label>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <input type="radio" name="question_type" id="scale_1_5" value="scale_1_5" 
                                   class="hidden peer" {{ old('question_type', 'scale_1_5') == 'scale_1_5' ? 'checked' : '' }} required>
                            <label for="scale_1_5" 
                                   class="flex items-center justify-between w-full p-4 bg-gray-800 border-2 border-gray-700 rounded-lg cursor-pointer peer-checked:border-custom-blue peer-checked:bg-blue-500/10 transition-all duration-200">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-list-ol text-blue-400"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-white">Escala 1-5</div>
                                        <div class="text-sm text-gray-400">Pregunta con opciones numéricas</div>
                                    </div>
                                </div>
                                <div class="w-4 h-4 border-2 border-gray-500 rounded-full peer-checked:bg-custom-blue peer-checked:border-custom-blue"></div>
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="radio" name="question_type" id="text" value="text" 
                                   class="hidden peer" {{ old('question_type') == 'text' ? 'checked' : '' }}>
                            <label for="text" 
                                   class="flex items-center justify-between w-full p-4 bg-gray-800 border-2 border-gray-700 rounded-lg cursor-pointer peer-checked:border-custom-blue peer-checked:bg-blue-500/10 transition-all duration-200">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-yellow-500/20 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-font text-yellow-400"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-white">Texto Libre</div>
                                        <div class="text-sm text-gray-400">Respuesta abierta del estudiante</div>
                                    </div>
                                </div>
                                <div class="w-4 h-4 border-2 border-gray-500 rounded-full peer-checked:bg-custom-blue peer-checked:border-custom-blue"></div>
                            </label>
                        </div>
                    </div>
                    @error('question_type')
                        <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Configuraciones Adicionales -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-3">
                        <i class="fas fa-cog mr-2 text-custom-blue"></i>
                        Configuraciones
                    </label>
                    <div class="space-y-4 p-4 bg-gray-800/50 rounded-lg border border-gray-700">
                        <!-- Requerida -->
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium text-white">Pregunta Requerida</div>
                                <div class="text-sm text-gray-400">El estudiante debe responder esta pregunta</div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_required" value="1" 
                                       class="sr-only peer" {{ old('is_required', true) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-custom-blue"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Opciones para Escala 1-5 -->
            <div id="options-section" class="mb-6 transition-all duration-300">
                <label class="block text-sm font-medium text-gray-300 mb-4">
                    <i class="fas fa-sliders-h mr-2 text-custom-blue"></i>
                    Opciones de Escala (1-5) *
                </label>
                <div class="space-y-3" id="options-container">
@php
    $defaultOptions = [
        1 => 'Totalmente de Acuerdo',
        2 => 'De Acuerdo', 
        3 => 'Neutral',
        4 => 'En Desacuerdo',
        5 => 'Totalmente en Desacuerdo'
    ];
@endphp

@for($i = 1; $i <= 5; $i++)
<div class="flex items-center space-x-3 p-3 bg-gray-800/50 rounded-lg border border-gray-700 hover:border-gray-600 transition-colors">
    <div class="w-10 h-10 bg-custom-blue rounded-lg flex items-center justify-center text-white font-semibold">
        {{ $i }}
    </div>
    <input type="hidden" name="options[{{ $i }}][value]" value="{{ $i }}">
    <input type="text" 
           name="options[{{ $i }}][text]" 
           class="flex-1 bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:border-custom-blue focus:ring-1 focus:ring-custom-blue"
           placeholder="Descripción para valor {{ $i }}"
           value="{{ old('options.' . $i . '.text', $defaultOptions[$i]) }}"
           required>
    @error('options.' . $i . '.text')
        <p class="text-red-400 text-sm">{{ $message }}</p>
    @enderror
</div>
@endfor
                </div>
<p class="text-sm text-gray-400 mt-3">
    <i class="fas fa-info-circle mr-1"></i>
    Escala de evaluación: 1 (Totalmente de Acuerdo) a 5 (Totalmente en Desacuerdo)
</p>
            </div>

            <!-- Botones de Acción -->
            <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-700">
                <a href="{{ route('evaluacion.admin.sessions.questions.index', $session->id) }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors flex items-center">
                    <i class="fas fa-times mr-2"></i> Cancelar
                </a>
                <button type="submit" 
                        class="bg-custom-blue hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors flex items-center shadow-lg shadow-blue-500/25">
                    <i class="fas fa-save mr-2"></i> Guardar Pregunta
                </button>
            </div>
        </form>
    </div>

    <!-- Ejemplos de Preguntas -->
    <div class="mt-8 bg-blue-500/10 border border-blue-500/30 rounded-xl p-6">
        <div class="flex items-start">
            <i class="fas fa-lightbulb text-blue-400 mt-1 mr-3 text-lg"></i>
            <div>
                <h4 class="text-blue-400 font-semibold text-lg mb-3">Ejemplos de Preguntas para Docentes</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-blue-500/5 rounded-lg p-4 border border-blue-500/20">
                        <h5 class="text-white font-medium mb-2">Preguntas de Escala 1-5</h5>
                        <ul class="text-sm text-gray-400 space-y-1">
                            <li>• "El profesor domina los temas que enseña"</li>
                            <li>• "Las explicaciones son claras y comprensibles"</li>
                            <li>• "El profesor es puntual en las clases"</li>
                            <li>• "Los materiales de estudio son adecuados"</li>
                        </ul>
                    </div>
                    <div class="bg-yellow-500/5 rounded-lg p-4 border border-yellow-500/20">
                        <h5 class="text-white font-medium mb-2">Preguntas de Texto Libre</h5>
                        <ul class="text-sm text-gray-400 space-y-1">
                            <li>• "¿Qué aspecto positivo destacaría del profesor?"</li>
                            <li>• "¿Qué sugerencias de mejora le daría al profesor?"</li>
                            <li>• "Comentarios adicionales sobre la metodología..."</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const questionTypeRadios = document.querySelectorAll('input[name="question_type"]');
    const optionsSection = document.getElementById('options-section');
    
    // Función para mostrar/ocultar opciones
    function toggleOptionsSection() {
        const selectedType = document.querySelector('input[name="question_type"]:checked').value;
        
        if (selectedType === 'scale_1_5') {
            optionsSection.style.display = 'block';
            // Hacer requeridos los campos de opciones
            document.querySelectorAll('#options-container input[type="text"]').forEach(input => {
                input.required = true;
            });
        } else {
            optionsSection.style.display = 'none';
            // Quitar requerido de los campos de opciones
            document.querySelectorAll('#options-container input[type="text"]').forEach(input => {
                input.required = false;
            });
        }
    }
    
    // Contador de caracteres
    function updateCharCount(textarea) {
        const charCount = document.getElementById('charCount');
        charCount.textContent = textarea.value.length;
        
        // Cambiar color según la longitud
        if (textarea.value.length > 200) {
            charCount.classList.add('text-yellow-400');
        } else {
            charCount.classList.remove('text-yellow-400');
        }
    }
    
    // Inicializar
    toggleOptionsSection();
    updateCharCount(document.getElementById('question_text'));
    
    // Event listeners
    questionTypeRadios.forEach(radio => {
        radio.addEventListener('change', toggleOptionsSection);
    });
    
    // Validación antes de enviar
    document.getElementById('questionForm').addEventListener('submit', function(e) {
        const questionText = document.getElementById('question_text').value.trim();
        if (!questionText) {
            e.preventDefault();
            alert('Por favor, escribe el texto de la pregunta.');
            return;
        }
        
        // Validación adicional para preguntas de escala
        const selectedType = document.querySelector('input[name="question_type"]:checked').value;
        if (selectedType === 'scale_1_5') {
            const optionInputs = document.querySelectorAll('#options-container input[type="text"]');
            let allFilled = true;
            optionInputs.forEach(input => {
                if (!input.value.trim()) {
                    allFilled = false;
                    input.classList.add('border-red-500');
                } else {
                    input.classList.remove('border-red-500');
                }
            });
            
            if (!allFilled) {
                e.preventDefault();
                alert('Por favor, completa todas las opciones de la escala 1-5.');
                return;
            }
        }
    });
});
</script>

<style>
/* Animaciones suaves */
#options-section {
    transition: all 0.3s ease-in-out;
}

/* Mejora el focus de los inputs */
input:focus, textarea:focus {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}

/* Efecto hover en las opciones de radio */
input[name="question_type"] + label:hover {
    border-color: #3b82f6;
    background-color: rgba(59, 130, 246, 0.05);
}
</style>
@endsection