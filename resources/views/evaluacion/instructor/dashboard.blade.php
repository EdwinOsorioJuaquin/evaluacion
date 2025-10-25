@extends('evaluacion.layouts.app')

@section('title', 'Panel del Instructor')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-10">

  {{-- ===================== 🧭 ENCABEZADO ===================== --}}
  <section>
    <h1 class="text-3xl font-bold text-neutral-50">Panel del Instructor</h1>
    <p class="text-neutral-400 mt-2">
      Bienvenido, <span class="font-semibold text-brand-400">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</span>
    </p>
  </section>

  {{-- ===================== 📊 ESTADÍSTICAS ===================== --}}
  <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <x-ui.stat-card color="blue" label="Total Evaluaciones" :value="$stats['totalEvaluations']" />
    <x-ui.stat-card color="green" label="Estudiantes Únicos" :value="$stats['totalStudents']" />
    <x-ui.stat-card color="yellow" label="Rating Promedio" :value="number_format($stats['averageRating'], 1)" />
    <x-ui.stat-card color="purple" label="Evaluaciones Recientes" :value="$stats['recentEvaluations']->count()" />
  </section>

  {{-- ===================== 📚 EVALUACIONES POR SECCIÓN ===================== --}}
  <section class="rounded-2xl bg-ink-700/80 border border-ink-400/20 shadow-soft p-6">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-xl font-semibold text-neutral-100">Evaluaciones por Sección</h2>
      <a href="{{ route('evaluacion.instructor.results.index') }}" class="text-brand-400 hover:text-brand-300 text-sm font-medium">
        Ver todas →
      </a>
    </div>

    @if($stats['recentEvaluations']->count() > 0)
      @php
        $evaluationsBySession = $stats['recentEvaluations']->groupBy('evaluation_session_id');
      @endphp

      <div class="space-y-10">
        @foreach($evaluationsBySession as $sessionId => $sessionEvaluations)
          @php
            $session = $sessionEvaluations->first()->session;
            $puntuaciones = [1 => 5, 2 => 4, 3 => 3, 4 => 2, 5 => 1];

            // Calcular promedio general por sesión
            $totalPuntos = 0;
            $totalRespuestas = 0;
            foreach($sessionEvaluations as $eval) {
              if($eval->question->question_type === 'scale_1_5' && is_numeric($eval->text_response)) {
                $optionId = (int)$eval->text_response;
                $option = \App\Models\EvaluationQuestionOption::find($optionId);
                if($option && isset($puntuaciones[$option->option_value])) {
                  $totalPuntos += $puntuaciones[$option->option_value];
                  $totalRespuestas++;
                }
              }
            }
            $promedio = $totalRespuestas ? round($totalPuntos / $totalRespuestas, 1) : 0;
            $evaluationsByQuestion = $sessionEvaluations->groupBy('question_id');
          @endphp

          {{-- 🟦 Sección principal --}}
          <div class="rounded-xl border border-blue-500/30 bg-gray-800/60 overflow-hidden shadow-inner">
            {{-- Header de sesión --}}
            <div class="bg-gradient-to-r from-blue-600/30 to-purple-600/30 p-6 border-b border-blue-500/20">
              <h3 class="text-2xl font-semibold text-neutral-50 flex items-center gap-3 mb-3">
                <i class="fas fa-folder-open text-blue-400"></i>
                {{ $session->title ?? 'Sección sin título' }}
              </h3>
              <p class="text-neutral-300 text-sm mb-4">{{ $session->description ?? 'Sin descripción disponible' }}</p>
              <div class="flex flex-wrap gap-3 text-xs font-medium">
                <x-ui.badge color="blue" icon="calendar">
                  {{ $session->start_date->format('d/m/Y') }} - {{ $session->end_date->format('d/m/Y') }}
                </x-ui.badge>
                <x-ui.badge color="green" icon="chart-bar">Promedio: {{ $promedio }}/5</x-ui.badge>
                <x-ui.badge color="purple" icon="question-circle">{{ $evaluationsByQuestion->count() }} preguntas</x-ui.badge>
                <x-ui.badge color="yellow" icon="users">{{ $sessionEvaluations->count() }} respuestas</x-ui.badge>
              </div>
            </div>

            {{-- Preguntas dentro de la sesión --}}
            <div class="p-6 space-y-6">
              @foreach($evaluationsByQuestion as $questionId => $questionEvaluations)
                @php
                  $question = $questionEvaluations->first()->question;
                  $totalP = 0;
                  $totalR = 0;
                  foreach($questionEvaluations as $eval) {
                    if($eval->question->question_type === 'scale_1_5' && is_numeric($eval->text_response)) {
                      $optionId = (int)$eval->text_response;
                      $option = \App\Models\EvaluationQuestionOption::find($optionId);
                      if($option && isset($puntuaciones[$option->option_value])) {
                        $totalP += $puntuaciones[$option->option_value];
                        $totalR++;
                      }
                    }
                  }
                  $promPregunta = $totalR ? round($totalP / $totalR, 1) : 0;
                @endphp

                {{-- 🟣 Pregunta --}}
                <div class="bg-gray-900/50 border border-gray-700/50 rounded-xl p-5">
                  <div class="flex justify-between items-start mb-4">
                    <div>
                      <h4 class="text-lg font-semibold text-neutral-50 flex items-center gap-2 mb-2">
                        <i class="fas fa-question text-yellow-400"></i>
                        {{ $question->question_text }}
                      </h4>
                      <div class="flex flex-wrap gap-3 text-xs">
                        <x-ui.badge color="blue">{{ $question->question_type === 'scale_1_5' ? 'Escala 1-5' : 'Texto Libre' }}</x-ui.badge>
                        <x-ui.badge color="yellow" icon="chart-line">Promedio: {{ $promPregunta }}/5</x-ui.badge>
                        <x-ui.badge color="green" icon="reply">{{ $questionEvaluations->count() }} respuestas</x-ui.badge>
                      </div>
                    </div>
                  </div>

                  {{-- Respuestas individuales --}}
                  <div class="space-y-3 mt-4">
                    @foreach($questionEvaluations as $evaluation)
                      <div class="bg-gray-800/50 border border-gray-700/30 rounded-lg p-4 hover:border-blue-500/30 transition">
                        <div class="flex justify-between items-start mb-3">
                          <div class="flex items-center gap-3 text-sm">
                            <i class="fas fa-user text-green-400"></i>
                            <span class="text-neutral-100 font-medium">
                              {{ $evaluation->student->first_name ?? 'Estudiante' }} {{ $evaluation->student->last_name ?? '' }}
                            </span>
                            <span class="text-neutral-400">| {{ $evaluation->response_date->format('d/m/Y H:i') }}</span>
                          </div>
                        </div>
                        <p class="text-neutral-300 text-sm">
                          <strong class="text-neutral-100">Respuesta:</strong>
                          @php
                            $optionId = (int)$evaluation->text_response;
                            $option = \App\Models\EvaluationQuestionOption::find($optionId);
                          @endphp
                          @if($evaluation->question->question_type === 'scale_1_5' && $option)
                            <span class="font-semibold {{ ['1'=>'text-green-400','2'=>'text-blue-400','3'=>'text-yellow-400','4'=>'text-orange-400','5'=>'text-red-400'][$option->option_value] ?? 'text-neutral-300' }}">
                              {{ $option->option_text }}
                            </span>
                            <span class="text-blue-400 ml-2">(Puntuación: {{ $puntuaciones[$option->option_value] ?? 0 }}/5)</span>
                          @else
                            <span class="text-neutral-400">{{ $evaluation->text_response ?? 'Sin respuesta' }}</span>
                          @endif
                        </p>
                      </div>
                    @endforeach
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="text-center py-12 text-neutral-400">
        <i class="fas fa-clipboard-list text-6xl text-neutral-500 mb-4"></i>
        <p class="text-xl">No hay evaluaciones recientes</p>
        <p class="text-sm text-neutral-500 mt-1">Las evaluaciones aparecerán aquí cuando los estudiantes respondan</p>
      </div>
    @endif
  </section>

  {{-- ===================== ⚡ ACCIONES RÁPIDAS ===================== --}}
  <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <x-ui.quick-action
      color="blue"
      icon="chart-bar"
      title="Ver Todas las Evaluaciones"
      description="Revisa el historial completo de evaluaciones"
      :route="route('evaluacion.instructor.results.index')"
    />
    <x-ui.quick-action
      color="green"
      icon="user-tie"
      title="Mi Estadística Personal"
      description="Consulta tu desempeño detallado"
      :route="route('evaluacion.instructor.results.detail')"
    />
  </section>

</div>
@endsection
