{{-- resources/views/admin/surveys/dashboard.blade.php --}}
@extends('satisfaccion.layouts.app')

@section('title', 'Panel Administrador')

@section('content')
<div class="min-h-screen bg-night text-white px-6 py-12 font-sans">
    <div class="max-w-7xl mx-auto">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-10">
            <h1 class="text-4xl font-extrabold text-deepSky mb-4 sm:mb-0 tracking-wide">📋 Encuestas</h1>
            <a href="{{ route('satisfaccion.admin.surveys.create') }}"
               class="bg-deepSky hover:bg-blue-500 text-night font-bold px-5 py-2 rounded-xl transition duration-300">
                + Nueva Encuesta
            </a>
        </div>

        <div class="overflow-x-auto rounded-2xl shadow-xl">
            <table class="min-w-full bg-darkPurple text-white">
                <thead class="bg-night text-deepSky">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold">Título</th>
                        <th class="px-6 py-4 text-center font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($surveys as $survey)
                        <tr class="border-t border-grayish hover:bg-smokyBlack transition">
                            <td class="px-6 py-4 text-gray-200">{{ $survey->qualification }}</td>
                            <td class="px-6 py-4 flex flex-wrap justify-center gap-2">
                                <a href="{{ route('satisfaccion.admin.surveys.edit', $survey->id) }}"
                                   class="bg-yellow-500 hover:bg-yellow-600 text-night font-bold px-3 py-1 rounded-lg transition duration-300">
                                    Editar
                                </a>

                                <form method="POST" action="{{ route('satisfaccion.admin.surveys.destroy', $survey->id) }}"
                                      onsubmit="return confirm('¿Eliminar esta encuesta?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="bg-red-500 hover:bg-red-600 text-white font-semibold px-3 py-1 rounded-lg transition duration-300">
                                        Eliminar
                                    </button>
                                </form>

                                <a href="{{ route('satisfaccion.admin.reports.generate', $survey->id) }}"
                                   class="bg-green-500 hover:bg-green-600 text-night font-bold px-3 py-1 rounded-lg transition duration-300">
                                    Reporte
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection

