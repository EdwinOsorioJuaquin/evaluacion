{{-- resources/views/settings/index.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-neutral-100">Configuración</h2>
      <a href="{{ route('auditoria.dashboard.index') }}"
         class="text-sm text-neutral-400 hover:text-brand-300 transition">Volver al dashboard</a>
    </div>
  </x-slot>

  {{-- Subnav sticky (anclas) --}}
  <nav class="sticky top-[calc(var(--topbar-h)+1px)] z-10 bg-ink-800/70 backdrop-blur
              border border-ink-400/20 rounded-xl px-3 sm:px-4 py-2.5 mb-6">
    <ul class="flex flex-wrap gap-2 text-sm">
      @php $lnk = 'px-3 py-1.5 rounded-lg text-neutral-300 hover:text-neutral-50 hover:bg-ink-600/60 transition'; @endphp
      <li><a href="#apariencia" class="{{ $lnk }}">Apariencia</a></li>
      <li><a href="#notificaciones" class="{{ $lnk }}">Notificaciones</a></li>
      <li><a href="#datos" class="{{ $lnk }}">Datos & Exportación</a></li>
    </ul>
  </nav>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Columna principal --}}
    <section class="lg:col-span-2 space-y-6">


      {{-- APARIENCIA --}}
      <div id="apariencia" class="rounded-2xl bg-ink-700/80 border border-ink-400/20 shadow-soft">
  <div class="p-5 sm:p-6 border-b border-ink-400/20">
    <h3 class="text-lg font-semibold text-neutral-50">Apariencia</h3>
    <p class="text-neutral-400 text-sm">Tema y preferencias de interfaz.</p>
  </div>

  <div class="p-5 sm:p-6 space-y-5" x-data="{ mode: localStorage.getItem('theme_mode') || 'dark' }">
    <label class="block">
      <span class="text-neutral-200 text-sm font-medium">Modo de tema</span>
      <select x-model="mode" @change="changeTheme(mode)"
        class="mt-2 w-full bg-ink-800 border border-ink-400/20 rounded-lg p-2.5 text-neutral-100 focus:ring-brand-400 focus:border-brand-400">
        <option value="light">☀️ Claro</option>
        <option value="dark">🌙 Oscuro</option>
        <option value="system">🖥️ Sistema</option>
      </select>
    </label>
  </div>
</div>

<script>
  function changeTheme(mode) {
    localStorage.setItem('theme_mode', mode);

    if (mode === 'dark') {
      document.documentElement.classList.add('dark');
      document.documentElement.classList.remove('light');
    } else if (mode === 'light') {
      document.documentElement.classList.add('light');
      document.documentElement.classList.remove('dark');
    } else {
      // Detectar tema del sistema
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      document.documentElement.classList.toggle('dark', prefersDark);
      document.documentElement.classList.toggle('light', !prefersDark);
    }
  }

  // Aplicar tema al cargar la página
  document.addEventListener('DOMContentLoaded', () => {
    const savedMode = localStorage.getItem('theme_mode') || 'dark';
    changeTheme(savedMode);
  });
