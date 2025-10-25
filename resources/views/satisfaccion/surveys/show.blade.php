{{-- resources/views/surveys/show.blade.php --}}
@extends('satisfaccion.layouts.app')

@section('title', 'Responder Encuesta')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-12 bg-smokyBlack text-white rounded-2xl shadow-xl">

    <h1 class="text-3xl font-extrabold text-deepSky text-center mb-4 tracking-wide">
        📝 {{ $survey->qualification }}
    </h1>
    <p class="text-grayish text-center mb-10">{{ $survey->description }}</p>

    <form action="{{ route('satisfaccion.student.surveys.submit', $survey->id) }}" method="POST" class="space-y-8">
        @csrf

        @foreach($survey->questions as $question)
            <div class="border border-grayish bg-darkPurple p-6 rounded-xl shadow-md">
                <p class="font-semibold text-gray-200 mb-4">
                    {{ $loop->iteration }}. {{ $question->question_text }}
                </p>
                @foreach($question->options as $option)
                    <div class="mb-2">
                        <label class="inline-flex items-center text-grayish">
                            {{-- ✅ Ahora se envía el texto de la opción en lugar del ID --}}
                            <input type="radio" 
                                   name="answers[{{ $question->id }}]" 
                                   value="{{ $option->option_text }}"
                                   class="form-radio text-deepSky focus:ring-deepSky" required>
                            <span class="ml-3">{{ $option->option_text }}</span>
                        </label>
                    </div>
                @endforeach
            </div>
        @endforeach

        <button type="submit"
                class="w-full bg-green-500 hover:bg-green-600 text-night font-bold py-3 rounded-xl transition duration-300">
            ✅ Enviar Respuestas
        </button>
    </form>
</div>
@endsection

