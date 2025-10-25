{{-- resources/views/layouts/navigation.blade.php --}}
<div
  x-data="{
    sidebarOpen: false, // offcanvas móvil
    sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === '1',
    toggleSidebar(){
      this.sidebarCollapsed = !this.sidebarCollapsed;
      localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed ? '1' : '0');
      window.dispatchEvent(new CustomEvent('sidebar:changed', { detail: this.sidebarCollapsed }));
    },
    openOffcanvas(){ this.sidebarOpen = true },
    closeOffcanvas(){ this.sidebarOpen = false },
  }"
  class="relative"
>
  @php
    /** @var \App\Models\User|null $auth */
    $auth = Auth::user();
    $avatar = $auth?->profile_photo ? asset('storage/'.$auth->profile_photo) : asset('images/avatar-students.png');
    $displayName = trim(($auth->first_name ?? '').' '.($auth->last_name ?? '')) ?: ($auth->full_name ?? $auth->name ?? $auth->email);
  @endphp

  {{-- ===================== TOPBAR ===================== --}}
  <header
    class="fixed top-0 z-50 h-14 bg-ink-900/80 backdrop-blur border-b border-ink-400/20"
    style="left: var(--sidebar-pad); width: calc(100% - var(--sidebar-pad));"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center gap-3">
      {{-- Desktop: colapsar/expandir | Mobile: hamburguesa --}}
      <div class="flex items-center">
        <button
          class="hidden md:inline-flex items-center justify-center h-9 w-9 rounded-xl
                 text-neutral-300 hover:bg-ink-700 focus:outline-none focus:ring-2 focus:ring-brand-300"
          @click="toggleSidebar()" aria-label="Colapsar/Expandir menú" title="Colapsar/Expandir (Ctrl+B)"
        >
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" x-show="!sidebarCollapsed">
            <path stroke-linecap="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" x-show="sidebarCollapsed" x-cloak>
            <path stroke-linecap="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </button>

        <button
          class="md:hidden inline-flex items-center justify-center h-9 w-9 rounded-xl
                 text-neutral-300 hover:bg-ink-700 focus:outline-none focus:ring-2 focus:ring-brand-300"
          @click="openOffcanvas()" aria-label="Abrir menú"
        >
          <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16"/>
          </svg>
        </button>
      </div>

      {{-- Buscador (dummy por ahora) --}}
      <div class="flex-1">
        <div class="relative">
          <input
            type="text" placeholder="Buscar…"
            class="w-full rounded-xl bg-ink-700/80 border border-ink-400/20 text-neutral-100 placeholder-neutral-400
                   h-9 pl-9 pr-3 focus:outline-none focus:ring-2 focus:ring-brand-300"
          />
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-neutral-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-width="1.8" d="M21 21l-4.35-4.35M10 18a8 8 0 110-16 8 8 0 010 16z"/>
          </svg>
        </div>
      </div>

      {{-- Usuario (avatar + nombre = trigger unificado) --}}
      <div class="flex items-center">
        <x-dropdown align="right" width="56" :matchWidth="false" :asButton="false">
          <x-slot:trigger>
            <span class="inline-flex items-center gap-2 rounded-xl px-2.5 py-1.5
                         text-sm text-neutral-200 hover:text-brand-300 hover:bg-ink-700/70 transition">
              <img src="{{ $avatar }}" alt="Avatar de {{ $displayName }}"
                   class="h-8 w-8 rounded-full border border-ink-400/40 object-cover" />
              <span class="hidden sm:inline">{{ $displayName }}</span>
              <svg class="h-4 w-4 opacity-80" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M5.25 7.5l4.5 4.5 4.5-4.5"/>
              </svg>
            </span>
          </x-slot:trigger>

          <x-slot:content>
            <x-dropdown-link :href="route('auditoria.profile.edit')">Perfil</x-dropdown-link>
            <div class="my-2 h-px bg-ink-400/20"></div>
            <form method="POST" action="{{ route('auditoria.logout') }}">
              @csrf
              <x-dropdown-link :href="route('auditoria.logout')" danger
                onclick="event.preventDefault(); this.closest('form').submit();">
                Cerrar sesión
              </x-dropdown-link>
            </form>
          </x-slot:content>
        </x-dropdown>
      </div>
    </div>

    {{-- Atajo de teclado Ctrl+B --}}
    <script>
      document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'b') {
          e.preventDefault();
          document.querySelectorAll('[x-data]')[0].__x.$data.toggleSidebar();
        }
      });
    </script>
  </header>

  {{-- ===================== SIDEBAR DESKTOP ===================== --}}
  <aside
    class="hidden md:flex fixed top-0 left-0 bottom-0 z-40
           bg-ink-900 border-r border-ink-400/20
           transition-[width] duration-200 ease-out"
    :class="sidebarCollapsed ? 'w-20' : 'w-64'"
  >
    <div class="flex flex-col w-full">
      {{-- Head: logo + nombre (condicional) + botón colapsar --}}
      <div class="h-14 flex items-center border-b border-ink-400/20 px-3">
        <a href="{{ route('auditoria.dashboard.index') }}" class="flex items-center gap-2 group">
          <x-application-logo preset="nav" class="h-8 w-8"/>
          <span class="font-semibold tracking-tight text-neutral-100" x-show="!sidebarCollapsed">INCADEV</span>
        </a>
        <button
          type="button"
          x-on:click="toggleSidebar"
          class="ml-auto inline-flex items-center justify-center h-9 w-9 rounded-xl
                 text-neutral-300 hover:bg-ink-700 focus:outline-none focus:ring-2 focus:ring-brand-300 transition"
          aria-label="Colapsar/Expandir menú"
        >
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" x-show="!sidebarCollapsed">
            <path stroke-linecap="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" x-show="sidebarCollapsed" x-cloak>
            <path stroke-linecap="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </button>
      </div>

      {{-- Items --}}
      @php
        $base = 'flex items-center gap-3 rounded-xl px-3 py-2 mx-2 text-sm transition focus:outline-none focus:ring-2 focus:ring-brand-300';
        $inactive = 'text-neutral-300 hover:bg-ink-700';
        $active   = 'bg-ink-700 text-neutral-50 border border-ink-400/20';
        $pill = fn($isActive) => $base.' '.($isActive ? $active : $inactive);
      @endphp

      <nav class="flex-1 overflow-y-auto py-3 space-y-1">
        <a href="{{ route('auditoria.dashboard.index') }}" class="{{ $pill(request()->routeIs('auditoria.dashboard.index')) }}">
          <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-width="1.8" d="M3 12l9-9 9 9M5 10v10h5V14h4v6h5V10"/>
          </svg>
          <span x-show="!sidebarCollapsed">Dashboard</span>
        </a>

        <a href="{{ route('auditoria.audits.index') }}" class="{{ $pill(request()->routeIs('auditoria.audits.*')) }}">
          <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-width="1.8" d="M9 7h8M9 12h8M9 17h8M5 7h.01M5 12h.01M5 17h.01"/>
          </svg>
          <span x-show="!sidebarCollapsed">Auditorías</span>
        </a>

        @if(auth()->user()?->hasRole('admin'))
          <div class="px-2" x-show="!sidebarCollapsed">
            <div class="text-[10px] uppercase tracking-wider text-neutral-500 mt-3 mb-1">Administración</div>
          </div>

          <a href="{{ route('auditoria.auditores.index') }}" class="{{ $pill(request()->routeIs('auditores.*')) }}">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-width="1.8" d="M16 11c1.66 0 3-1.57 3-3.5S17.66 4 16 4s-3 1.57-3 3.5S14.34 11 16 11zM8 11c1.66 0 3-1.57 3-3.5S9.66 4 8 4 5 5.57 5 7.5 6.34 11 8 11zM8 13c-2.33 0-7 1.17-7 3.5V20h14v-3.5C15 14.17 10.33 13 8 13zM16 13c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V20h6v-3.5c0-2.33-4.67-3.5-7-3.5z"/>
            </svg>
            <span x-show="!sidebarCollapsed">Auditores</span>
          </a>
        @endif

        <a href="{{ route('auditoria.settings.index') }}" class="{{ $pill(request()->routeIs('settings.*')) }}">
          <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-width="1.8" d="M12 8a4 4 0 100 8 4 4 0 000-8z"/>
            <path stroke-linecap="round" stroke-width="1.8" d="M2 12h2m16 0h2M12 2v2m0 16v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
          </svg>
          <span x-show="!sidebarCollapsed">Configuración</span>
        </a>
      </nav>

      <div class="p-3 border-t border-ink-400/20 text-[11px] text-neutral-500" x-show="!sidebarCollapsed">
        v1.0 • Panel INCADEV
      </div>
    </div>
  </aside>

  {{-- ===================== OFFCANVAS MÓVIL ===================== --}}
  <div class="md:hidden">
    <div x-show="sidebarOpen" x-transition.opacity x-cloak
         class="fixed inset-0 z-40 bg-black/50" @click="closeOffcanvas()"></div>

    <aside x-show="sidebarOpen" x-cloak
           x-transition:enter="transition ease-out duration-150"
           x-transition:enter-start="-translate-x-full opacity-0"
           x-transition:enter-end="translate-x-0 opacity-100"
           x-transition:leave="transition ease-in duration-100"
           x-transition:leave-start="translate-x-0 opacity-100"
           x-transition:leave-end="-translate-x-full opacity-0"
           class="fixed top-14 left-0 bottom-0 z-50 w-72
                  bg-ink-900/95 backdrop-blur border-r border-ink-400/20">
      <nav class="py-3">
        <a href="{{ route('auditoria.dashboard.index') }}"
           class="flex items-center gap-3 rounded-xl px-3 py-2 mx-2 text-sm
                  {{ request()->routeIs('auditoria.dashboard.index') ? 'bg-ink-700 text-neutral-50 border border-ink-400/20' : 'text-neutral-300 hover:bg-ink-700' }}">
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-width="1.8" d="M3 12l9-9 9 9M5 10v10h5V14h4v6h5V10"/>
          </svg>
          Dashboard
        </a>

        <a href="{{ route('auditoria.audits.index') }}"
           class="flex items-center gap-3 rounded-xl px-3 py-2 mx-2 text-sm
                  {{ request()->routeIs('audits.*') ? 'bg-ink-700 text-neutral-50 border border-ink-400/20' : 'text-neutral-300 hover:bg-ink-700' }}">
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-width="1.8" d="M9 7h8M9 12h8M9 17h8M5 7h.01M5 12h.01M5 17h.01"/>
          </svg>
          Auditorías
        </a>

        @if(auth()->user()?->hasRole('admin'))
          <div class="px-3 mt-3 mb-1 text-[10px] uppercase tracking-wider text-neutral-500">Administración</div>
          <a href="{{ route('auditoria.auditores.index') }}"
             class="flex items-center gap-3 rounded-xl px-3 py-2 mx-2 text-sm
                    {{ request()->routeIs('auditores.*') ? 'bg-ink-700 text-neutral-50 border border-ink-400/20' : 'text-neutral-300 hover:bg-ink-700' }}">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-width="1.8" d="M16 11c1.66 0 3-1.57 3-3.5S17.66 4 16 4s-3 1.57-3 3.5S14.34 11 16 11zM8 11c1.66 0 3-1.57 3-3.5S9.66 4 8 4 5 5.57 5 7.5 6.34 11 8 11zM8 13c-2.33 0-7 1.17-7 3.5V20h14v-3.5C15 14.17 10.33 13 8 13zM16 13c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V20h6v-3.5c0-2.33-4.67-3.5-7-3.5z"/>
            </svg>
            Auditores
          </a>
        @endif

        <a href="{{ route('auditoria.settings.index') }}"
           class="flex items-center gap-3 rounded-xl px-3 py-2 mx-2 text-sm
                  {{ request()->routeIs('settings.*') ? 'bg-ink-700 text-neutral-50 border border-ink-400/20' : 'text-neutral-300 hover:bg-ink-700' }}">
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-width="1.8" d="M12 8a4 4 0 100 8 4 4 0 000-8z"/>
            <path stroke-linecap="round" stroke-width="1.8" d="M2 12h2m16 0h2M12 2v2m0 16v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
          </svg>
          Configuración
        </a>
      </nav>
    </aside>
  </div>
</div>
