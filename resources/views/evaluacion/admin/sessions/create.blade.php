@extends('evaluacion.layouts.app')

@section('title', 'Crear Nueva Sesión de Evaluación')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white">Crear Nueva Sesión</h1>
            <p class="text-gray-400 mt-2">Configura una nueva sesión de evaluación para profesores</p>
        </div>
        <a href="{{ route('evaluacion.admin.dashboard') }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
    </div>

    <!-- Formulario -->
    <div class="bg-[#111115] rounded-xl border border-gray-800 p-6">
        <form action="{{ route('evaluacion.admin.sessions.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Título -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-300 mb-2">
                        Título de la Sesión *
                    </label>
                    <input type="text" 
                           name="title" 
                           id="title"
                           value="{{ old('title') }}"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-custom-blue focus:ring-1 focus:ring-custom-blue"
                           placeholder="Ej: Evaluación Docente Primer Semestre 2024"
                           required>
                    @error('title')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descripción -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-300 mb-2">
                        Descripción
                    </label>
                    <textarea name="description" 
                              id="description"
                              rows="3"
                              class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-custom-blue focus:ring-1 focus:ring-custom-blue"
                              placeholder="Describe el propósito de esta evaluación...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fecha de Inicio -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-300 mb-2">
                        Fecha de Inicio *
                    </label>
                    <input type="date" 
                           name="start_date" 
                           id="start_date"
                           value="{{ old('start_date') }}"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-custom-blue focus:ring-1 focus:ring-custom-blue"
                           required>
                    @error('start_date')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fecha de Fin -->
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-300 mb-2">
                        Fecha de Fin *
                    </label>
                    <input type="date" 
                           name="end_date" 
                           id="end_date"
                           value="{{ old('end_date') }}"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-custom-blue focus:ring-1 focus:ring-custom-blue"
                           required>
                    @error('end_date')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Período Académico -->
                <div class="md:col-span-2">
                    <label for="academic_period" class="block text-sm font-medium text-gray-300 mb-2">
                        Período Académico *
                    </label>
                    <select name="academic_period" 
                            id="academic_period"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-custom-blue focus:ring-1 focus:ring-custom-blue"
                            required>
                        <option value="">Seleccionar período académico</option>
                        <option value="2024-1" {{ old('academic_period') == '2024-1' ? 'selected' : '' }}>2024-1 - Primer Semestre 2024</option>
                        <option value="2024-2" {{ old('academic_period') == '2024-2' ? 'selected' : '' }}>2024-2 - Segundo Semestre 2024</option>
                        <option value="2024-A" {{ old('academic_period') == '2024-A' ? 'selected' : '' }}>2024-A - Año Académico 2024</option>
                        <option value="2025-1" {{ old('academic_period') == '2025-1' ? 'selected' : '' }}>2025-1 - Primer Semestre 2025</option>
                        <option value="2025-2" {{ old('academic_period') == '2025-2' ? 'selected' : '' }}>2025-2 - Segundo Semestre 2025</option>
                    </select>
                    @error('academic_period')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-700">
                <a href="{{ route('evaluacion.admin.sessions.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="bg-custom-blue hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors flex items-center">
                    <i class="fas fa-save mr-2"></i> Crear Sesión
                </button>
            </div>
        </form>
    </div>

    <!-- Información adicional -->
    <div class="mt-6 bg-blue-500/10 border border-blue-500/30 rounded-lg p-4">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-400 mt-1 mr-3"></i>
            <div>
                <h4 class="text-blue-400 font-semibold">Información importante</h4>
                <p class="text-gray-400 text-sm mt-1">
                    Después de crear la sesión, podrás agregar las preguntas de evaluación que los estudiantes responderán sobre los profesores.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
// Validación de fechas
document.addEventListener('DOMContentLoaded', function() {
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    
    // Establecer fecha mínima como hoy
    const today = new Date().toISOString().split('T')[0];
    startDate.min = today;
    endDate.min = today;
    
    // Validar que end_date sea después de start_date
    startDate.addEventListener('change', function() {
        endDate.min = this.value;
    });
});
</script>
@endsection