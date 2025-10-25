<x-app-layout>
  {{-- HEADER --}}
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-neutral-100 leading-tight">
        Editar Hallazgo
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
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
      <div class="rounded-2xl bg-ink-700/80 border border-ink-400/20 shadow-soft">
        <div class="p-6 sm:p-8 space-y-6">

          {{-- Mensajes de estado --}}
          @if (session('success'))
            <div class="rounded-xl border border-success-500/30 bg-success-500/10 text-green-300 px-4 py-3">
              {{ session('success') }}
            </div>
          @endif

          @if ($errors->any())
            <div class="rounded-xl border border-danger-500/30 bg-danger-500/10 text-danger-300 px-4 py-3">
              <div class="font-semibold mb-1">Por favor corrige los errores:</div>
              <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
              </ul>
            </div>
          @endif

          {{-- FORMULARIO DE EDICIÓN --}}
          <form method="POST"
                action="{{ route('auditoria.findings.update', [$audit, $finding]) }}"
                enctype="multipart/form-data"
                class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Descripción --}}
            <div>
              <label for="description" class="block text-sm font-medium text-neutral-200 mb-1">
                Descripción del hallazgo <span class="text-danger-500">*</span>
              </label>
              <textarea id="description" name="description" rows="4" required
                        class="w-full rounded-xl bg-ink-800/70 border border-ink-400/30 text-neutral-100
                               placeholder-neutral-500 px-3 py-2 focus:ring-brand-300 focus:border-brand-400"
                        placeholder="Actualiza la descripción del hallazgo...">{{ old('description', $finding->description) }}</textarea>
              @error('description') <p class="mt-1 text-xs text-danger-400">{{ $message }}</p> @enderror
            </div>

            {{-- Clasificación --}}
            <div>
              <label for="classification" class="block text-sm font-medium text-neutral-200 mb-1">
                Clasificación
              </label>
              <select id="classification" name="classification"
                      class="w-full h-10 rounded-xl bg-ink-800/70 border border-ink-400/30 text-neutral-100 px-3
                             focus:ring-brand-300 focus:border-brand-400">
                <option value="Revisado"  {{ old('classification', $finding->classification) === 'Revisado'  ? 'selected' : '' }}>Revisado</option>
                <option value="Observado" {{ old('classification', $finding->classification) === 'Observado' ? 'selected' : '' }}>Observado</option>
                <option value="No aplica" {{ old('classification', $finding->classification) === 'No aplica' ? 'selected' : '' }}>No aplica</option>
              </select>
              @error('classification') <p class="mt-1 text-xs text-danger-400">{{ $message }}</p> @enderror
            </div>

            {{-- Severidad --}}
            <div>
              <label for="severity" class="block text-sm font-medium text-neutral-200 mb-1">
                Severidad
              </label>
              <select id="severity" name="severity"
                      class="w-full h-10 rounded-xl bg-ink-800/70 border border-ink-400/30 text-neutral-100 px-3
                             focus:ring-brand-300 focus:border-brand-400">
                <option value="low"    {{ old('severity', $finding->severity) === 'low' ? 'selected' : '' }}>Baja</option>
                <option value="medium" {{ old('severity', $finding->severity) === 'medium' ? 'selected' : '' }}>Media</option>
                <option value="high"   {{ old('severity', $finding->severity) === 'high' ? 'selected' : '' }}>Alta</option>
              </select>
              @error('severity') <p class="mt-1 text-xs text-danger-400">{{ $message }}</p> @enderror
            </div>

            {{-- Evidencia (texto) --}}
            <div>
              <label for="evidence" class="block text-sm font-medium text-neutral-200 mb-1">
                Evidencia (texto o descripción)
              </label>
              <textarea id="evidence" name="evidence" rows="3"
                        class="w-full rounded-xl bg-ink-800/70 border border-ink-400/30 text-neutral-100
                               placeholder-neutral-500 px-3 py-2 focus:ring-brand-300 focus:border-brand-400"
                        placeholder="Actualiza la evidencia si es necesario...">{{ old('evidence', $finding->evidence) }}</textarea>
              @error('evidence') <p class="mt-1 text-xs text-danger-400">{{ $message }}</p> @enderror
            </div>

            {{-- Archivo (opcional) --}}
            <div>
              <label class="block text-sm font-medium text-neutral-200 mb-1">Reemplazar documento (opcional)</label>
              <input type="file" name="document"
                     class="w-full rounded-xl bg-ink-800/70 border border-ink-400/30 text-neutral-100
                            file:bg-brand-500 file:text-black file:border-0 file:px-4 file:py-2
                            file:rounded-lg hover:file:bg-brand-400 transition">
              @error('document') <p class="mt-1 text-xs text-danger-400">{{ $message }}</p> @enderror

              @if ($finding->evidence && str_contains($finding->evidence, '/'))
                <p class="mt-2 text-xs text-neutral-400">
                  Archivo actual:
                  <a href="{{ Storage::url($finding->evidence) }}" target="_blank"
                     class="text-brand-400 hover:text-brand-300">
                    {{ basename($finding->evidence) }}
                  </a>
                </p>
              @endif
            </div>

            {{-- BOTONES --}}
            <div class="flex justify-end gap-3 pt-4">
              <a href="{{ route('auditoria.audits.show', $audit) }}"
                 class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5
                        bg-ink-800/70 border border-ink-400/20 text-neutral-200 hover:bg-ink-700 transition">
                Cancelar
              </a>
              <button type="submit"
                      class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5
                             bg-brand-500 text-black font-semibold hover:bg-brand-400 transition">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
                Guardar cambios
              </button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</x-app-layout>
