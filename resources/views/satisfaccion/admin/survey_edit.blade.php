{{-- resources/views/admin/surveys/survey_edit.blade.php --}}
@extends('satisfaccion.layouts.app')

@section('title', 'Editar Encuesta')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-12 bg-smokyBlack text-white rounded-2xl shadow-xl">

    <h1 class="text-3xl font-extrabold text-deepSky text-center mb-10 tracking-wide">✏️ Editar Encuesta</h1>

    <!-- Formulario de edición de encuesta -->
    <form method="POST" action="{{ route('satisfaccion.admin.surveys.update', $survey->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label for="qualification" class="block text-grayish font-semibold mb-2">Título</label>
            <input type="text" name="qualification" id="qualification" value="{{ $survey->qualification }}" required
                   class="w-full px-4 py-3 bg-darkPurple text-white border border-grayish rounded-xl focus:outline-none focus:ring-2 focus:ring-deepSky focus:border-deepSky placeholder-grayish"
                   placeholder="Título de la encuesta">
        </div>

        <div>
            <label for="description" class="block text-grayish font-semibold mb-2">Descripción</label>
            <textarea name="description" id="description" rows="4"
                      class="w-full px-4 py-3 bg-darkPurple text-white border border-grayish rounded-xl focus:outline-none focus:ring-2 focus:ring-deepSky focus:border-deepSky placeholder-grayish"
                      placeholder="Descripción detallada">{{ $survey->description }}</textarea>
        </div>

        <button type="submit"
                class="w-full bg-yellow-500 hover:bg-yellow-600 text-night font-bold py-3 rounded-xl transition duration-300">
            💾 Actualizar Encuesta
        </button>
    </form>

    <!-- Lista de preguntas -->
    <h2 class="text-2xl font-bold mt-12 mb-6 text-deepSky">📋 Preguntas</h2>
    <ul class="space-y-4">
        @foreach($survey->questions as $q)
            <li class="flex justify-between items-center bg-darkPurple p-4 rounded-xl shadow-md hover:bg-smokyBlack transition">
                <span class="text-gray-200">{{ $q->text }}</span>
                <form method="POST" action="{{ route('satisfaccion.admin.questions.destroy', $q->id) }}">
                    @csrf
                    @method('DELETE')
                    <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg transition-colors">
                        🗑 Eliminar
                    </button>
                </form>
            </li>
        @endforeach
    </ul>

    <!-- Agregar nueva pregunta -->
    <form method="POST" action="{{ route('satisfaccion.admin.questions.store', $survey->id) }}" class="mt-10 space-y-4">
        @csrf
        <input type="text" name="text" placeholder="Nueva pregunta" required
               class="w-full px-4 py-3 bg-darkPurple text-white border border-grayish rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 placeholder-grayish">
        <button type="submit"
                class="w-full bg-green-500 hover:bg-green-600 text-night font-bold py-3 rounded-xl transition duration-300">
            ➕ Agregar Pregunta
        </button>
    </form>
</div>
@endsection

