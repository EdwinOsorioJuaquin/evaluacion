{{-- resources/views/corrective-actions/create.blade.php --}}
<x-app-layout>
  {{-- Header --}}
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-semibold text-xl text-neutral-100 leading-tight">
          Registrar acción correctiva
        </h2>
        <p class="text-sm text-neutral-400 mt-1">
          Auditoría: <span class="text-neutral-100">{{ $audit->objective }}</span>
          • Hallazgo: <span class="text-neutral-100">{{ \Illuminate\Support\Str::limit($finding->description, 80) }}</span>
        </p>
      </div>

      <a href="{{ route('auditoria.findings.show', [$audit->id, $finding->id]) }}"
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
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
      <div class="rounded-2xl bg-ink-700/80 border border-ink-400/20 shadow-soft">
        <div class="p-6 md:p-8">
          {{-- Title --}}
          <div class="mb-6">
            <h3 class="text-lg font-semibold text-neutral-50">Nueva acción correctiva</h3>
            <p class="text-sm text-neutral-400 mt-1">
              Registra la acción de mejora que atiende el hallazgo. Procura que la descripción sea específica y medible.
            </p>
          </div>

          {{-- Alerts (flash / errors globales) --}}
          @if (session('success'))
            <div class="mb-4 rounded-xl border border-green-500/30 bg-green-500/10 text-green-300 px-4 py-3">
              {{ session('success') }}
            </div>
          @endif

          @if ($errors->any())
            <div class="mb-4 rounded-xl border border-danger-500/30 bg-danger-500/10 text-danger-300 px-4 py-3">
              <div class="font-semibold mb-1">Revisa los campos:</div>
              <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $e)
                  <li>{{ $e }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          {{-- Form --}}
          <form method="POST"
                action="{{ route('auditoria.actions.store', ['audit' => $audit->id, 'finding' => $finding->id]) }}"
                class="space-y-6">
            @csrf

            {{-- Descripción --}}
            <div>
              <label for="description" class="block text-sm font-medium text-neutral-200 mb-1">
                Descripción <span class="text-danger-500">*</span>
              </label>
              <textarea
                id="description"
                name="description"
                rows="5"
                class="w-full rounded-xl bg-ink-800/70 border border-ink-400/30 text-neutral-100
                       placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-brand-300 focus:border-brand-400 px-3 py-2"
                placeholder="Ej.: Implementar un registro digital de asistencia con validación semanal por el coordinador del área…"
                required>{{ old('description') }}</textarea>
              @error('description')
                <p class="mt-2 text-xs text-danger-400">{{ $message }}</p>
              @enderror
            </div>

            {{-- Fechas --}}
            @php
              // sugerimos min hoy o el start_date de la auditoría si existe
              $today = now()->toDateString();
              $minStart = optional($audit->start_date)->toDateString() ?: $today;
              $minDue   = old('engagement_date') ?: $minStart;
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="engagement_date" class="block text-sm font-medium text-neutral-200 mb-1">
                  Fecha de compromiso <span class="text-danger-500">*</span>
                </label>
                <input
                  type="date"
                  id="engagement_date"
                  name="engagement_date"
                  value="{{ old('engagement_date') }}"
                  min="{{ $minStart }}"
                  class="w-full h-10 rounded-xl bg-ink-800/70 border border-ink-400/30 text-neutral-100
                         focus:outline-none focus:ring-2 focus:ring-brand-300 focus:border-brand-400 px-3"
                  required>
                @error('engagement_date')
                  <p class="mt-2 text-xs text-danger-400">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label for="due_date" class="block text-sm font-medium text-neutral-200 mb-1">
                  Fecha límite <span class="text-danger-500">*</span>
                </label>
                <input
                  type="date"
                  id="due_date"
                  name="due_date"
                  value="{{ old('due_date') }}"
                  min="{{ $minDue }}"
                  class="w-full h-10 rounded-xl bg-ink-800/70 border border-ink-400/30 text-neutral-100
                         focus:outline-none focus:ring-2 focus:ring-brand-300 focus:border-brand-400 px-3"
                  required>
                <p class="mt-2 text-xs text-neutral-400">Debe ser igual o posterior a la fecha de compromiso.</p>
                @error('due_date')
                  <p class="mt-2 text-xs text-danger-400">{{ $message }}</p>
                @enderror
              </div>
            </div>

            {{-- CTA --}}
            <div class="pt-2 flex flex-wrap gap-3">
              <button type="submit"
                      class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5
                             bg-brand-500 text-black font-semibold hover:bg-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-300">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-width="1.8" d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
                Registrar acción
              </button>

              <a href="{{ route('auditoria.findings.show', [$audit->id, $finding->id]) }}"
                 class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5
                        bg-ink-800/70 border border-ink-400/20 text-neutral-200 hover:bg-ink-700">
                Cancelar
              </a>
            </div>
          </form>

          {{-- Contexto del Hallazgo (mini) --}}
          <div class="mt-8 rounded-xl border border-ink-400/20 bg-ink-800/40 p-4">
            <div class="text-sm text-neutral-300">
              <div class="mb-1">
                <span class="text-neutral-400">Hallazgo:</span>
                <span class="text-neutral-100">{{ $finding->description }}</span>
              </div>
              <div class="flex flex-wrap gap-2 text-xs">
                @php
                  $classMap = ['Revisado'=>'bg-green-500 text-white','Observado'=>'bg-yellow-500 text-black'];
                  $sevMap = ['high'=>'Alta','medium'=>'Media','low'=>'Baja'];
                @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full {{ $classMap[$finding->classification] ?? 'bg-neutral-600 text-white' }}">
                  {{ ucfirst($finding->classification) }}
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-ink-700 text-neutral-200 border border-ink-400/30">
                  Severidad: {{ $sevMap[strtolower($finding->severity ?? '')] ?? ucfirst($finding->severity ?? '—') }}
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-ink-700 text-neutral-200 border border-ink-400/30">
                  Área: {{ $audit->area }}
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-ink-700 text-neutral-200 border border-ink-400/30">
                  Auditoría: {{ \Illuminate\Support\Str::limit($audit->objective, 50) }}
                </span>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</x-app-layout>
