{{-- resources/views/auditoria/findings/index.blade.php --}}
<x-app-layout>
  {{-- Header --}}
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-neutral-100 leading-tight">
        Hallazgos de la auditoría
      </h2>

      <a href="{{ route('auditoria.audits.show', $audit) }}"
         class="inline-flex items-center gap-2 h-9 rounded-2xl px-3
                bg-ink-800/70 border border-ink-400/20 text-neutral-200
                hover:bg-ink-700 transition">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-width="1.8" d="M15 19l-7-7 7-7"/>
        </svg>
        Volver a la auditoría
      </a>
    </div>
  </x-slot>

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

      {{-- ================== NUEVO HALLAZGO ================== --}}
      <section class="rounded-2xl bg-ink-700/80 border border-ink-400/20 shadow-soft">
        <div class="p-6">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-neutral-100">Añadir hallazgo</h3>

            @if ($audit->state !== 'in_progress')
              <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs
                           bg-neutral-700 text-neutral-300 border border-ink-400/20">
                La auditoría no está en progreso
              </span>
            @endif
          </div>

          <form
            x-data="findingForm()"
            method="POST"
            action="{{ route('auditoria.findings.store', $audit) }}"
            enctype="multipart/form-data"
            class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4"
          >
            @csrf

            {{-- Descripción --}}
            <div class="md:col-span-2">
              <label for="description" class="text-sm text-neutral-300">Descripción del hallazgo</label>
              <div class="relative mt-1">
                <textarea
                  id="description" name="description" rows="4" x-model="desc"
                  class="w-full rounded-xl bg-ink-800/70 border border-ink-400/20 text-neutral-100
                         placeholder-neutral-400 px-3 py-2 focus:ring-brand-300 focus:border-brand-300"
                  placeholder="Describe el hallazgo de manera concreta y accionable..." required
                ></textarea>
                <div class="absolute bottom-2 right-3 text-xs text-neutral-400" x-text="`${desc.length}/500`"></div>
              </div>
              @error('description')
                <p class="mt-1 text-danger-500 text-xs">{{ $message }}</p>
              @enderror
            </div>

            {{-- Clasificación --}}
            <div>
              <label for="classification" class="text-sm text-neutral-300">Clasificación</label>
              <select id="classification" name="classification" required
                      class="mt-1 w-full h-10 rounded-xl bg-ink-800/70 border border-ink-400/20
                             text-neutral-100 px-3 focus:ring-brand-300 focus:border-brand-300">
                <option value="Revisado" {{ old('classification')==='Revisado' ? 'selected' : '' }}>Revisado</option>
                <option value="Observado" {{ old('classification')==='Observado' ? 'selected' : '' }}>Observado</option>
                <option value="No aplica" {{ old('classification')==='No aplica' ? 'selected' : '' }}>No aplica</option>
              </select>
              @error('classification')
                <p class="mt-1 text-danger-500 text-xs">{{ $message }}</p>
              @enderror
            </div>

            {{-- Severidad --}}
            <div>
              <label for="severity" class="text-sm text-neutral-300">Severidad</label>
              <select id="severity" name="severity" required
                      class="mt-1 w-full h-10 rounded-xl bg-ink-800/70 border border-ink-400/20
                             text-neutral-100 px-3 focus:ring-brand-300 focus:border-brand-300">
                <option value="low" {{ old('severity')==='low' ? 'selected' : '' }}>Baja</option>
                <option value="medium" {{ old('severity')==='medium' ? 'selected' : '' }}>Media</option>
                <option value="high" {{ old('severity')==='high' ? 'selected' : '' }}>Alta</option>
              </select>
              @error('severity')
                <p class="mt-1 text-danger-500 text-xs">{{ $message }}</p>
              @enderror
            </div>

            {{-- Evidencia (texto) --}}
            <div class="md:col-span-2">
              <label for="evidence" class="text-sm text-neutral-300">Evidencia (texto opcional)</label>
              <textarea id="evidence" name="evidence" rows="3"
                        class="mt-1 w-full rounded-xl bg-ink-800/70 border border-ink-400/20
                               text-neutral-100 placeholder-neutral-400 px-3 py-2
                               focus:ring-brand-300 focus:border-brand-300"
                        placeholder="Describe brevemente la evidencia si no adjuntarás documento.">{{ old('evidence') }}</textarea>
              @error('evidence')
                <p class="mt-1 text-danger-500 text-xs">{{ $message }}</p>
              @enderror
            </div>

            {{-- Archivo de evidencia --}}
            <div class="md:col-span-2">
              <label class="text-sm text-neutral-300">Adjuntar documento (opcional)</label>
              <div
                x-on:drop.prevent="onDrop($event)"
                x-on:dragover.prevent
                class="mt-1 rounded-xl border-2 border-dashed border-ink-400/30
                       bg-ink-800/40 p-4 text-neutral-300"
              >
                <div class="flex items-center gap-3">
                  <svg class="h-5 w-5 text-neutral-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-width="1.8"
                          d="M12 16v-8m0 0l-3 3m3-3l3 3M6 20h12a2 2 0 002-2v-6a2 2 0 00-2-2h-3l-2-2H6a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                  </svg>
                  <div class="text-sm">
                    <label for="document" class="text-brand-400 hover:text-brand-300 cursor-pointer">
                      Selecciona un archivo
                    </label>
                    <span class="text-neutral-400"> o arrástralo aquí</span>
                    <div class="text-xs text-neutral-500 mt-1">PDF, imágenes o DOCX (máx. 10MB)</div>
                  </div>
                </div>
                <input id="document" name="document" type="file" class="hidden" x-ref="file" x-on:change="onFileChange">
                <template x-if="fileName">
                  <div class="mt-3 text-xs text-neutral-300">
                    Archivo seleccionado: <span class="font-medium text-neutral-100" x-text="fileName"></span>
                  </div>
                </template>
              </div>
              @error('document')
                <p class="mt-1 text-danger-500 text-xs">{{ $message }}</p>
              @enderror
            </div>

            {{-- Submit --}}
            <div class="md:col-span-2 flex justify-end pt-2">
              <button type="submit"
                {{ $audit->state !== 'in_progress' ? 'disabled' : '' }}
                class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5
                       bg-brand-500 text-black font-semibold hover:bg-brand-400
                       disabled:opacity-50 disabled:cursor-not-allowed transition">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-width="1.8" d="M12 5v14M5 12h14"/>
                </svg>
                Agregar hallazgo
              </button>
            </div>
          </form>
        </div>
      </section>

    </div>
  </div>

  {{-- Alpine helpers --}}
  <script>
    function findingForm(){
      return {
        desc: @json(old('description','')),
        fileName: '',
        onFileChange(e){
          this.fileName = e.target.files?.[0]?.name || '';
        },
        onDrop(e){
          const file = e.dataTransfer.files?.[0];
          if(file){
            this.$refs.file.files = e.dataTransfer.files;
            this.fileName = file.name;
          }
        }
      }
    }
  </script>
</x-app-layout>
