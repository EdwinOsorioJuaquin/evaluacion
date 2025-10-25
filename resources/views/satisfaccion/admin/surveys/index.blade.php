{{-- resources/views/admin/surveys/index.blade.php --}}
@extends('satisfaccion.layouts.app')

@section('title', 'Encuestas')

@section('content')
<div class="min-h-screen bg-night text-white px-6 py-12 font-sans">
    <div class="max-w-7xl mx-auto">

        {{-- TÍTULO Y BOTÓN --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
            <div>
                <h1 class="text-4xl font-extrabold text-deepSky tracking-wide mb-2">Encuestas</h1>
                <p class="text-grayish text-sm">Gestiona, edita y genera reportes de las encuestas creadas.</p>
            </div>
            <a href="{{ route('satisfaccion.admin.surveys.create') }}"
               class="bg-deepSky hover:bg-blue-500 text-night font-bold py-2 px-6 rounded-xl transition duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                ➕ Nueva Encuesta
            </a>
        </div>

        {{-- MENSAJE VACÍO --}}
        @if($surveys->isEmpty())
            <div class="bg-smokyBlack/70 backdrop-blur-lg border border-grayish p-10 rounded-2xl shadow-xl text-center">
                <p class="text-grayish text-lg">No hay encuestas creadas aún.</p>
            </div>
        @else

        {{-- TABLA --}}
        <div class="overflow-x-auto rounded-2xl shadow-2xl backdrop-blur-lg border border-gray-700">
            <table class="min-w-full text-white">
                <thead class="bg-[#1A1827] text-deepSky sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold uppercase text-sm tracking-wider">ID</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase text-sm tracking-wider">Título</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase text-sm tracking-wider">Categoría</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase text-sm tracking-wider">Estado</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase text-sm tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($surveys as $survey)
                    <tr class="border-t border-gray-700 hover:bg-smokyBlack/70 transition duration-200 ease-in-out">
                        <td class="px-6 py-4 text-gray-300">{{ $survey->id }}</td>
                        <td class="px-6 py-4 text-gray-300 font-medium">{{ $survey->qualification }}</td>
                        <td class="px-6 py-4 text-gray-300">{{ $survey->category->category_name ?? 'Sin categoría' }}</td>
                        <td class="px-6 py-4">
                            @if($survey->state === 'Activa')
                                <span class="bg-green-600/20 text-green-400 px-3 py-1 rounded-full text-sm font-semibold">Activa</span>
                            @else
                                <span class="bg-red-600/20 text-red-400 px-3 py-1 rounded-full text-sm font-semibold">{{ $survey->state }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 space-x-2 flex flex-wrap gap-2">

                            {{-- Botón Editar --}}
                            <a href="{{ route('satisfaccion.admin.surveys.edit', $survey->id) }}"
                               class="bg-yellow-500/20 border border-yellow-500 text-yellow-400 px-3 py-1 rounded-lg hover:bg-yellow-500 hover:text-night transition duration-200 font-semibold">
                                ✏️ Editar
                            </a>

                            {{-- Botón Eliminar --}}
                            <form method="POST" action="{{ route('satisfaccion.admin.surveys.destroy', $survey->id) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="bg-red-500/20 border border-red-500 text-red-400 px-3 py-1 rounded-lg hover:bg-red-500 hover:text-night transition duration-200 font-semibold">
                                    🗑️ Eliminar
                                </button>
                            </form>

                            {{-- Botón Ver reporte --}}
                            <a href="{{ route('satisfaccion.admin.reports.generate', $survey->id) }}"
                               class="bg-blue-500/20 border border-blue-500 text-blue-400 px-3 py-1 rounded-lg hover:bg-blue-500 hover:text-night transition duration-200 font-semibold">
                                📊 Reporte
                            </a>
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

