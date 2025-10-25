{{-- resources/views/surveys/index.blade.php --}}
@extends('satisfaccion.layouts.app')

@section('title', 'Encuestas Disponibles')

@section('content')
<div class="min-h-screen bg-night text-white px-6 py-12 font-sans">
    <div class="max-w-7xl mx-auto">

        <h1 class="text-4xl font-extrabold text-deepSky mb-10 tracking-wide">📋 Encuestas Disponibles</h1>

        @if($surveys->isEmpty())
            <div class="max-w-md mx-auto bg-darkPurple text-grayish rounded-2xl shadow-xl p-10 text-center border border-grayish">
                <div class="text-6xl mb-4">😕</div>
                <h2 class="text-2xl font-bold text-deepSky mb-2">No hay encuestas disponibles</h2>
                <p class="text-grayish text-base">Cuando se publique una encuesta, aparecerá aquí.</p>
            </div>
        @else
            <div class="overflow-x-auto rounded-2xl shadow-xl">
                <table class="min-w-full bg-darkPurple text-white">
                    <thead class="bg-night text-deepSky">
                        <tr>
                            <th class="px-6 py-4 text-left font-semibold">ID</th>
                            <th class="px-6 py-4 text-left font-semibold">Título</th>
                            <th class="px-6 py-4 text-left font-semibold">Estado</th>
                            <th class="px-6 py-4 text-left font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($surveys as $survey)
                        <tr class="border-t border-grayish hover:bg-smokyBlack transition">
                            <td class="px-6 py-4 text-gray-200">{{ $survey->id }}</td>
                            <td class="px-6 py-4 text-gray-200">{{ $survey->qualification }}</td>
                            <td class="px-6 py-4 text-gray-200">{{ $survey->state }}</td>
                            <td class="px-6 py-4">
                                @if($survey->state === 'Activa')
                                    <a href="{{ route('satisfaccion.student.surveys.show', $survey->id) }}"
                                       class="text-deepSky hover:underline font-medium">Responder</a>
                                @else
                                    <span class="text-grayish font-medium">No disponible</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>
</div>
@endsection
