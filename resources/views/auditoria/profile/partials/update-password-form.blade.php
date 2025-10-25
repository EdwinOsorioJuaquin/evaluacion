<section>
  <header class="mb-4">
    <h2 class="text-lg font-semibold text-neutral-100">Actualizar contraseña</h2>
    <p class="text-sm text-neutral-400">Usa una contraseña robusta y única.</p>
  </header>

  @if (session('status') === 'password-updated')
    <div class="mb-4 rounded-xl border border-green-400/30 bg-green-500/10 text-green-300 px-4 py-2">
      Contraseña actualizada.
    </div>
  @endif

  <form method="post" action="{{ route('auditoria.profile.password.update') }}" class="space-y-5">
    @csrf
    @method('put')

    <x-ui.input
      name="current_password"
      type="password"
      label="Contraseña actual"
      toggle="true"
      autocomplete="current-password"
      required
    />
    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />

    <x-ui.input
      name="password"
      type="password"
      label="Nueva contraseña"
      toggle="true"
      autocomplete="new-password"
      helper="Mínimo 8 caracteres. Combina mayúsculas, minúsculas, números y símbolos."
      required
    />
    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />

    <x-ui.input
      name="password_confirmation"
      type="password"
      label="Confirmar nueva contraseña"
      toggle="true"
      autocomplete="new-password"
      required
    />
    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />

    <div class="flex items-center gap-3">
      <x-ui.button type="submit" variant="primary">Guardar</x-ui.button>
    </div>
  </form>
</section>
