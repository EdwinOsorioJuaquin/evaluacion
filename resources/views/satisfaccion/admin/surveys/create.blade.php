{{-- resources/views/admin/surveys/create.blade.php --}}
@extends('satisfaccion.layouts.app')

@section('title', 'Crear Encuesta')

@section('content')
<div class="max-w-3xl mx-auto px-8 py-12 bg-smokyBlack/80 backdrop-blur-lg text-white rounded-3xl shadow-2xl border border-gray-700">

    {{-- Título principal --}}
    <h1 class="text-4xl font-extrabold text-deepSky text-center mb-6 tracking-wide drop-shadow-lg">
        📝 Crear Nueva Encuesta
    </h1>
    <p class="text-grayish text-center mb-10 text-sm">
        Completa la información para registrar una nueva encuesta.
    </p>

    <form action="{{ route('satisfaccion.admin.surveys.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Título --}}
        <div>
            <label class="block text-grayish font-semibold mb-2">Título <span class="text-red-500">*</span></label>
            <input type="text" name="qualification"
                class="w-full px-4 py-3 bg-darkPurple/70 border border-grayish rounded-xl focus:outline-none focus:ring-2 focus:ring-deepSky focus:border-deepSky placeholder-grayish transition duration-200"
                placeholder="Ej: Encuesta de satisfacción 2025" required>
        </div>

        {{-- Descripción --}}
        <div>
            <label class="block text-grayish font-semibold mb-2">Descripción</label>
            <textarea name="description"
                class="w-full px-4 py-3 bg-darkPurple/70 border border-grayish rounded-xl focus:outline-none focus:ring-2 focus:ring-deepSky focus:border-deepSky placeholder-grayish transition duration-200 resize-none"
                rows="4" placeholder="Describe brevemente el objetivo de la encuesta..."></textarea>
        </div>

        {{-- Estado --}}
        <div>
            <label class="block text-grayish font-semibold mb-2">Estado</label>
            <select name="state"
                class="w-full px-4 py-3 bg-darkPurple/70 border border-grayish rounded-xl focus:outline-none focus:ring-2 focus:ring-deepSky focus:border-deepSky transition duration-200">
                <option value="Activa">🟢 Activa</option>
                <option value="Inactiva">🔴 Inactiva</option>
            </select>
        </div>

        {{-- Categoría --}}
        <div>
            <label for="id_category" class="block text-grayish font-semibold mb-2">Categoría <span class="text-red-500">*</span></label>
            <select name="id_category" id="id_category" required
                class="w-full px-4 py-3 bg-darkPurple/70 border border-grayish rounded-xl focus:outline-none focus:ring-2 focus:ring-deepSky focus:border-deepSky transition duration-200">
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Botón de acción --}}
        <button type="submit"
            class="w-full bg-green-500 hover:bg-green-600 text-night font-bold py-3 rounded-xl transition duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
            💾 Crear Encuesta
        </button>
    </form>
</div>
@endsection

