{{-- resources/views/admin/surveys/edit.blade.php --}}
@extends('satisfaccion.layouts.app')

@section('title', 'Editar Encuesta')

@section('content')
<div class="max-w-4xl mx-auto px-8 py-12 bg-smokyBlack/80 backdrop-blur-lg text-white rounded-3xl shadow-2xl border border-gray-700">

    {{-- Título principal --}}
    <h1 class="text-4xl font-extrabold text-deepSky text-center mb-8 tracking-wide drop-shadow-lg">
        ✏️ Editar Encuesta
    </h1>

    {{-- Formulario de edición de encuesta --}}
    <form method="POST" action="{{ route('satisfaccion.admin.surveys.update', $survey->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Título --}}
        <div>
            <label class="block text-grayish font-semibold mb-2">Título <span class="text-red-500">*</span></label>
            <input type="text" name="qualification" value="{{ old('qualification', $survey->qualification) }}"
                class="w-full px-4 py-3 bg-darkPurple/70 border border-grayish rounded-xl focus:outline-none focus:ring-2 focus:ring-deepSky focus:border-deepSky placeholder-grayish transition duration-200"
                placeholder="Título de la encuesta" required>
        </div>

        {{-- Descripción --}}
        <div>
            <label class="block text-grayish font-semibold mb-2">Descripción</label>
            <textarea name="description" rows="4"
                class="w-full px-4 py-3 bg-darkPurple/70 border border-grayish rounded-xl focus:outline-none focus:ring-2 focus:ring-deepSky focus:border-deepSky placeholder-grayish transition duration-200 resize-none"
                placeholder="Descripción detallada">{{ old('description', $survey->description) }}</textarea>
        </div>

        {{-- Estado --}}
        <div>
            <label class="block text-grayish font-semibold mb-2">Estado</label>
            <select name="state"
                class="w-full px-4 py-3 bg-darkPurple/70 border border-grayish rounded-xl focus:outline-none focus:ring-2 focus:ring-deepSky focus:border-deepSky transition duration-200">
                <option value="Activa" {{ $survey->state === 'Activa' ? 'selected' : '' }}>🟢 Activa</option>
                <option value="Inactiva" {{ $survey->state === 'Inactiva' ? 'selected' : '' }}>🔴 Inactiva</option>
            </select>
        </div>

        {{-- Categoría --}}
        <div>
            <label for="id_category" class="block text-grayish font-semibold mb-2">Categoría <span class="text-red-500">*</span></label>
            <select name="id_category" id="id_category" required
                class="w-full px-4 py-3 bg-darkPurple/70 border border-grayish rounded-xl focus:outline-none focus:ring-2 focus:ring-deepSky focus:border-deepSky transition duration-200">
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $survey->id_category == $category->id ? 'selected' : '' }}>
                        {{ $category->category_name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Botón Guardar --}}
        <button type="submit"
            class="w-full bg-yellow-500 hover:bg-yellow-600 text-night font-bold py-3 rounded-xl transition duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
            💾 Guardar Cambios
        </button>
    </form>

    {{-- Lista de preguntas --}}
    <div class="mt-12">
        <h2 class="text-2xl font-bold mb-6 text-deepSky flex items-center gap-2">
            📋 Preguntas <span class="text-grayish text-sm font-normal">({{ $survey->questions->count() }})</span>
        </h2>

        @if($survey->questions->isEmpty())
            <p class="text-grayish italic text-center py-4">Aún no hay preguntas en esta encuesta.</p>
        @else
            <ul class="space-y-4">
                @foreach($survey->questions as $q)
                    <li class="flex justify-between items-center bg-darkPurple p-4 rounded-xl shadow-md border border-gray-700 hover:bg-[#1e1b3a] transition duration-300 group">
                        <span class="text-gray-200 font-medium">{{ $q->question_text }}</span>
                        <form method="POST" action="{{ route('satisfaccion.admin.questions.destroy', $q->id) }}">
                            @csrf
                            @method('DELETE')
                            <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-sm font-semibold transition transform hover:scale-105">
                                🗑 Eliminar
                            </button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    
</div>
@endsection
