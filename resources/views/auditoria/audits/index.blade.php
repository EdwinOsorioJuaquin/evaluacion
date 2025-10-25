<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-neutral-100 leading-tight">
        {{ __('Auditorías Registradas') }}
      </h2>

      @if(auth()->user()?->hasRole('admin'))
        <x-ui.button as="a" href="{{ route('auditoria.dashboard.create-audit') }}" class="gap-2">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-width="1.8" d="M12 5v14M5 12h14"/>
          </svg>
          Crear
        </x-ui.button>
      @endif
    </div>
  </x-slot>

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="rounded-2xl bg-ink-700/80 border border-ink-400/20 shadow-soft">

        {{-- Filtros / acciones --}}
        <form method="GET" action="{{ route('auditoria.audits.index') }}" class="p-4 border-b border-ink-400/20">
          <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">

            {{-- Izquierda: filtro por estado --}}
            <div class="flex items-center gap-2">
              <label for="state" class="text-sm text-neutral-300">Estado</label>
              <select id="state" name="state"
                      class="h-9 rounded-xl bg-ink-800/80 border border-ink-400/30 text-neutral-100
                             focus:ring-brand-300 focus:border-brand-300">
                @php
                  $states = [
                    ''            => 'Todos',
                    'planned'     => 'Planificada',
                    'in_progress' => 'En progreso',
                    'completed'   => 'Completada',
                    'cancelled'   => 'Cancelada',
                    // Ojo: 'deleted' solo si quieres listarlas explícitamente
                  ];
                @endphp
                @foreach($states as $value => $label)
                  <option value="{{ $value }}" @selected(request('state')===$value)>{{ $label }}</option>
                @endforeach
              </select>

              {{-- Ver eliminadas (opcional) --}}
              <label class="inline-flex items-center gap-2 text-sm text-neutral-300 ml-2">
                <input type="checkbox" name="show" value="deleted"
                       class="rounded bg-ink-800 border-ink-400/40 text-brand-500 focus:ring-brand-300"
                       @checked(request('show')==='deleted')>
                <span>Ver eliminadas</span>
              </label>
            </div>

            {{-- Derecha: buscador + reset --}}
            <div class="flex items-center gap-2">
              <div class="relative">
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Buscar por objetivo o área…"
                       class="w-64 h-9 rounded-xl bg-ink-800/80 border border-ink-400/30 text-neutral-100
                              placeholder-neutral-400 pl-9 pr-3 focus:ring-brand-300 focus:border-brand-300">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-neutral-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-width="1.8" d="M21 21l-4.35-4.35M10 18a8 8 0 110-16 8 8 0 010 16z"/>
                </svg>
              </div>

              <x-ui.button type="submit" variant="secondary">Filtrar</x-ui.button>
              @if(request()->hasAny(['q','state','show']))
                <a href="{{ route('auditoria.audits.index') }}"
                   class="inline-flex items-center h-9 rounded-xl px-3 text-sm bg-ink-800/70 border border-ink-400/30 hover:bg-ink-700">
                  Limpiar
                </a>
              @endif
            </div>
          </div>
        </form>

        {{-- Tabla --}}
        @php
          // Mapeo de clases y etiquetas para el badge de estado
          $badgeMap = [
            'planned'     => ['bg-yellow-500 text-black',                 'Planificada'],
            'in_progress' => ['bg-brand-500 text-black',                  'En progreso'],
            'completed'   => ['bg-green-500 text-white',                  'Completada'],
            'cancelled'   => ['bg-danger-500 text-white',                 'Cancelada'],
            'deleted'     => ['bg-neutral-800 text-neutral-300 border border-ink-400/30', 'Eliminada'],
          ];
        @endphp

        <div class="overflow-x-auto">
          <table class="min-w-full text-sm text-neutral-200">
            <thead class="bg-ink-800/80 text-neutral-300">
              <tr>
                <th class="text-left font-semibold px-4 py-3">Objetivo</th>
                <th class="text-left font-semibold px-4 py-3">Área</th>
                <th class="text-left font-semibold px-4 py-3">Inicio</th>
                <th class="text-left font-semibold px-4 py-3">Fin</th>
                <th class="text-left font-semibold px-4 py-3">Estado</th>
                <th class="text-right font-semibold px-4 py-3">Acciones</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($audits as $audit)
                <tr class="border-t border-ink-400/10 hover:bg-ink-800/50">
                  <td class="px-4 py-3">
                    <div class="font-medium text-neutral-100">{{ $audit->objective }}</div>
                    <div class="text-xs text-neutral-400">
                      ID: {{ $audit->id }}
                      @if($audit->summary_results)
                        · {{ \Illuminate\Support\Str::limit(strip_tags($audit->summary_results), 60) }}
                      @endif
                    </div>
                  </td>

                  <td class="px-4 py-3">
                    {{ $audit->area }}
                  </td>

                  <td class="px-4 py-3">
                    {{ optional($audit->start_date)->format('d/m/Y') ?? '—' }}
                  </td>

                  <td class="px-4 py-3">
                    {{ optional($audit->end_date)->format('d/m/Y') ?? '—' }}
                  </td>

                  <td class="px-4 py-3">
                    @php
                      [$cls,$lbl] = $badgeMap[$audit->state] ?? ['bg-neutral-600 text-white','Desconocido'];
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs {{ $cls }}">
                      {{-- icono simple por estado (opcional) --}}
                      @switch($audit->state)
                        @case('planned')
                          <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-width="1.8" d="M8 7h8M8 11h8M8 15h5M5 7v10a2 2 0 002 2h10"/>
                          </svg>
                          @break
                        @case('in_progress')
                          <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-width="1.8" d="M12 6v6l4 2"/>
                          </svg>
                          @break
                        @case('completed')
                          <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                          </svg>
                          @break
                        @case('cancelled')
                          <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                          </svg>
                          @break
                        @case('deleted')
                          <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-width="1.8" d="M3 6h18M8 6v12m8-12v12M5 6l1 14a2 2 0 002 2h8a2 2 0 002-2l1-14"/>
                          </svg>
                          @break
                      @endswitch
                      {{ $lbl }}
                    </span>
                  </td>

                  <td class="px-4 py-3">
                    <div class="flex items-center justify-end gap-2">
                      {{-- Iniciar / Completar (admin; solo si no deleted) --}}
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
                        @elseif($audit->state === 'in_progress')
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

                      {{-- Ver --}}
                      <a href="{{ route('auditoria.audits.show', $audit) }}"
                         class="inline-flex h-9 w-9 items-center justify-center rounded-xl
                                bg-ink-800/70 border border-ink-400/20 hover:bg-ink-700"
                         title="Ver">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                          <path stroke-linecap="round" stroke-width="1.8" d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z"/>
                          <circle cx="12" cy="12" r="3" stroke-width="1.8"/>
                        </svg>
                      </a>

                      {{-- Editar (si no está eliminada) --}}
                      @if($audit->state !== 'deleted' && auth()->user()?->hasRole('admin'))
                        <a href="{{ route('auditoria.audits.edit', $audit) }}"
                           class="inline-flex h-9 w-9 items-center justify-center rounded-xl
                                  bg-ink-800/70 border border-ink-400/20 hover:bg-ink-700"
                           title="Editar">
                          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-width="1.8" d="M4 21h4l11-11a2.828 2.828 0 00-4-4L4 17v4z"/>
                          </svg>
                        </a>
                      @endif

                    

                      {{-- Marcar eliminado (POST) --}}
                      @if(auth()->user()?->hasRole('admin') && $audit->state !== 'deleted')
                        <form method="POST" action="{{ route('auditoria.audits.destroy', $audit) }}"
                              onsubmit="return confirm('¿Marcar esta auditoría como eliminada?');">
                          @csrf
                          <button type="submit"
                                  class="inline-flex h-9 w-9 items-center justify-center rounded-xl
                                         bg-danger-500/10 border border-danger-500/30 text-danger-500
                                         hover:bg-danger-500/20"
                                  title="Eliminar (marcar)">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                              <path stroke-linecap="round" stroke-width="1.8" d="M3 6h18M8 6v12m8-12v12M5 6l1 14a2 2 0 002 2h8a2 2 0 002-2l1-14"/>
                            </svg>
                          </button>
                        </form>
                      @endif

                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="px-4 py-10 text-center text-neutral-400">
                    No se encontraron auditorías con los filtros actuales.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- Paginación --}}
        <div class="px-4 py-4 border-t border-ink-400/20">
          {{ $audits->appends(request()->query())->links() }}
        </div>

      </div>
    </div>
  </div>
</x-app-layout>
