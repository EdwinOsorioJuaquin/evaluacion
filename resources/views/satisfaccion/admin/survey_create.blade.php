{{-- resources/views/admin//surveys/survey_create.blade.php --}}
@extends('satisfaccion.layouts.app')

@section('title', 'Crear Encuesta')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-12 bg-smokyBlack text-white rounded-2xl shadow-xl">

    <h1 class="text-3xl font-extrabold text-deepSky text-center mb-10 tracking-wide">📝 Crear Nueva Encuesta</h1>

    <form method="POST" action="{{ route('satisfaccion.admin.surveys.store') }}" class="space-y-6">
        @csrf

        <div>
            <label for="qualification" class="block text-grayish font-semibold mb-2">Título</label>
            <input type="text" name="qualification" id="qualification" required
                   class="w-full px-4 py-3 bg-darkPurple text-white border border-grayish rounded-xl focus:outline-none focus:ring-2 focus:ring-deepSky focus:border-deepSky placeholder-grayish"
                   placeholder="Título de la encuesta">
        </div>

        <div>
            <label for="description" class="block text-grayish font-semibold mb-2">Descripción</label>
            <textarea name="description" id="description" rows="4"
                      class="w-full px-4 py-3 bg-darkPurple text-white border border-grayish rounded-xl focus:outline-none focus:ring-2 focus:ring-deepSky focus:border-deepSky placeholder-grayish"
                      placeholder="Descripción detallada"></textarea>
        </div>

        <button type="submit"
                class="w-full bg-green-500 hover:bg-green-600 text-night font-bold py-3 rounded-xl transition duration-300">
            💾 Guardar Encuesta
        </button>
    </form>
</div>
@endsection
