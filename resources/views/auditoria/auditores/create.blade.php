<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-neutral-100 leading-tight">Nuevo usuario</h2>
  </x-slot>

  <div class="py-6">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
      <div class="rounded-2xl bg-ink-700/80 border border-ink-400/20 shadow-soft p-6 space-y-6">

        @if ($errors->any())
          <div class="rounded-xl border border-danger-500/30 bg-danger-500/10 text-danger-300 px-4 py-3">
            <div class="font-semibold mb-1">Corrige los siguientes errores:</div>
            <ul class="list-disc list-inside text-sm">
              @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
          </div>
        @endif

        <form action="{{ route('auditoria.auditores.store') }}" method="POST" class="space-y-6">
          @csrf

          <div class="grid sm:grid-cols-2 gap-4">
            <x-ui.input name="first_name" label="Nombres" :value="old('first_name')" required />
            <x-ui.input name="last_name"  label="Apellidos" :value="old('last_name')" required />
          </div>

          <div class="grid sm:grid-cols-2 gap-4">
            <x-ui.input name="email" type="email" label="Correo" :value="old('email')" required />
            <x-ui.input name="phone_number" label="Teléfono" :value="old('phone_number')" />
          </div>

          <div class="grid sm:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-neutral-200 mb-1">Rol</label>
              <select name="role" class="w-full h-10 rounded-xl bg-ink-800/70 border border-ink-400/30 text-neutral-100 focus:ring-brand-300 focus:border-brand-400" required>
                @foreach($roles as $k => $label)
                  <option value="{{ $k }}" @selected(old('role')===$k)>{{ $label }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-neutral-200 mb-1">Estado</label>
              <select name="status" class="w-full h-10 rounded-xl bg-ink-800/70 border border-ink-400/30 text-neutral-100" required>
                <option value="active" @selected(old('status')==='active')>Activo</option>
                <option value="inactive" @selected(old('status')==='inactive')>Inactivo</option>
              </select>
            </div>
          </div>

          <div class="grid sm:grid-cols-2 gap-4">
            <x-ui.input name="password" type="password" label="Contraseña" required toggle="true" />
            <x-ui.input name="password_confirmation" type="password" label="Confirmar contraseña" required />
          </div>

          <div class="flex gap-3">
            <x-ui.button type="submit" variant="primary">Guardar</x-ui.button>
            <a href="{{ route('auditoria.auditores.index') }}" class="inline-flex items-center rounded-2xl px-4 py-2.5 bg-ink-800/70 border border-ink-400/20 text-neutral-200 hover:bg-ink-700">
              Cancelar
            </a>
          </div>
        </form>

      </div>
    </div>
  </div>
</x-app-layout>
