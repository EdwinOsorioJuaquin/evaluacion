<x-app-layout>
  {{-- HEADER --}}
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold text-neutral-50 leading-tight">
        Editar Acción Correctiva
      </h2>
      <a href="{{ route('auditoria.findings.show', [$audit, $finding]) }}"
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
      <div class="rounded-2xl bg-ink-700/80 border border-ink-400/20 shadow-soft p-6 space-y-6">

        {{-- Estado de éxito --}}
        @if(session('success'))
          <div class="rounded-xl border border-green-500/30 bg-green-500/10 text-green-300 px-4 py-3">
            {{ session('success') }}
          </div>
        @endif

        {{-- Errores --}}
        @if($errors->any())
          <div class="rounded-xl border border-danger-500/30 bg-danger-500/10 text-danger-300 px-4 py-3">
            <p class="font-semibold mb-2">Por favor corrija los errores a continuación:</p>
            <ul class="list-disc list-inside text-sm">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        {{-- FORMULARIO --}}
        <form method="POST"
              action="{{ route('auditoria.actions.update', [$audit, $finding, $correctiveAction]) }}"
              class="space-y-6">
          @csrf
          @method('PUT')



          {{-- Descripción --}}
          <div>
            <label for="descripcion" class="block text-sm font-medium text-neutral-200 mb-1">
              Descripción
            </label>
            <textarea id="descripcion" name="descripcion" rows="4"
                      class="w-full rounded-xl bg-ink-800/70 border border-ink-400/30
                             text-neutral-100 px-3 py-2 placeholder-neutral-400
                             focus:ring-brand-300 focus:border-brand-400"
                      placeholder="Describe la acción correctiva a implementar...">{{ old('descripcion', $correctiveAction->description) }}</textarea>
            @error('descripcion')
              <p class="mt-1 text-xs text-danger-400">{{ $message }}</p>
            @enderror
          </div>

          {{-- RESPONSABLE / FECHA / PRIORIDAD --}}
          <div>
            <label for="responsable" class="block text-sm font-medium text-neutral-200 mb-1">
                Responsable
            </label>

            {{-- Selector dinámico de usuarios --}}
            <select id="responsable" name="responsable"
                    class="w-full rounded-xl bg-ink-800/70 border border-ink-400/30
                            text-neutral-100 px-3 py-2 focus:ring-brand-300 focus:border-brand-400">

                {{-- Opción por defecto: usuario actual asignado --}}
                @if($correctiveAction->user)
                    <option value="{{ $correctiveAction->user->id }}" selected>
                    {{ $correctiveAction->user->full_name }} (actual)
                    </option>
                @endif

                {{-- Listar otros usuarios disponibles --}}
                @foreach($usuarios as $usuario)
                    {{-- Evitar repetir el usuario actual --}}
                    @if(!$correctiveAction->user || $usuario->id !== $correctiveAction->user->id)
                    <option value="{{ $usuario->id }}">
                        {{ $usuario->full_name }} — {{ ucfirst(is_array($usuario->role) ? $usuario->role[0] : $usuario->role) }}
                    </option>

                    @endif
                @endforeach
            </select>

            <p class="text-xs text-neutral-500 mt-1">Selecciona otro auditor o administrador si deseas reasignar la acción.</p>

            @error('responsable')
                <p class="mt-1 text-xs text-danger-400">{{ $message }}</p>
            @enderror
            </div>


            <div>
              <label for="fecha_limite" class="block text-sm font-medium text-neutral-200 mb-1">
                Fecha Límite
              </label>
              <input type="date" id="fecha_limite" name="fecha_limite"
                     value="{{ old('fecha_limite', optional($correctiveAction->fecha_limite)->format('Y-m-d')) }}"
                     class="w-full rounded-xl bg-ink-800/70 border border-ink-400/30
                            text-neutral-100 px-3 py-2 focus:ring-brand-300 focus:border-brand-400" />
              @error('fecha_limite')
                <p class="mt-1 text-xs text-danger-400">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="prioridad" class="block text-sm font-medium text-neutral-200 mb-1">
                Prioridad
              </label>
              <select id="prioridad" name="prioridad"
                      class="w-full h-10 rounded-xl bg-ink-800/70 border border-ink-400/30
                             text-neutral-100 px-3 focus:ring-brand-300 focus:border-brand-400">
                @php $prioridades = ['baja' => 'Baja', 'media' => 'Media', 'alta' => 'Alta']; @endphp
                @foreach($prioridades as $key => $label)
                  <option value="{{ $key }}" {{ old('prioridad', $correctiveAction->prioridad) == $key ? 'selected' : '' }}>
                    {{ $label }}
                  </option>
                @endforeach
              </select>
              @error('prioridad')
                <p class="mt-1 text-xs text-danger-400">{{ $message }}</p>
              @enderror
            </div>
          </div>

          {{-- ESTADO --}}
          <div>
            <label for="estado" class="block text-sm font-medium text-neutral-200 mb-1">
              Estado
            </label>
            <select id="estado" name="estado"
                    class="w-full h-10 rounded-xl bg-ink-800/70 border border-ink-400/30
                           text-neutral-100 px-3 focus:ring-brand-300 focus:border-brand-400">
              @php
                $estados = [
                  'pendiente' => 'Pendiente',
                  'en_progreso' => 'En progreso',
                  'completada' => 'Completada'
                ];
              @endphp
              @foreach($estados as $key => $label)
                <option value="{{ $key }}" {{ old('estado', $correctiveAction->estado) == $key ? 'selected' : '' }}>
                  {{ $label }}
                </option>
              @endforeach
            </select>
            @error('estado')
              <p class="mt-1 text-xs text-danger-400">{{ $message }}</p>
            @enderror
          </div>

          {{-- BOTONES --}}
          <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('auditoria.findings.show', [$audit, $finding]) }}"
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
              Guardar Cambios
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>
</x-app-layout>
