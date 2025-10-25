{{-- resources/views/dashboard/index.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-neutral-100 tracking-tight">
        {{ __('Dashboard') }}
      </h2>

      <div class="flex items-center gap-2">
        <a href="{{ route('auditoria.audits.index') }}"
           class="inline-flex items-center gap-2 rounded-xl px-3 py-2
                  bg-ink-700/70 hover:bg-ink-600 text-neutral-100 border border-ink-400/20 transition">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-width="1.6" d="M15 12H9M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9z"/>
          </svg>
          <span>Ver todas</span>
        </a>

        @if(auth()->user()?->hasRole('admin'))
          <a href="{{ route('auditoria.dashboard.create-audit') }}"
             class="inline-flex items-center gap-2 rounded-xl px-3 py-2
                    bg-brand-500 hover:bg-brand-400 text-black shadow-soft transition">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-width="1.8" d="M12 5v14M5 12h14"/>
            </svg>
            <span>Crear auditoría</span>
          </a>
        @endif
      </div>
    </div>
  </x-slot>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

      {{-- ===== Toolbar (filtros rápidos) ===== --}}
      <section class="rounded-2xl bg-ink-700/60 border border-ink-400/20 p-4">
        <form method="GET" action="{{ route('auditoria.dashboard.index') }}" class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
          <div class="flex items-center gap-2">
            <span class="text-sm text-neutral-300">Rango:</span>
            <select name="range"
                    class="rounded-lg bg-ink-600 border border-ink-400/30 text-neutral-100 text-sm px-3 py-2 focus:ring-2 focus:ring-brand-300">
              @php $range = request('range','12m'); @endphp
              <option value="3m"  @selected($range==='3m')>Últ. 3 meses</option>
              <option value="6m"  @selected($range==='6m')>Últ. 6 meses</option>
              <option value="12m" @selected($range==='12m')>Últ. 12 meses</option>
              <option value="all" @selected($range==='all')>Todo</option>
            </select>
          </div>
          <button class="inline-flex items-center gap-2 rounded-xl px-3 py-2 bg-ink-700/70 hover:bg-ink-600 text-neutral-100 border border-ink-400/20">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="1.8" d="M4 4h7v7H4zm9 9h7v7h-7zM13 4h7M4 13h7"/></svg>
            Aplicar
          </button>
        </form>
      </section>

      {{-- ===== KPIs primarios ===== --}}
      <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
          $kpi = fn($title,$val,$icon,$bg) => ['title'=>$title,'val'=>$val,'icon'=>$icon,'bg'=>$bg];
          $cards = [
            $kpi('Totales',        $totalAudits,     'M4 6h16M4 12h16M4 18h16', 'bg-ink-700/80'),
            $kpi('Planificadas',   $plannedAudits,   'M12 6v12M6 12h12',       'bg-yellow-500/10'),
            $kpi('En progreso',    $activeAudits,    'M12 8v5l3 3',            'bg-blue-500/10'),
            $kpi('Completadas',    $completedAudits, 'M20 6L9 17l-5-5',        'bg-green-500/10'),
          ];
        @endphp

        @foreach($cards as $c)
          <div class="rounded-2xl {{ $c['bg'] }} border border-ink-400/20 p-5 shadow-soft">
            <div class="flex items-center gap-4">
              <div class="shrink-0 rounded-xl bg-ink-800/50 p-3">
                <svg class="h-6 w-6 text-neutral-300" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-width="1.8" d="{{ $c['icon'] }}"/>
                </svg>
              </div>
              <div>
                <p class="text-sm text-neutral-400">{{ $c['title'] }}</p>
                <p class="text-2xl font-semibold text-neutral-50">{{ $c['val'] }}</p>
              </div>
            </div>
          </div>
        @endforeach
      </section>

      {{-- ===== KPIs secundarios ===== --}}
      <section class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-ink-700/70 border border-ink-400/20 p-4">
          <p class="text-neutral-400 text-sm">Hallazgos (total)</p>
          <p class="text-2xl text-neutral-50 font-semibold">{{ $totalFindings }}</p>
        </div>
        <div class="rounded-2xl bg-ink-700/70 border border-ink-400/20 p-4">
          <p class="text-neutral-400 text-sm">Hallazgos abiertos</p>
          <p class="text-2xl text-yellow-300 font-semibold">{{ $openFindings }}</p>
        </div>
        <div class="rounded-2xl bg-ink-700/70 border border-ink-400/20 p-4">
          <p class="text-neutral-400 text-sm">Acciones (pend+en prog.)</p>
          <p class="text-2xl text-blue-300 font-semibold">{{ $actionsPending + $actionsInProg }}</p>
        </div>
        <div class="rounded-2xl bg-ink-700/70 border border-ink-400/20 p-4">
          <p class="text-neutral-400 text-sm">Acciones vencidas</p>
          <p class="text-2xl text-red-300 font-semibold">{{ $actionsOverdue }}</p>
        </div>
      </section>

      {{-- ===== Gráficas (grilla 12 cols) ===== --}}
      <section class="grid grid-cols-1 xl:grid-cols-12 gap-5">
        {{-- Línea: auditorías por mes (xl: 8 cols) --}}
        <div class="xl:col-span-8 rounded-2xl bg-ink-700/80 border border-ink-400/20 p-5 shadow-soft">
          <h3 class="text-neutral-100 font-semibold mb-3">Auditorías por mes</h3>
          <div class="h-[280px] md:h-[320px]">
            <canvas id="chAuditsByMonth" aria-label="Auditorías por mes"></canvas>
          </div>
        </div>

        {{-- Dona: estados (xl: 4 cols) --}}
        <div class="xl:col-span-4 rounded-2xl bg-ink-700/80 border border-ink-400/20 p-5 shadow-soft">
          <h3 class="text-neutral-100 font-semibold mb-3">Distribución por estado</h3>
          <div class="h-[260px] md:h-[300px]">
            <canvas id="chStates" aria-label="Distribución por estado"></canvas>
          </div>
        </div>

        {{-- Barras H: hallazgos por auditoría (xl: 7 cols) --}}
        <div class="xl:col-span-7 rounded-2xl bg-ink-700/80 border border-ink-400/20 p-5 shadow-soft">
          <h3 class="text-neutral-100 font-semibold mb-3">Top auditorías por hallazgos</h3>
          <div class="h-[320px]">
            <canvas id="chFindingsByAudit" aria-label="Hallazgos por auditoría"></canvas>
          </div>
          @if(collect($barFindLabels)->isEmpty())
            <p class="mt-2 text-neutral-400 text-sm">No hay hallazgos registrados aún.</p>
          @endif
        </div>

        {{-- Barras: acciones por estado (xl: 5 cols) --}}
        <div class="xl:col-span-5 rounded-2xl bg-ink-700/80 border border-ink-400/20 p-5 shadow-soft">
          <h3 class="text-neutral-100 font-semibold mb-3">Acciones correctivas</h3>
          <div class="h-[300px]">
            <canvas id="chActionsStatus" aria-label="Acciones correctivas por estado"></canvas>
          </div>
        </div>

        {{-- Carga por auditor (solo admin) (xl: 12 cols) --}}
        @if(auth()->user()?->hasRole('admin'))
          <div class="xl:col-span-12 rounded-2xl bg-ink-700/80 border border-ink-400/20 p-5 shadow-soft">
            <h3 class="text-neutral-100 font-semibold mb-3">Carga por auditor (asignadas)</h3>
            <div class="h-[340px]">
              <canvas id="chWorkload" aria-label="Carga por auditor"></canvas>
            </div>
            @if(collect($workloadLabels)->isEmpty())
              <p class="mt-2 text-neutral-400 text-sm">No hay auditorías asignadas a auditores.</p>
            @endif
          </div>
        @endif
      </section>

      {{-- ===== Auditorías recientes ===== --}}
      <section class="rounded-2xl bg-ink-700/80 border border-ink-400/20 shadow-soft">
        <div class="px-5 py-4 flex items-center justify-between">
          <h3 class="text-lg font-semibold text-neutral-100">Auditorías recientes</h3>
          <a href="{{ route('auditoria.audits.index') }}" class="text-sm text-brand-300 hover:text-brand-200">ver todas</a>
        </div>

        @if($audits->isEmpty())
          <div class="px-5 pb-6">
            <div class="rounded-xl bg-ink-800/70 border border-ink-400/20 p-6 text-center">
              <p class="text-neutral-300">No hay auditorías recientes.</p>
            </div>
          </div>
        @else
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead class="bg-ink-800/70 border-t border-b border-ink-400/20">
                <tr>
                  <th class="px-4 py-3 text-left font-medium text-neutral-300">Objetivo</th>
                  <th class="px-4 py-3 text-left font-medium text-neutral-300">Inicio</th>
                   @if(auth()->user()?->hasRole('admin'))
                  <th class="px-4 py-3 text-left font-medium text-neutral-300">Auditor Responsable</th>
                  @endif
                  <th class="px-4 py-3 text-left font-medium text-neutral-300">Estado</th>
                  <th class="px-4 py-3 text-right font-medium text-neutral-300">Acciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-ink-400/10">
                @foreach($audits as $audit)
                  @php
                    $state = $audit->state;
                    $badge = match($state) {
                      'planned'     => ['bg'=>'bg-yellow-500/20','text'=>'text-yellow-300','dot'=>'bg-yellow-400','label'=>'Planificada'],
                      'in_progress' => ['bg'=>'bg-blue-500/20',  'text'=>'text-blue-300',  'dot'=>'bg-blue-400',  'label'=>'En progreso'],
                      'completed'   => ['bg'=>'bg-green-500/20', 'text'=>'text-green-300', 'dot'=>'bg-green-400', 'label'=>'Completada'],
                      'cancelled'   => ['bg'=>'bg-red-500/20',   'text'=>'text-red-300',   'dot'=>'bg-red-400',   'label'=>'Cancelada'],
                      default       => ['bg'=>'bg-gray-500/20',  'text'=>'text-gray-300',  'dot'=>'bg-gray-400',  'label'=>ucfirst($state)],
                    };
                  @endphp
                  <tr class="hover:bg-ink-800/40">
                    <td class="px-4 py-3 align-top">
                      <div class="text-neutral-100 font-medium">{{ $audit->objective }}</div>
                    </td>
                    <td class="px-4 py-3 align-top text-neutral-200">
                      {{ optional($audit->start_date)->format('d-m-Y') }}
                    </td>
                    @if(auth()->user()?->hasRole('admin'))
                    <td class="px-4 py-3 align-top text-neutral-200">
                      {{ optional($audit->assignee)->full_name ?? optional($audit->assignee)->name ?? 'N/A' }}
                    </td>
                    @endif
                    <td class="px-4 py-3 align-top">
                      <span class="inline-flex items-center gap-2 rounded-full px-3 py-1
                                   {{ $badge['bg'] }} {{ $badge['text'] }} border border-ink-400/10">
                        <span class="h-1.5 w-1.5 rounded-full {{ $badge['dot'] }}"></span>
                        {{ $badge['label'] }}
                      </span>
                    </td>
                    <td class="px-4 py-3 align-top">
                      <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('auditoria.audits.show', $audit->id) }}"
                           class="inline-flex items-center justify-center h-9 w-9 rounded-xl
                                  bg-ink-700/70 hover:bg-ink-600 text-neutral-100 border border-ink-400/20"
                           title="Ver detalles" aria-label="Ver detalles">
                          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-width="1.8" d="M2.46 12C3.73 7.94 7.52 5 12 5s8.27 2.94 9.54 7c-1.27 4.06-5.06 7-9.54 7S3.73 16.06 2.46 12z"/>
                          </svg>
                        </a>
                        @if(auth()->user()?->hasRole('admin'))
                          <a href="{{ route('auditoria.audits.edit', $audit->id) }}"
                             class="inline-flex items-center justify-center h-9 w-9 rounded-xl
                                    bg-ink-700/70 hover:bg-ink-600 text-neutral-100 border border-ink-400/20"
                             title="Editar" aria-label="Editar">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                              <path stroke-linecap="round" stroke-width="1.8" d="M12 20h9"/>
                              <path stroke-linecap="round" stroke-width="1.8" d="M16.5 3.5l4 4L7 21l-4 1 1-4 12.5-14.5z"/>
                            </svg>
                          </a>
                          <form action="{{ route('auditoria.audits.destroy', $audit->id) }}" method="POST"
                                onsubmit="return confirm('¿Eliminar esta auditoría?');">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center justify-center h-9 w-9 rounded-xl
                                           bg-ink-700/70 hover:bg-danger-700/70 text-danger-400 border border-ink-400/20"
                                    title="Eliminar" aria-label="Eliminar">
                              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-width="1.8" d="M6 7h12M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2M19 7l-1 13a2 2 0 01-2 2H8a2 2 0 01-2-2L5 7"/>
                                <path stroke-linecap="round" stroke-width="1.8" d="M10 11v6M14 11v6"/>
                              </svg>
                            </button>
                          </form>
                        @endif
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </section>
    </div>
  </div>

  {{-- ===== Chart.js ===== --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <script>
    const monthsLabels = @json($labelsMonths);
    const monthsData   = @json($dataMonths);

    const stateCounts  = @json($stateCounts);
    const stateLabels  = ['Planificada','En progreso','Completada'];
    const stateData    = [stateCounts.planned, stateCounts.in_progress, stateCounts.completed];

    const findLabels   = @json($barFindLabels);
    const findData     = @json($barFindData);

    const actLabels    = @json($actionsStatusLabels);
    const actData      = @json($actionsStatusData);

    const wlLabels     = @json($workloadLabels ?? []);
    const wlData       = @json($workloadData ?? []);

    const baseTicksColor = '#9CA3AF';
    const gridColor      = 'rgba(148,163,184,0.15)';
    Chart.defaults.color = '#E5E7EB';
    Chart.defaults.font.family = "'Inter','DejaVu Sans','system-ui',sans-serif";
    Chart.defaults.plugins.legend.labels.boxWidth = 12;

    const truncate = (s, n=28) => (s && s.length>n) ? s.slice(0, n-1)+'…' : s;

    const commonOpts = {
      responsive: true,
      maintainAspectRatio: false,
      layout: { padding: { top: 8, right: 8, bottom: 8, left: 8 } },
      plugins: { legend: { labels: { color: '#E5E7EB' } }, tooltip: { intersect:false, mode:'index' } },
      animation: { duration: 280 }
    };

    // Línea
    new Chart(document.getElementById('chAuditsByMonth'), {
      type: 'line',
      data: {
        labels: monthsLabels,
        datasets: [{ label: 'Auditorías', data: monthsData, borderWidth:2, tension:.3, pointRadius:3 }]
      },
      options: {
        ...commonOpts,
        plugins: { ...commonOpts.plugins, legend: { display:false } },
        scales: {
          x: { ticks: { color: baseTicksColor, maxRotation:0, autoSkip:true }, grid: { color: gridColor } },
          y: { beginAtZero:true, ticks: { color: baseTicksColor, precision:0 }, grid: { color: gridColor } }
        }
      }
    });

    // Dona
    new Chart(document.getElementById('chStates'), {
      type: 'doughnut',
      data: { labels: stateLabels, datasets: [{ data: stateData }] },
      options: {
        ...commonOpts,
        cutout: '60%',
        plugins: { ...commonOpts.plugins, legend: { position:'bottom', labels:{ color:'#E5E7EB' } } }
      }
    });

    // Barras H hallazgos
    new Chart(document.getElementById('chFindingsByAudit'), {
      type: 'bar',
      data: { labels: findLabels.map(l => truncate(l, 36)), datasets: [{ label:'Hallazgos', data: findData, borderWidth:1 }] },
      options: {
        ...commonOpts,
        indexAxis: 'y',
        plugins: { ...commonOpts.plugins, legend: { display:false } },
        scales: {
          x: { beginAtZero:true, ticks: { color: baseTicksColor, precision:0 }, grid:{ color: gridColor } },
          y: { ticks: { color: baseTicksColor, autoSkip:true }, grid:{ color:'transparent' } }
        }
      }
    });

    // Barras: acciones por estado
    new Chart(document.getElementById('chActionsStatus'), {
      type: 'bar',
      data: { labels: actLabels, datasets: [{ label:'Acciones', data: actData, borderWidth:1 }] },
      options: {
        ...commonOpts,
        plugins: { ...commonOpts.plugins, legend: { display:false } },
        scales: {
          x: { ticks: { color: baseTicksColor, autoSkip:true }, grid:{ color:'transparent' } },
          y: { beginAtZero:true, ticks: { color: baseTicksColor, precision:0 }, grid:{ color: gridColor } }
        }
      }
    });

    // Carga por auditor
    const elWl = document.getElementById('chWorkload');
    if (elWl && wlLabels.length) {
      new Chart(elWl, {
        type: 'bar',
        data: { labels: wlLabels.map(l => truncate(l)), datasets: [{ label:'Asignadas', data: wlData }] },
        options: {
          ...commonOpts,
          indexAxis: 'y',
          plugins: { ...commonOpts.plugins, legend: { display:false } },
          scales: {
            x: { beginAtZero:true, ticks: { color: baseTicksColor, precision:0 }, grid:{ color: gridColor } },
            y: { ticks: { color: baseTicksColor, autoSkip:true }, grid:{ color:'transparent' } }
          }
        }
      });
    }
  </script>
</x-app-layout>
