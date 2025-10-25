{{-- resources/views/auditoria/findings/show.blade.php --}}
<x-app-layout>
  {{-- HEADER --}}
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-neutral-100 leading-tight">
        Detalles del Hallazgo
      </h2>

      <a href="{{ route('auditoria.audits.show', $audit) }}"
         class="inline-flex items-center gap-2 h-9 rounded-2xl px-3
                bg-ink-800/70 border border-ink-400/20 text-neutral-200
                hover:bg-ink-700 transition">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-width="1.8" d="M15 19l-7-7 7-7"/>
        </svg>
        Volver
      </a>
    </div>
  </x-slot>

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

      {{-- ==================== RESUMEN DEL HALLAZGO ==================== --}}
      <section class="rounded-2xl bg-ink-700/80 border border-ink-400/20 shadow-soft">
        <div class="p-6 space-y-5">

          {{-- Título y metadatos --}}
          <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
            <div>
              <h3 class="text-lg md:text-xl font-semibold text-neutral-50 leading-snug">
                {{ $finding->description }}
              </h3>

              <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                @php
                  $classMap = [
                    'Revisado'  => 'bg-green-500 text-white',
                    'Observado' => 'bg-yellow-500 text-black',
                    'No aplica' => 'bg-neutral-600 text-white',
                  ];
                  $sevMap = [
                    'high'   => ['text-danger-500 border-danger-500/30 bg-danger-500/10', 'Alta'],
                    'medium' => ['text-warning-500 border-warning-500/30 bg-warning-500/10', 'Media'],
                    'low'    => ['text-green-500 border-green-500/30 bg-green-500/10', 'Baja'],
                  ];
                  [$sevCls,$sevLbl] = $sevMap[strtolower($finding->severity ?? '')] ?? ['text-neutral-300 border-ink-400/30 bg-ink-800/60', ucfirst($finding->severity ?? '—')];
                @endphp

                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium {{ $classMap[$finding->classification] ?? 'bg-neutral-600 text-white' }}">
                  {{ ucfirst($finding->classification) }}
                </span>

                <span class="inline-flex items-center px-2 py-1 rounded-full border text-[11px] {{ $sevCls }}">
                  Severidad: {{ $sevLbl }}
                </span>

                <span class="inline-flex items-center px-2 py-1 rounded-full border text-[11px] text-neutral-300 border-ink-400/30 bg-ink-800/60">
                  Auditoría: <span class="ml-1 text-neutral-100 font-medium">{{ $audit->objective }}</span>
                </span>
              </div>
            </div>

            {{-- Acción correctiva rápida --}}
            @if ($finding->classification === 'Observado' && $audit->state === 'in_progress')
              <a href="{{ route('auditoria.actions.index', [$audit, $finding]) }}"
                 class="inline-flex items-center gap-2 rounded-2xl px-4 py-2
                        bg-warning-500 text-black font-semibold hover:bg-warning-600 transition">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-width="2" d="M12 5v14M5 12h14"/>
                </svg>
                Nueva acción correctiva
              </a>
            @endif
          </div>

          {{-- Evidencia --}}
          <div>
            <h4 class="text-sm font-semibold text-neutral-200 mb-2">Evidencia</h4>

            @php
              $evidence = $finding->evidence;
              $isFile = $evidence && strpos($evidence, '/') !== false;
              $url = $isFile ? Storage::url($evidence) : null;
              $ext = $isFile ? strtolower(pathinfo($evidence, PATHINFO_EXTENSION)) : null;
            @endphp

            @if ($evidence)
              @if ($isFile && in_array($ext, ['jpg','jpeg','png','gif','webp']))
                <div class="rounded-xl overflow-hidden border border-ink-400/20 bg-ink-800/70">
                  <img src="{{ $url }}" alt="Evidencia" class="w-full max-h-[480px] object-contain">
                </div>

              @elseif ($isFile && $ext === 'pdf')
                <div class="rounded-xl overflow-hidden border border-ink-400/20 bg-ink-800/70">
                  <embed src="{{ $url }}" type="application/pdf" class="w-full h-[640px]">
                </div>

              @elseif ($isFile)
                <div class="rounded-xl border border-ink-400/20 bg-ink-800/60 p-4 text-sm text-neutral-300">
                  Archivo adjunto:
                  <a href="{{ $url }}" target="_blank" class="text-brand-400 hover:text-brand-300 font-medium">
                    {{ basename($evidence) }}
                  </a>
                </div>
              @else
                <div class="rounded-xl border border-ink-400/20 bg-ink-800/60 p-4 text-sm text-neutral-200">
                  {!! nl2br(e($evidence)) !!}
                </div>
              @endif

              {{-- Descargar --}}
              @if ($isFile)
                <div class="mt-3">
                  <a href="{{ $url }}" download
                     class="inline-flex items-center gap-2 rounded-2xl px-3 py-2
                            bg-ink-800/70 border border-ink-400/20 text-neutral-200 hover:bg-ink-700 transition">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path stroke-linecap="round" stroke-width="1.8" d="M12 16V4m0 12l-3-3m3 3l3-3M4 20h16"/>
                    </svg>
                    Descargar evidencia
                  </a>
                </div>
              @endif
            @else
              <p class="text-neutral-400 text-sm">No se ha registrado evidencia.</p>
            @endif
          </div>
        </div>
      </section>

      {{-- ==================== ACCIONES CORRECTIVAS ==================== --}}
      <section class="rounded-2xl bg-ink-700/80 border border-ink-400/20 shadow-soft">
        <div class="p-6">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-lg font-semibold text-neutral-100">Acciones Correctivas</h3>

            @if ($finding->classification === 'Observado' && $audit->state === 'in_progress')
              <a href="{{ route('auditoria.actions.store', [$audit, $finding]) }}"
                 class="inline-flex items-center gap-2 h-9 rounded-2xl px-3
                        bg-brand-500 text-black font-semibold hover:bg-brand-400 transition">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-width="2" d="M12 5v14M5 12h14"/>
                </svg>
                Nueva acción
              </a>
            @endif
          </div>

          @php
            $statusMap = [
              'pending'     => ['bg-yellow-500 text-black', 'Pendiente'],
              'in_progress' => ['bg-blue-500 text-white',   'En progreso'],
              'completed'   => ['bg-green-500 text-white',  'Completada'],
              'cancelled'   => ['bg-neutral-600 text-white','Cancelada'],
            ];
          @endphp

          <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-sm text-neutral-200">
              <thead class="bg-ink-800/80 text-neutral-300">
                <tr>
                  <th class="text-left font-semibold px-4 py-3">Descripción</th>
                  <th class="text-left font-semibold px-4 py-3">Estado</th>
                  <th class="text-right font-semibold px-4 py-3">Acciones</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($finding->correctiveActions as $correctiveAction)
                  @php
                    [$stCls,$stLbl] = $statusMap[$correctiveAction->status] ?? ['bg-neutral-600 text-white','—'];
                  @endphp
                  <tr class="border-t border-ink-400/10 hover:bg-ink-800/50 transition">
                    <td class="px-4 py-3">
                      <div class="font-medium text-neutral-100">
                        {{ \Illuminate\Support\Str::limit($correctiveAction->description, 140) }}
                      </div>
                    </td>
                    <td class="px-4 py-3">
                      <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs {{ $stCls }}">
                        {{ $stLbl }}
                      </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                      <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('auditoria.actions.show', [$audit, $finding, $correctiveAction]) }}"
                           class="inline-flex h-9 w-9 items-center justify-center rounded-xl
                                  bg-ink-800/70 border border-ink-400/20 hover:bg-ink-700"
                           title="Ver detalle">
                          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-width="1.8" d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z"/>
                            <circle cx="12" cy="12" r="3" stroke-width="1.8"/>
                          </svg>
                        </a>
                        <a href="{{ route('auditoria.actions.edit', [$audit, $finding, $correctiveAction]) }}"
                           class="inline-flex h-9 w-9 items-center justify-center rounded-xl
                                  bg-ink-800/70 border border-ink-400/20 hover:bg-ink-700"
                           title="Editar">
                          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z"/>
                          </svg>
                        </a>
                        <form method="POST" action="{{ route('auditoria.actions.delete', [$audit, $finding, $correctiveAction]) }}">
                          @csrf @method('DELETE')
                          <button type="submit"
                                  class="inline-flex h-9 w-9 items-center justify-center rounded-xl
                                         bg-ink-800/70 border border-ink-400/20 hover:bg-ink-700"
                                  title="Eliminar">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4a1 1 0 011 1v1H9V4a1 1 0 011-1zM4 7h16"/>
                            </svg>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="3" class="px-4 py-10 text-center text-neutral-400">
                      No se han registrado acciones correctivas.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </section>

    </div>
  </div>
</x-app-layout>
