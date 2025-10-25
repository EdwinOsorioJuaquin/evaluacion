<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-neutral-100 tracking-tight">
        Crear nueva auditoría
      </h2>
      <a href="{{ route('auditoria.audits.index') }}"
         class="inline-flex items-center gap-2 rounded-xl px-3 py-2
                bg-ink-700/70 hover:bg-ink-600 text-neutral-100 border border-ink-400/20">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-width="1.8" d="M15 12H9M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9z"/>
        </svg>
        Volver
      </a>
    </div>
  </x-slot>

  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
       x-data="auditForm()"
       x-init="init()"
  >
    {{-- Mensaje flash --}}
    @if (session('success'))
      <div class="mb-4 rounded-xl border border-green-500/20 bg-green-500/10 text-green-300 px-4 py-3">
        {{ session('success') }}
      </div>
    @endif

    <form method="POST" action="{{ route('auditoria.dashboard.store-audit') }}" class="space-y-6">
      @csrf

      {{-- Card: Información básica --}}
      <section class="rounded-2xl bg-ink-700/80 border border-ink-400/20 shadow-soft p-6">
        <h3 class="text-lg font-semibold text-neutral-100 mb-4">Información básica</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          {{-- Área --}}
          <x-ui.input
            name="area"
            label="Área"
            placeholder="Académica / Administrativa / TI …"
            :value="old('area')"
            required
            :messages="$errors->get('area')"
          >
            <x-slot:prefix>
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-width="1.6" d="M4 6h16M4 12h8M4 18h6"/>
              </svg>
            </x-slot:prefix>
          </x-ui.input>

          {{-- Objetivo --}}
          <x-ui.input
            name="objective"
            label="Objetivo"
            placeholder="Mejorar proceso de evaluación docente…"
            :value="old('objective')"
            required
            :messages="$errors->get('objective')"
          >
            <x-slot:prefix>
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-width="1.6" d="M12 6v12M6 12h12"/>
              </svg>
            </x-slot:prefix>
          </x-ui.input>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
          {{-- Tipo --}}
          <div>
            <x-input-label for="type" class="text-neutral-300">Tipo</x-input-label>
            <select id="type" name="type"
                    class="mt-1 w-full rounded-xl bg-ink-600 border border-ink-400/30 text-neutral-100
                           focus:ring-2 focus:ring-brand-300 focus:border-brand-400"
                    required>
              @php $oldType = old('type','internal'); @endphp
              <option value="internal"  {{ $oldType === 'internal' ? 'selected' : '' }}>Interna</option>
              <option value="external"  {{ $oldType === 'external' ? 'selected' : '' }}>Externa</option>
            </select>
            <x-input-error :messages="$errors->get('type')" class="mt-2" />
          </div>

          {{-- Estado --}}
          <div>
            <x-input-label for="state" class="text-neutral-300">Estado</x-input-label>
            <select id="state" name="state"
                    class="mt-1 w-full rounded-xl bg-ink-600 border border-ink-400/30 text-neutral-100
                           focus:ring-2 focus:ring-brand-300 focus:border-brand-400"
                    required>
              @php $oldState = old('state','planned'); @endphp
              <option value="planned"     {{ $oldState === 'planned' ? 'selected' : '' }}>Planificada</option>
              <option value="in_progress" {{ $oldState === 'in_progress' ? 'selected' : '' }}>En progreso</option>
              <option value="completed"   {{ $oldState === 'completed' ? 'selected' : '' }}>Completada</option>
              <option value="cancelled"   {{ $oldState === 'cancelled' ? 'selected' : '' }}>Cancelada</option>
            </select>
            <x-input-error :messages="$errors->get('state')" class="mt-2" />
          </div>

          {{-- Asignar a (solo admin) --}}
          @if(auth()->user()?->hasRole('admin'))
            <div>
              <x-input-label for="assignee" class="text-neutral-300">Asignar a</x-input-label>
              <select id="assignee" name="assignee"
                      class="mt-1 w-full rounded-xl bg-ink-600 border border-ink-400/30 text-neutral-100
                             focus:ring-2 focus:ring-brand-300 focus:border-brand-400">
                <option value="">— Sin asignar (serás tú) —</option>
                @foreach($auditors as $aud)
                  @php
                    $label = trim(($aud->first_name ?? '').' '.($aud->last_name ?? ''));
                    if (!$label) { $label = $aud->full_name ?? $aud->name ?? $aud->email; }
                  @endphp
                  <option value="{{ $aud->id }}" @selected(old('assignee') == $aud->id)>{{ $label }}</option>
                @endforeach
              </select>
              <x-input-error :messages="$errors->get('assignee')" class="mt-2" />
            </div>
          @endif
        </div>
      </section>

      {{-- Card: Fechas --}}
      <section class="rounded-2xl bg-ink-700/80 border border-ink-400/20 shadow-soft p-6">
        <h3 class="text-lg font-semibold text-neutral-100 mb-4">Calendario</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          {{-- Fecha inicio --}}
          <div>
            <x-input-label for="start_date" class="text-neutral-300">Fecha de inicio</x-input-label>
            <input type="date" id="start_date" name="start_date"
                   x-model="start"
                   x-on:change="syncMinEnd()"
                   class="mt-1 w-full rounded-xl bg-ink-600 border border-ink-400/30 text-neutral-100
                          focus:ring-2 focus:ring-brand-300 focus:border-brand-400"
                   required>
            <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
          </div>

          {{-- Fecha fin --}}
          <div>
            <x-input-label for="end_date" class="text-neutral-300">Fecha de fin</x-input-label>
            <input type="date" id="end_date" name="end_date"
                   x-model="end"
                   :min="start"
                   class="mt-1 w-full rounded-xl bg-ink-600 border border-ink-400/30 text-neutral-100
                          focus:ring-2 focus:ring-brand-300 focus:border-brand-400"
                   required>
            <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
          </div>
        </div>
      </section>

      {{-- Card: Resumen / notas --}}
      <section class="rounded-2xl bg-ink-700/80 border border-ink-400/20 shadow-soft p-6">
        <h3 class="text-lg font-semibold text-neutral-100 mb-4">Resumen / notas</h3>
        <label for="summary_results" class="sr-only">Resumen</label>
        <textarea id="summary_results" name="summary_results" rows="4"
                  class="w-full rounded-xl bg-ink-600 border border-ink-400/30 text-neutral-100
                         focus:ring-2 focus:ring-brand-300 focus:border-brand-400 placeholder-neutral-400"
                  placeholder="Contexto, alcance, puntos clave… (opcional)">{{ old('summary_results') }}</textarea>
        <x-input-error :messages="$errors->get('summary_results')" class="mt-2" />
      </section>

      {{-- Barra de acciones --}}
      <div class="flex items-center justify-end gap-3">
        <a href="{{ route('auditoria.audits.index') }}"
           class="inline-flex items-center gap-2 rounded-xl px-4 py-2
                  bg-ink-700/70 hover:bg-ink-600 text-neutral-100 border border-ink-400/20">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-width="1.8" d="M15 19l-7-7 7-7"/>
          </svg>
          Cancelar
        </a>

        <button type="submit"
                class="inline-flex items-center gap-2 rounded-2xl px-5 py-2.5
                       bg-brand-500 hover:bg-brand-400 text-black shadow-soft
                       focus:outline-none focus:ring-2 focus:ring-brand-300 transition">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-width="1.8" d="M5 12h14M12 5l7 7-7 7"/>
          </svg>
          Crear auditoría
        </button>
      </div>
    </form>
  </div>

  {{-- Alpine helpers fechas --}}
  <script>
    function auditForm() {
      return {
        start: @json(old('start_date')),
        end:   @json(old('end_date')),
        today() {
          const d = new Date();
          return d.toISOString().slice(0,10);
        },
        init() {
          // set min hoy
          const s = document.getElementById('start_date');
          const e = document.getElementById('end_date');
          if (s && !s.value) s.value = this.today();
          if (e && !e.value) e.value = s.value;
          this.start = s.value; this.end = e.value;
          this.syncMinEnd();
        },
        syncMinEnd() {
          const e = document.getElementById('end_date');
          if (e) {
            e.min = this.start || this.today();
            if (this.end && this.end < e.min) {
              this.end = e.min;
              e.value = e.min;
            }
          }
        }
      }
    }
  </script>
</x-app-layout>
