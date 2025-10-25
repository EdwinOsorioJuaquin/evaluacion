{{-- resources/views/student/survey_show.blade.php --}}
@extends('satisfaccion.layouts.app')

@section('title', 'Responder Encuesta')

@section('content')
<div class="min-h-screen bg-night text-white px-6 py-12 font-sans">
    <div class="max-w-3xl mx-auto bg-smokyBlack rounded-2xl shadow-2xl p-8 md:p-12 border border-gray-800 backdrop-blur-md">

        <!-- Encabezado -->
        <div class="text-center mb-10">
            <h1 class="text-3xl sm:text-4xl font-extrabold bg-gradient-to-r from-deepSky to-blue-500 bg-clip-text text-transparent mb-2 tracking-wide">
                📝 {{ $survey->title }}
            </h1>
            <p class="text-grayish text-base">Por favor, responde honestamente cada pregunta.</p>
        </div>

        <!-- Formulario -->
        <form method="POST" action="{{ route('satisfaccion.student.surveys.submit', $survey->id) }}" class="space-y-8">
            @csrf

            @foreach($survey->questions as $q)
                <div class="bg-darkPurple rounded-xl p-5 shadow-md hover:shadow-lg border border-gray-700 transition-all duration-300">
                    <label for="question-{{ $q->id }}" class="block text-gray-200 font-semibold mb-3 text-lg">
                        {{ $q->question_text }}
                    </label>
                    <input type="text" 
                           name="responses[{{ $q->id }}]" 
                           id="question-{{ $q->id }}"
                           placeholder="Escribe tu respuesta aquí..."
                           required
                           class="w-full px-4 py-3 bg-[#1b1934] text-white border border-grayish rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 placeholder-gray-500 transition duration-300">
                </div>
            @endforeach

            <button type="submit"
                    class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-emerald-600 hover:to-green-500 text-night font-bold py-3 rounded-xl shadow-md hover:shadow-lg transform hover:scale-[1.02] transition duration-300">
                ✅ Enviar Respuestas
            </button>
        </form>

    </div>
</div>
@endsection
