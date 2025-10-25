{{-- resources/views/corrective-actions/show.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-semibold text-xl text-neutral-100 leading-tight">
          Detalles de la acción correctiva
        </h2>
        <p class="text-sm text-neutral-400 mt-1">
          Auditoría: <span class="text-neutral-100">{{ $audit->objective }}</span>
          • Hallazgo: <span class="text-neutral-100">{{ \Illuminate\Support\Str::limit($finding->description, 80) }}</span>
        </p>
      </div>

      <a href="{{ route('findings.show', [$audit->id, $finding->id]) }}"
         class="inline-flex items-center gap-2 h-9 rounded-2xl px-3
                bg-ink-800/70 border border-ink-400/20 text-neutral-200
                hover:bg-ink-700">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-width="1.8" d="M15 19l-7-7 7-7"/>
        </svg>
        Volver
      </a>
    </div>
  </x-slot>

  <div class="py-6">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
      <div class="rounded-2xl bg-ink-700/80 border border-ink-400/20 shadow-soft">
        <div class="p-6 md:p-8 space-y-8">

          {{-- Flash + errores globales --}}
          @if (session('success'))
            <div class="rounded-xl border border-green-500/30 bg-green-500/10 text-green-300 px-4 py-3">
              {{ session('success') }}
            </div>
          @endif
          @if ($errors->any())
            <div class="rounded-xl border border-danger-500/30 bg-danger-500/10 text-danger-300 px-4 py-3">
              <div class="font-semibold mb-1">Revisa los campos:</div>
              <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
              </ul>
            </div>
          @endif

          {{-- Encabezado acción --}}
          <div class="flex flex-col gap-3">
            <h3 class="text-xl font-semibold text-neutral-50">
              {{ $correctiveAction->description }}
            </h3>

            @php
              $status = $correctiveAction->status;
              $pill = match ($status) {
                'pending'     => 'bg-yellow-500 text-black',
                'in_progress' => 'bg-brand-500 text-black',
                'completed'   => 'bg-green-500 text-white',
                default       => 'bg-neutral-600 text-white'
              };
            @endphp

            <div class="flex flex-wrap items-center gap-2">
              <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full {{ $pill }}">
                {{ ucfirst(str_replace('_',' ', $correctiveAction->status)) }}
              </span>
              <span class="text-xs text-neutral-400">
                Última actualización: {{ optional($correctiveAction->updated_at)->diffForHumans() }}
              </span>
            </div>
          </div>

          {{-- Meta: Fechas y estado visual --}}
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="rounded-xl bg-ink-800/60 border border-ink-400/20 p-4">
              <div class="text-xs text-neutral-400">Fecha de compromiso</div>
              <div class="text-neutral-100 font-medium">
                {{ \Carbon\Carbon::parse($correctiveAction->engagement_date)->format('d/m/Y') }}
              </div>
            </div>

            <div class="rounded-xl bg-ink-800/60 border border-ink-400/20 p-4">
              <div class="text-xs text-neutral-400">Fecha límite</div>
              <div class="text-neutral-100 font-medium">
                {{ \Carbon\Carbon::parse($correctiveAction->due_date)->format('d/m/Y') }}
              </div>
            </div>

            <div class="rounded-xl bg-ink-800/60 border border-ink-400/20 p-4">
              <div class="text-xs text-neutral-400">Fecha de ejecución</div>
              <div class="text-neutral-100 font-medium">
                {{ $correctiveAction->completion_date
                    ? \Carbon\Carbon::parse($correctiveAction->completion_date)->format('d/m/Y')
                    : '—' }}
              </div>
            </div>
          </div>

          {{-- Barra de progreso por estado --}}
          @php
            $progress = match ($status) {
              'pending' => 20,
              'in_progress' => 60,
              'completed' => 100,
              default => 10,
            };
          @endphp
          <div>
            <div class="flex items-center justify-between mb-2">
              <span class="text-sm text-neutral-300">Progreso</span>
              <span class="text-sm text-neutral-400">{{ $progress }}%</span>
            </div>
            <div class="h-2 rounded-full bg-ink-800/60 border border-ink-400/20 overflow-hidden">
              <div class="h-full rounded-full bg-brand-500" style="width: {{ $progress }}%"></div>
            </div>
          </div>

          {{-- Acciones por estado --}}
          @if ($correctiveAction->status === 'pending')
            <div class="pt-2">
              <form
                method="POST"
                action="{{ route('correctiveActions.start', ['audit' => $audit->id, 'finding' => $finding->id, 'correctiveAction' => $correctiveAction->id]) }}"
                class="inline-flex">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5
                               bg-brand-500 text-black font-semibold hover:bg-brand-400
                               focus:outline-none focus:ring-2 focus:ring-brand-300">
                  <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-width="1.8" d="M5 12h14M12 5l7 7-7 7"/>
                  </svg>
                  Iniciar acción
                </button>
              </form>
            </div>
          @elseif ($correctiveAction->status === 'in_progress')
            <div class="pt-2">
              <form
                method="POST"
                action="{{ route('correctiveActions.updateExecutionDate', ['audit' => $audit->id, 'finding' => $finding->id, 'correctiveAction' => $correctiveAction->id]) }}"
                class="space-y-4">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label for="completion_date" class="block text-sm font-medium text-neutral-200 mb-1">
                      Fecha real de ejecución <span class="text-danger-500">*</span>
                    </label>
                    <input
                      type="date"
                      id="completion_date"
                      name="completion_date"
                      value="{{ old('completion_date', $correctiveAction->completion_date ? $correctiveAction->completion_date->format('Y-m-d') : now()->toDateString()) }}"
                      min="{{ \Carbon\Carbon::parse($correctiveAction->engagement_date)->toDateString() }}"
                      class="w-full h-10 rounded-xl bg-ink-800/70 border border-ink-400/30 text-neutral-100
                             focus:outline-none focus:ring-2 focus:ring-brand-300 focus:border-brand-400 px-3"
                      required>
                    @error('completion_date')
                      <p class="mt-2 text-xs text-danger-400">{{ $message }}</p>
                    @enderror
                  </div>
                </div>

                <div class="flex flex-wrap gap-3">
                  <button type="submit"
                          class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5
                                 bg-green-500 text-white font-semibold hover:bg-green-400
                                 focus:outline-none focus:ring-2 focus:ring-green-300">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path stroke-linecap="round" stroke-width="1.8" d="M5 13l4 4L19 7"/>
                    </svg>
                    Completar acción
                  </button>

                  <a href="{{ route('findings.show', [$audit->id, $finding->id]) }}"
                     class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5
                            bg-ink-800/70 border border-ink-400/20 text-neutral-200 hover:bg-ink-700">
                    Cancelar
                  </a>
                </div>
              </form>
            </div>
          @elseif ($correctiveAction->status === 'completed')
            <div class="rounded-xl bg-ink-800/40 border border-ink-400/20 p-4">
              <div class="text-sm text-neutral-300">
                Acción completada el
                <span class="text-neutral-100 font-medium">
                  {{ \Carbon\Carbon::parse($correctiveAction->completion_date)->format('d/m/Y') }}
                </span>
              </div>
            </div>
          @endif

          {{-- Info adicional / contexto mini --}}
          <div class="rounded-xl bg-ink-800/40 border border-ink-400/20 p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
              <div>
                <div class="text-neutral-400">Área</div>
                <div class="text-neutral-100 font-medium">{{ $audit->area }}</div>
              </div>
              <div>
                <div class="text-neutral-400">Hallazgo</div>
                <div class="text-neutral-100">
                  {{ \Illuminate\Support\Str::limit($finding->description, 120) }}
                </div>
              </div>
            </div>
          </div>

        </div> {{-- /card padding --}}
      </div>
    </div>
  </div>
</x-app-layout>
