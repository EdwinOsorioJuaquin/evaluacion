{{-- resources/views/student/dashboard.blade.php --}}
@extends('satisfaccion.layouts.app')

@section('title', 'Encuestas Disponibles')

@section('content')
<div class="min-h-screen bg-night text-white px-6 py-12 font-sans">
    <div class="max-w-7xl mx-auto">

        <!-- Encabezado -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-extrabold text-deepSky mb-4 tracking-wide">📝 Encuestas Disponibles</h1>
            <p class="text-grayish text-lg">Responde las encuestas asignadas y comparte tu experiencia.</p>
        </div>

        <!-- Si no existe registro de estudiante -->
        @if(!$studentId)
            <div class="max-w-md mx-auto bg-darkPurple rounded-2xl shadow-xl p-10 text-center border border-grayish">
                <div class="text-6xl mb-4">⚠️</div>
                <h2 class="text-2xl font-bold text-deepSky mb-2">No tienes un perfil de estudiante</h2>
                <p class="text-grayish text-base">Contacta al administrador para habilitar tu acceso a las encuestas.</p>
            </div>
        @elseif($surveys->isEmpty())
            <!-- Si no hay encuestas activas -->
            <div class="max-w-md mx-auto bg-darkPurple rounded-2xl shadow-xl p-10 text-center border border-grayish">
                <div class="text-6xl mb-4">😕</div>
                <h2 class="text-2xl font-bold text-deepSky mb-2">No hay encuestas disponibles</h2>
                <p class="text-grayish text-base">Cuando se publique una encuesta, aparecerá aquí.</p>
            </div>
        @else
            <!-- Listado de encuestas -->
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($surveys as $survey)
                    @php
                            $alreadyAnswered = $survey->questions
                            ->pluck('responses')
                            ->flatten()
                            ->where('id_student', $studentId)
                            ->isNotEmpty();
                    @endphp

                    <div class="bg-darkPurple rounded-2xl shadow-lg border border-grayish hover:bg-smokyBlack transition duration-300 flex flex-col">
                        <!-- Título -->
                        <div class="px-6 py-4 border-b border-grayish">
                            <h2 class="text-xl font-bold text-white truncate">
                                {{ $survey->qualification }}
                            </h2>
                        </div>

                        <!-- Contenido -->
                        <div class="p-6 flex flex-col flex-1">
                            <p class="text-grayish mb-6 flex-1">
                                {{ $survey->description ?? 'Sin descripción' }}
                            </p>

                            @if(!$alreadyAnswered)
                                <!-- Botón para responder -->
                                <a href="{{ route('satisfaccion.student.surveys.show', $survey->id) }}"
                                   class="mt-auto inline-block text-center bg-deepSky hover:bg-blue-500 text-night font-bold py-2 px-4 rounded-xl transition duration-300">
                                    📥 Responder
                                </a>
                            @else
                                <!-- Estado ya respondida -->
                                <span class="mt-auto inline-block text-center bg-grayish text-night font-bold py-2 px-4 rounded-xl">
                                    ✅ Ya respondida
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>
@endsection

