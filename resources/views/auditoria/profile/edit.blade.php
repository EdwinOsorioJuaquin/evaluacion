<x-app-layout>
  <x-slot name="header">
    <div>
      <h2 class="font-semibold text-xl text-neutral-100 leading-tight">Perfil</h2>
      <p class="text-sm text-neutral-400 mt-1">Administra tu información, seguridad y cuenta.</p>
    </div>
  </x-slot>

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
      <div class="rounded-2xl bg-ink-700/80 border border-ink-400/20 shadow-soft p-6">
        <div class="max-w-3xl">
          @include('auditoria.profile.partials.update-profile-information-form')
        </div>
      </div>

      <div class="rounded-2xl bg-ink-700/80 border border-ink-400/20 shadow-soft p-6">
        <div class="max-w-2xl">
          @include('auditoria.profile.partials.update-password-form')
        </div>
      </div>

      <div class="rounded-2xl bg-ink-700/80 border border-ink-400/20 shadow-soft p-6">
        <div class="max-w-2xl">
          @include('auditoria.profile.partials.delete-user-form')
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
