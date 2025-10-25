@extends('satisfaccion.layouts.app')

@section('title', 'Agregar Preguntas')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-12 bg-smokyBlack text-white rounded-2xl shadow-xl">

    <h1 class="text-3xl font-extrabold text-deepSky text-center mb-10 tracking-wide">
        🧠 Agregar Preguntas a: {{ $survey->qualification }}
    </h1>

    <form action="{{ route('satisfaccion.admin.surveys.storeQuestions', $survey->id) }}" method="POST" class="space-y-8">
        @csrf

        <div id="questions-container">
            {{-- Primera pregunta base --}}
            <div class="question bg-darkPurple p-6 rounded-xl shadow-md mb-6">
                <label class="block text-grayish font-semibold mb-2">Pregunta</label>
                <input type="text" name="questions[0][text]"
                       class="w-full px-4 py-3 bg-smokyBlack text-white border border-grayish rounded-xl focus:outline-none focus:ring-2 focus:ring-deepSky focus:border-deepSky placeholder-grayish"
                       placeholder="Texto de la pregunta" required>
                <input type="hidden" name="questions[0][id_satisfaction_question]" value="1">

<label class="block text-grayish font-semibold mt-4 mb-2">Tipo de pregunta</label>
<select name="questions[0][type]"
    class="w-full px-4 py-3 bg-smokyBlack text-white border border-grayish rounded-xl">
    <option value="opcion_multiple">Opción múltiple</option>
    </select>

                <label class="block text-grayish font-semibold mt-4 mb-2">Opciones</label>
                <input type="text" name="questions[0][options][0][text]"
                       class="w-full px-4 py-2 mb-2 bg-smokyBlack text-white border border-grayish rounded-xl"
                       placeholder="Opción 1" required>
                <input type="text" name="questions[0][options][1][text]"
                       class="w-full px-4 py-2 mb-2 bg-smokyBlack text-white border border-grayish rounded-xl"
                       placeholder="Opción 2" required>
                <input type="text" name="questions[0][options][2][text]"
                       class="w-full px-4 py-2 mb-2 bg-smokyBlack text-white border border-grayish rounded-xl"
                       placeholder="Opción 3" required>
            </div>
        </div>

        {{-- Botón para agregar más preguntas --}}
        <button type="button" id="add-question-btn"
                class="w-full bg-blue-500 hover:bg-blue-600 text-night font-bold py-3 rounded-xl transition duration-300">
            ➕ Agregar otra pregunta
        </button>

        <button type="submit"
                class="w-full bg-green-500 hover:bg-green-600 text-night font-bold py-3 rounded-xl transition duration-300 mt-4">
            💾 Guardar Preguntas
        </button>
    </form>
</div>

{{-- Script para clonar preguntas dinámicamente --}}
<script>
    let questionIndex = 1;

    document.getElementById('add-question-btn').addEventListener('click', () => {
        const container = document.getElementById('questions-container');

        const newQuestion = document.createElement('div');
        newQuestion.classList.add('question', 'bg-darkPurple', 'p-6', 'rounded-xl', 'shadow-md', 'mb-6');

        newQuestion.innerHTML = `
    <label class="block text-grayish font-semibold mb-2">Pregunta</label>
    <input type="text" name="questions[${questionIndex}][text]"
        class="w-full px-4 py-3 bg-smokyBlack text-white border border-grayish rounded-xl focus:outline-none focus:ring-2 focus:ring-deepSky focus:border-deepSky placeholder-grayish"
        placeholder="Texto de la pregunta" required>

    <label class="block text-grayish font-semibold mt-4 mb-2">Tipo de pregunta</label>
    <select name="questions[${questionIndex}][type]"
        class="w-full px-4 py-3 bg-smokyBlack text-white border border-grayish rounded-xl">
        <option value="opcion_multiple">Opción múltiple</option>
        <option value="respuesta_abierta">Respuesta abierta</option>
    </select>

    <label class="block text-grayish font-semibold mt-4 mb-2">Opciones</label>
    <input type="text" name="questions[${questionIndex}][options][0][text]"
        class="w-full px-4 py-2 mb-2 bg-smokyBlack text-white border border-grayish rounded-xl"
        placeholder="Opción 1" required>
    <input type="text" name="questions[${questionIndex}][options][1][text]"
        class="w-full px-4 py-2 mb-2 bg-smokyBlack text-white border border-grayish rounded-xl"
        placeholder="Opción 2" required>
    <input type="text" name="questions[${questionIndex}][options][2][text]"
        class="w-full px-4 py-2 mb-2 bg-smokyBlack text-white border border-grayish rounded-xl"
        placeholder="Opción 3" required>
`;


        container.appendChild(newQuestion);
        questionIndex++;
    });
</script>
@endsection
