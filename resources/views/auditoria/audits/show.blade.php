{{-- resources/views/auditoria/audits/show.blade.php --}}
<x-app-layout>
  {{-- === HEADER === --}}
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <a href="{{ route('auditoria.audits.index') }}"
           class="inline-flex h-9 items-center gap-2 rounded-xl px-3
                  bg-ink-800/70 border border-ink-400/20 text-neutral-200
                  hover:bg-ink-700">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-width="1.8" d="M15 19l-7-7 7-7"/>
          </svg>
          Volver
        </a>
        <h2 class="font-semibold text-xl text-neutral-100 leading-tight">
          Detalles de la Auditoría
        </h2>
      </div>

      {{-- === ESTADO + ACCIONES === --}}
      <div class="flex items-center gap-2">
        @php
          $stateMap = [
            'planned'     => ['bg-yellow-500 text-black', 'Planificada'],
            'in_progress' => ['bg-brand-500 text-black',  'En progreso'],
            'completed'   => ['bg-green-500 text-white',  'Completada'],
            'cancelled'   => ['bg-danger-500 text-white', 'Cancelada'],
            'deleted'     => ['bg-neutral-800 text-neutral-300 border border-ink-400/30', 'Eliminada'],
          ];
          [$cls,$label] = $stateMap[$audit->state] ?? ['bg-neutral-600 text-white','Desconocido'];
        @endphp

        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs {{ $cls }}">
          {{ $label }}
        </span>

        @if(auth()->user() && $audit->state !== 'deleted')
          @if($audit->state === 'planned')
            <form method="POST" action="{{ route('auditoria.audits.start', $audit) }}">
              @csrf
              <button type="submit"
                class="inline-flex items-center gap-1.5 h-9 rounded-2xl px-3
                       bg-warning-500 text-black hover:bg-warning-700">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-width="2" d="M8 5v14l11-7z"/>
                </svg>
                Iniciar
              </button>
            </form>
          @elseif($audit->state === 'in_progress' && $findings->every(fn($f) => $f->classification === 'Revisado'))
            <form method="POST" action="{{ route('auditoria.audits.complete', $audit) }}">
              @csrf
              <button type="submit"
                class="inline-flex items-center gap-1.5 h-9 rounded-2xl px-3
                       bg-success-500 text-white hover:bg-success-700">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Completar
              </button>
            </form>
          @endif
        @endif
      </div>
    </div>
  </x-slot>

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

      {{-- === RESUMEN === --}}
      <section class="rounded-2xl bg-ink-700/80 border border-ink-400/20 shadow-soft">
        <div class="p-6">
          <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
            <div>
              <h3 class="text-2xl font-semibold text-neutral-100">{{ $audit->objective }}</h3>
              <p class="text-sm text-neutral-300 mt-1">
                <span class="text-neutral-400">Área:</span> {{ $audit->area }}
              </p>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-sm">
              <div class="rounded-xl bg-ink-800/70 border border-ink-400/20 p-3">
                <div class="text-neutral-400">Inicio</div>
                <div class="font-medium text-neutral-100">
                  {{ optional($audit->start_date)->format('d/m/Y') ?? '—' }}
                </div>
              </div>
              <div class="rounded-xl bg-ink-800/70 border border-ink-400/20 p-3">
                <div class="text-neutral-400">Fin</div>
                <div class="font-medium text-neutral-100">
                  {{ optional($audit->end_date)->format('d/m/Y') ?? '—' }}
                </div>
              </div>
              <div class="rounded-xl bg-ink-800/70 border border-ink-400/20 p-3">
                <div class="text-neutral-400">Tipo</div>
                <div class="font-medium text-neutral-100">
                  {{ $audit->type ? ucfirst($audit->type) : '—' }}
                </div>
              </div>
            </div>
          </div>

          @if($audit->summary_results)
            <div class="mt-4">
              <div class="text-neutral-300 text-sm">Resumen</div>
              <div class="mt-1 text-neutral-100 leading-relaxed">
                {!! nl2br(e($audit->summary_results)) !!}
              </div>
            </div>
          @endif

          @if ($audit->state === 'in_progress')
            <div class="mt-5">
              <a href="{{ route('auditoria.findings.index', $audit) }}"
                 class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5
                        bg-brand-500 text-black font-semibold hover:bg-brand-400">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-width="1.8" d="M12 5v14M5 12h14"/>
                </svg>
                Añadir Hallazgo
              </a>
            </div>
          @endif
        </div>
      </section>

      {{-- === HALLAZGOS === --}}
      <section class="rounded-2xl bg-ink-700/80 border border-ink-400/20 shadow-soft">
        <div class="p-6">
          <h4 class="text-lg font-semibold text-neutral-100 mb-3">Hallazgos Registrados</h4>

          @php
            $sevMap = [
              'high'   => ['text-danger-500 border-danger-500/30 bg-danger-500/10',  'Alta'],
              'medium' => ['text-warning-500 border-warning-500/30 bg-warning-500/10','Media'],
              'low'    => ['text-green-500 border-green-500/30 bg-green-500/10',     'Baja'],
            ];
            $classMap = [
              'Revisado'  => ['bg-green-500 text-white','Revisado'],
              'Observado' => ['bg-yellow-500 text-black','Observado'],
              'No aplica' => ['bg-gray-500 text-white','No aplica'],
            ];
          @endphp

          <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-sm text-neutral-200">
              <thead class="bg-ink-800/80 text-neutral-300">
                <tr>
                  <th class="text-left font-semibold px-4 py-3">Descripción</th>
                  <th class="text-left font-semibold px-4 py-3">Severidad</th>
                  <th class="text-left font-semibold px-4 py-3">Clasificación</th>
                  <th class="text-right font-semibold px-4 py-3">Acciones</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($findings as $finding)
                  <tr class="border-t border-ink-400/10 hover:bg-ink-800/50">
                    <td class="px-4 py-3">
                      <div class="font-medium text-neutral-100">
                        {{ \Illuminate\Support\Str::limit($finding->description, 120) }}
                      </div>
                    </td>
                    <td class="px-4 py-3">
                      @php
                        [$sevCls,$sevLbl] = $sevMap[strtolower($finding->severity ?? '')] ?? ['text-neutral-300 border-ink-400/30 bg-ink-800/60', ucfirst($finding->severity ?? '—')];
                      @endphp
                      <span class="inline-flex items-center px-2 py-1 rounded-full border text-xs {{ $sevCls }}">
                        {{ $sevLbl }}
                      </span>
                    </td>
                    <td class="px-4 py-3">
                      @php
                        [$cCls,$cLbl] = $classMap[$finding->classification] ?? ['bg-neutral-600 text-white','—'];
                      @endphp
                      <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs {{ $cCls }}">
                        {{ $cLbl }}
                      </span>
                    </td>
                    <td class="px-4 py-3">
                      <div class="flex items-center justify-end gap-2">
                        {{-- Acción Correctiva --}}
                        @if($finding->classification === 'Observado' && $audit->state === 'in_progress')
                          <a href="{{ route('auditoria.actions.index', [$audit, $finding]) }}"
                             class="inline-flex items-center gap-1.5 h-9 rounded-2xl px-3
                                    bg-warning-500 text-black hover:bg-warning-700"
                             title="Agregar acción correctiva">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                              <path stroke-linecap="round" stroke-width="2" d="M12 5v14M5 12h14"/>
                            </svg>
                            Acción
                          </a>
                        @endif

                        <a href="{{ route('auditoria.findings.show', [$audit, $finding]) }}"
                           class="inline-flex h-9 w-9 items-center justify-center rounded-xl
                                  bg-ink-800/70 border border-ink-400/20 hover:bg-ink-700"
                           title="Ver detalle">
                          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-width="1.8" d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z"/>
                            <circle cx="12" cy="12" r="3" stroke-width="1.8"/>
                          </svg>
                        </a>

                        @if($finding->state !== 'deleted' && auth()->user()?->hasRole('admin'))
                          <a href="{{ route('auditoria.findings.edit', [$audit, $finding]) }}"
                             class="inline-flex h-9 w-9 items-center justify-center rounded-xl
                                    bg-ink-800/70 border border-ink-400/20 hover:bg-ink-700"
                             title="Editar">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                              <path stroke-linecap="round" stroke-width="1.8" d="M4 21h4l11-11a2.828 2.828 0 00-4-4L4 17v4z"/>
                            </svg>
                          </a>
                        @endif

                        @if(auth()->user()?->hasRole('admin'))
                          <form action="{{ route('auditoria.findings.destroy', [$audit, $finding]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex h-9 w-9 items-center justify-center
                                   rounded-xl bg-ink-800/70 border border-ink-400/20 hover:bg-ink-700"
                                    title="Eliminar">
                              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/>
                              </svg>
                            </button>
                          </form>
                        @endif
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="px-4 py-10 text-center text-neutral-400">
                      No se han registrado hallazgos aún.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </section>

      {{-- === RECOMENDACIONES === --}}
      <section class="rounded-2xl bg-ink-700/80 border border-ink-400/20 shadow-soft">
        <div class="p-6">
          <div class="flex items-center justify-between">
            <h4 class="text-lg font-semibold text-neutral-100">Recomendaciones Finales</h4>

            @if ($audit->auditReports()->count() == 0)
              <a href="{{ route('auditoria.reports.create', $audit) }}"
                 class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5
                        bg-brand-500 text-black font-semibold hover:bg-brand-400">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-width="1.8" d="M12 5v14M5 12h14"/>
                </svg>
                Agregar
              </a>
            @endif
          </div>

          @if ($audit->auditReports()->count() > 0)
            @php $report = $audit->auditReports->last(); @endphp
            <div class="mt-4 rounded-xl bg-ink-800/70 border border-ink-400/20 p-4">
              <div class="text-neutral-100 whitespace-pre-line leading-relaxed">
                {{ $report->resume }}
              </div>
            </div>

            <div class="mt-4">
              <a href="{{ route('auditoria.reports.pdf', $audit) }}" target="_blank"
                 class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5
                        bg-brand-500 border border-ink-400/20 text-black hover:bg-brand-400 font-semibold">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-width="1.8" d="M12 3v12m0 0l-4-4m4 4l4-4M4 17h16v2H4z"/>
                </svg>
                Generar Reporte
              </a>
            </div>
          @else
            <p class="mt-3 text-neutral-400 text-sm">
              Aún no hay recomendaciones finales registradas.
            </p>
          @endif
        </div>
      </section>

    </div>
  </div>
</x-app-layout>
