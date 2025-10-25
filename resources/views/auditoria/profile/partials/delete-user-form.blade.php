<section
  x-data="{ open:false }"
  x-on:open-modal.window="if($event.detail === 'confirm-user-deletion') open=true"
>
  <header class="mb-4">
    <h2 class="text-lg font-semibold text-danger-300">Eliminar cuenta</h2>
    <p class="text-sm text-neutral-400">
      Esta acción es permanente. Descarga cualquier dato que quieras conservar.
    </p>
  </header>

  <x-ui.button variant="danger" x-on:click="open=true">Eliminar cuenta</x-ui.button>

  {{-- Modal --}}
  <div x-cloak x-show="open"
       class="fixed inset-0 z-[10000] flex items-center justify-center">
    <div class="absolute inset-0 bg-black/60" x-on:click="open=false"></div>

    <div class="relative w-full max-w-md rounded-2xl bg-ink-800 border border-ink-400/20 p-6">
      <h3 class="text-lg font-semibold text-neutral-100">¿Eliminar tu cuenta?</h3>
      <p class="text-sm text-neutral-400 mt-1">
        Ingresa tu contraseña para confirmar la eliminación.
      </p>

      <form method="post" action="{{ route('auditoria.profile.destroy') }}" class="mt-4 space-y-4">
        @csrf
        @method('delete')

        <x-ui.input name="password" type="password" label="Contraseña" required />
        <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />

        <div class="flex items-center justify-end gap-2">
          <button type="button"
                  class="rounded-2xl px-4 py-2.5 bg-ink-700 border border-ink-400/20 text-neutral-200 hover:bg-ink-600"
                  x-on:click="open=false">
            Cancelar
          </button>
          <x-ui.button variant="danger" type="submit">Eliminar definitivamente</x-ui.button>
        </div>
      </form>
    </div>
  </div>
</section>
