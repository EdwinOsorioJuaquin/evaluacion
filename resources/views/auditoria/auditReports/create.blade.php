{{-- resources/views/auditoria/auditReports/create.blade.php --}}
<x-app-layout>
  {{-- === HEADER === --}}
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-semibold text-xl text-neutral-100 leading-tight">
          Agregar recomendaciones finales de la auditoría
        </h2>
        <p class="text-sm text-neutral-400 mt-1">
          Auditoría: <span class="text-neutral-100">{{ $audit->objective }}</span> • Área:
          <span class="text-neutral-100">{{ $audit->area }}</span>
        </p>
      </div>

      <a href="{{ route('auditoria.audits.show', $audit->id) }}"
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

          {{-- === FLASH & ERRORES === --}}
          @if (session('success'))
            <div class="rounded-xl border border-success-500/30 bg-success-500/10 text-green-300 px-4 py-3">
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

          {{-- === FORMULARIO === --}}
          <form method="POST" action="{{ route('auditoria.reports.store', $audit->id) }}" class="space-y-6">
            @csrf

            {{-- Resumen --}}
            <div>
              <label for="resume" class="block text-sm font-medium text-neutral-200 mb-1">
                Resumen <span class="text-danger-500">*</span>
              </label>
              <textarea
                id="resume"
                name="resume"
                rows="5"
                class="w-full rounded-xl bg-ink-800/70 border border-ink-400/30 text-neutral-100
                       placeholder-neutral-500 px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-brand-300 focus:border-brand-400"
                placeholder="Describe brevemente el resultado general de la auditoría, puntos fuertes y oportunidades."
                required>{{ old('resume') }}</textarea>
              @error('resume') <p class="mt-2 text-xs text-danger-400">{{ $message }}</p> @enderror
            </div>

            {{-- Recomendaciones --}}
            <div>
              <label for="recommendations" class="block text-sm font-medium text-neutral-200 mb-1">
                Recomendaciones <span class="text-danger-500">*</span>
              </label>
              <textarea
                id="recommendations"
                name="recommendations"
                rows="7"
                class="w-full rounded-xl bg-ink-800/70 border border-ink-400/30 text-neutral-100
                       placeholder-neutral-500 px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-brand-300 focus:border-brand-400"
                placeholder="Enumera recomendaciones accionables (usa numeración, responsables y plazos)."
                required>{{ old('recommendations') }}</textarea>
              @error('recommendations') <p class="mt-2 text-xs text-danger-400">{{ $message }}</p> @enderror
            </div>

            {{-- Indicadores --}}
            <div>
              <div class="flex items-center justify-between">
                <label for="indicators" class="block text-sm font-medium text-neutral-200 mb-1">
                  Indicadores (opcional)
                </label>
                <span class="text-xs text-neutral-500">Ej.: tasa de cierre, N° de hallazgos, SLA de acciones, etc.</span>
              </div>
              <textarea
                id="indicators"
                name="indicators"
                rows="4"
                class="w-full rounded-xl bg-ink-800/70 border border-ink-400/30 text-neutral-100
                       placeholder-neutral-500 px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-brand-300 focus:border-brand-400"
                placeholder="Define métricas que permitan medir el avance y la efectividad de las acciones correctivas.">{{ old('indicators') }}</textarea>
              @error('indicators') <p class="mt-2 text-xs text-danger-400">{{ $message }}</p> @enderror
            </div>

            {{-- Acciones --}}
            <div class="flex flex-wrap gap-3 pt-2">
              <button type="submit"
                      class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5
                             bg-brand-500 text-black font-semibold hover:bg-brand-400">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
                Guardar
              </button>

              <a href="{{ route('auditoria.audits.show', $audit->id) }}"
                 class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5
                        bg-ink-800/70 border border-ink-400/20 text-neutral-200 hover:bg-ink-700">
                Cancelar
              </a>
            </div>
          </form>

          {{-- Consejos UX --}}
          <div class="rounded-xl bg-ink-800/40 border border-ink-400/20 p-4 mt-8">
            <ul class="text-sm text-neutral-300 list-disc pl-5 space-y-1">
              <li>Usa listas numeradas en <span class="text-neutral-100">Recomendaciones</span> para claridad.</li>
              <li>En <span class="text-neutral-100">Indicadores</span> define métricas medibles (KPI) y periodicidad.</li>
              <li>Tras guardar, podrás <span class="text-neutral-100">generar el reporte PDF</span> desde la vista de la auditoría.</li>
            </ul>
          </div>

        </div>
      </div>
    </div>
  </div>
</x-app-layout>
