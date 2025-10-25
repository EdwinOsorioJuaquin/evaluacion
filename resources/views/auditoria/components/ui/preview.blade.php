@extends('layouts.app') {{-- o la que uses --}}

@section('content')
<div class="min-h-screen bg-ink-900 text-neutral-100 py-10">
  <div class="max-w-5xl mx-auto px-6 space-y-10">

    <header class="flex items-center justify-between">
      <div class="flex items-center gap-4">
        <x-application-logo size="xl" alt="INCADEV"/>
        <div>
          <h1 class="text-2xl font-semibold">UI Preview</h1>
          <p class="text-neutral-400">Paleta y componentes modernizados</p>
        </div>
      </div>
      <div class="flex items-center gap-3">
        <x-primary-button>Guardar</x-primary-button>
        <x-secondary-button>Cancelar</x-secondary-button>
        <x-danger-button>Eliminar</x-danger-button>
      </div>
    </header>

    <div class="grid md:grid-cols-2 gap-8">
      <!-- Card -->
      <div class="rounded-2xl bg-ink-700/80 border border-ink-400/20 p-6 shadow-soft">
        <h3 class="text-lg font-semibold mb-4">Formulario</h3>
        <form class="space-y-4">
          <x-input-label for="email">Email</x-input-label>
          <x-text-input id="email" type="email" placeholder="tucorreo@incadev.dev" />

          <x-input-label for="password">Contraseña</x-input-label>
          <x-text-input id="password" type="password" placeholder="••••••••" />

          <div class="flex items-center gap-3 pt-2">
            <x-primary-button>Acceder</x-primary-button>
            <x-secondary-button>Recuperar</x-secondary-button>
          </div>
        </form>
      </div>

      <!-- Dropdown / Alerts -->
      <div class="rounded-2xl bg-ink-700/80 border border-ink-400/20 p-6 shadow-soft space-y-4">
        <h3 class="text-lg font-semibold">Estados</h3>

        <x-auth-session-status status="Sesión iniciada correctamente." />

        <x-dropdown>
          <x-slot:trigger>Menú</x-slot:trigger>
          <x-dropdown-link href="#">Perfil</x-dropdown-link>
          <x-dropdown-link href="#" :active="true">Ajustes</x-dropdown-link>
          <x-dropdown-link href="#">Cerrar sesión</x-dropdown-link>
        </x-dropdown>

        <div class="flex items-center gap-2">
          <x-ui.button icon="true" aria-label="Icon">
            <!-- icono simple -->
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M12 6v12M6 12h12" />
            </svg>
          </x-ui.button>
          <x-ui.button variant="ghost">Ghost</x-ui.button>
          <x-ui.button variant="secondary">Secondary</x-ui.button>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