</script>


      {{-- NOTIFICACIONES --}}
      <div id="notificaciones" class="rounded-2xl bg-ink-700/80 border border-ink-400/20 shadow-soft">
        <div class="p-5 sm:p-6 border-b border-ink-400/20">
          <h3 class="text-lg font-semibold text-neutral-50">Notificaciones</h3>
          <p class="text-neutral-400 text-sm">Define cómo y cuándo te avisamos.</p>
        </div>
        <form method="POST" action="{{ route('auditoria.settings.notifications.update') }}" class="p-5 sm:p-6 space-y-4">
          @csrf @method('PUT')

          {{-- Checkboxes simples (usa tus estilos si tienes x-ui.switch) --}}
          @php
            $n = $notifications ?? (object)[
              'email_audits' => true,
              'email_findings' => true,
              'email_reports' => false,
              'browser_push' => false,
            ];
          @endphp

          <label class="flex items-center justify-between gap-3 rounded-xl border border-ink-400/20 px-4 py-3">
            <div>
              <div class="font-medium text-neutral-100">Actualizaciones de auditorías</div>
              <div class="text-xs text-neutral-400">Cambios de estado y asignaciones.</div>
            </div>
            <input type="checkbox" name="email_audits" value="1" class="h-5 w-5 accent-brand-500" {{ 12 < 12 ? 'checked' : '' }}>
          </label>

          <label class="flex items-center justify-between gap-3 rounded-xl border border-ink-400/20 px-4 py-3">
            <div>
              <div class="font-medium text-neutral-100">Nuevos hallazgos</div>
              <div class="text-xs text-neutral-400">Cuando se registra un hallazgo en tu área.</div>
            </div>
            <input type="checkbox" name="email_findings" value="1" class="h-5 w-5 accent-brand-500" {{ 12 < 12   ? 'checked' : '' }}>
          </label>

          <label class="flex items-center justify-between gap-3 rounded-xl border border-ink-400/20 px-4 py-3">
            <div>
              <div class="font-medium text-neutral-100">Reportes finales</div>
              <div class="text-xs text-neutral-400">Envío/actualización de recomendaciones.</div>
            </div>
            <input type="checkbox" name="email_reports" value="1" class="h-5 w-5 accent-brand-500" {{ 12 < 12 ? 'checked' : '' }}>
          </label>

          <label class="flex items-center justify-between gap-3 rounded-xl border border-ink-400/20 px-4 py-3">
            <div>
              <div class="font-medium text-neutral-100">Notificaciones del navegador</div>
              <div class="text-xs text-neutral-400">Requiere permiso del navegador.</div>
            </div>
            <input type="checkbox" name="browser_push" value="1" class="h-5 w-5 accent-brand-500" {{ 12 < 12 ? 'checked' : '' }}>
          </label>

          <x-ui.button type="submit" variant="primary">Guardar notificaciones</x-ui.button>
        </form>
      </div>

      {{-- DATOS & EXPORTACIÓN --}}
      <div id="datos" class="rounded-2xl bg-ink-700/80 border border-ink-400/20 shadow-soft">
        <div class="p-5 sm:p-6 border-b border-ink-400/20">
          <h3 class="text-lg font-semibold text-neutral-50">Datos & Exportación</h3>
          <p class="text-neutral-400 text-sm">Exporta o elimina tu información.</p>
        </div>
        <div class="p-5 sm:p-6 space-y-4">
          <form method="POST" action="{{ route('auditoria.settings.data.export') }}">
            @csrf
            <x-ui.button variant="secondary">Exportar mis datos (.zip)</x-ui.button>
          </form>

          <div class="rounded-xl bg-danger-100/5 border border-danger-500/30 p-4">
            <h4 class="font-medium text-danger-500">Zona peligrosa</h4>
            <p class="text-xs text-neutral-400 mb-3">Desactiva tu cuenta. Podrás reactivarla más adelante (soft-delete).</p>
            <form method="POST" action="{{ route('auditoria.settings.account.deactivate') }}">
              @csrf
              <x-ui.button variant="danger">Desactivar cuenta</x-ui.button>
            </form>
          </div>
        </div>
      </div>

    </section>

    {{-- Columna lateral (resumen) --}}
    <aside class="space-y-6">
      <div class="rounded-2xl bg-ink-700/80 border border-ink-400/20 shadow-soft p-5">
        <div class="flex items-center gap-3">
          <img src="{{ $user->profile_photo ? asset('storage/'.$user->profile_photo) : asset('images/avatar-students.png') }}"
               class="h-12 w-12 rounded-full border border-ink-400/30 object-cover" alt="Avatar">
          <div>
            <div class="font-semibold text-neutral-100">{{ $user->first_name }} {{ $user->last_name }}</div>
            <div class="text-xs text-neutral-400">{{ $user->email }}</div>
          </div>
        </div>
        <ul class="mt-4 space-y-2 text-sm text-neutral-300">
          <li>Zona horaria: <span class="text-neutral-100">{{ $user->timezone ?? '—' }}</span></li>
          <li>País: <span class="text-neutral-100">{{ $user->country ?? '—' }}</span></li>
          <li>Roles: <span class="text-neutral-100">{{ $user->hashRoles ?? '—' }}</span></li>
        </ul>
      </div>

      {{-- Tip de seguridad --}}
      <div class="rounded-2xl bg-ink-700/80 border border-ink-400/20 shadow-soft p-5">
        <h4 class="font-medium text-neutral-100 mb-1">Consejo</h4>
        <p class="text-sm text-neutral-400">
          Activa las notificaciones de <span class="text-neutral-200">reportes finales</span> para recibir alertas cuando
          se publiquen recomendaciones de auditorías.
        </p>
      </div>
    </aside>
  </div>
</x-app-layout>
