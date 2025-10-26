<x-guest-layout>

      {{-- Header --}}
      <div class="text-center mb-6 space-y-3">
        <x-application-logo preset="login" class="mx-auto" />
        <h2 class="text-xl font-semibold text-neutral-50 tracking-tight">SISTEMA DE AUDITORÍA</h2>
        <p class="text-sm text-neutral-400">Accede a tu cuenta para continuar</p>
      </div>

      {{-- Estado de sesión --}}
      <x-auth-session-status class="mb-4" :status="session('status')" />

      {{-- Formulario --}}
      <form method="POST" action="{{ route('auditoria.auth.login.post') }}" class="space-y-6">
        @csrf

        {{-- Correo con ícono (usa x-ui.input) --}}
        <x-ui.input
          name="email"
          type="email"
          label="Correo electrónico"
          placeholder="tu_correo@incadev.edu.pe"
          :value="old('email')"
          autocomplete="username"
          required
          autofocus
          :messages="$errors->get('email')"
        >
          <x-slot:prefix>
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4"
                    d="M4 6h16a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4"
                    d="M22 8l-10 6L2 8"/>
            </svg>
          </x-slot:prefix>
        </x-ui.input>

        {{-- Contraseña con ícono + toggle (x-ui.input con toggle) --}}
        <x-ui.input
          name="password"
          type="password"
          label="Contraseña"
          placeholder="••••••••"
          autocomplete="current-password"
          required
          toggle="true"
          :messages="$errors->get('password')"
        >
          <x-slot:prefix>
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4"
                    d="M12 15a3 3 0 100-6 3 3 0 000 6z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4"
                    d="M2.46 12C3.73 7.94 7.52 5 12 5s8.27 2.94 9.54 7c-1.27 4.06-5.06 7-9.54 7S3.73 16.06 2.46 12z"/>
            </svg>
          </x-slot:prefix>
        </x-ui.input>

        {{-- Recordarme / Olvidar --}}
        <div class="flex items-center justify-between text-sm">
          <label for="remember_me" class="inline-flex items-center gap-2 text-neutral-300">
            <input id="remember_me" name="remember" type="checkbox"
                   class="rounded bg-ink-600 border-ink-400/40 text-brand-500 focus:ring-brand-300">
            <span>Recordarme</span>
          </label>

          @if (Route::has('auditoria.password.request'))
            <a href="{{ route('auditoria.password.request') }}"
               class="text-brand-400 hover:text-brand-300 font-medium">
              ¿Olvidaste tu contraseña?
            </a>
          @endif
        </div>

        {{-- Botón submit primario --}}
        <div>
          <x-ui.button type="submit" variant="primary" class="w-full">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            Iniciar sesión
          </x-ui.button>
        </div>
      </form>

      {{-- Pie --}}
      <div class="text-center mt-6 text-neutral-500 text-xs">
        ¿No tienes cuenta?
        <a href="{{ route('auditoria.auth.register') }}" class="text-brand-400 hover:text-brand-300 font-medium">Regístrate</a>
      </div>
      <div>
        <a href="{{ url('/') }}" 
          class="absolute top-6 left-6 inline-flex items-center gap-2 px-4 py-2 bg-ink-700/60 border border-brand-400/40 text-brand-300 text-sm font-medium rounded-lg backdrop-blur-md hover:bg-ink-700 hover:border-brand-300 transition duration-300">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
          </svg>
          Volver al inicio
        </a>

      </div>
</x-guest-layout>
