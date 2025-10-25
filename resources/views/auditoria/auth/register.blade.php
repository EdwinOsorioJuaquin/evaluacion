<x-guest-layout>
      <div class="text-center mb-6 space-y-2">
        <x-application-logo preset="login" class="mx-auto" />
        <h2 class="text-xl font-semibold text-neutral-50 tracking-tight">Crear cuenta</h2>
        <p class="text-sm text-neutral-400">Regístrate para comenzar a usar INCADEV</p>
      </div>

      <form method="POST" action="{{ route('auditoria.auth.register.post') }}" class="space-y-5" x-data="{ loading:false }" x-on:submit="loading=true">
        @csrf

        {{-- Nombres --}}
        <div class="grid md:grid-cols-2 gap-4">
          <x-ui.input
            name="first_name"
            type="text"
            label="Nombres"
            placeholder="Juan"
            :value="old('first_name')"
            autocomplete="given-name"
            required
            :messages="$errors->get('first_name')"
          >
            <x-slot:prefix>
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M16 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M12 14c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5z"/>
              </svg>
            </x-slot:prefix>
          </x-ui.input>

          <x-ui.input
            name="last_name"
            type="text"
            label="Apellidos"
            placeholder="Pérez"
            :value="old('last_name')"
            autocomplete="family-name"
            required
            :messages="$errors->get('last_name')"
          >
            <x-slot:prefix>
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M16 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M12 14c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5z"/>
              </svg>
            </x-slot:prefix>
          </x-ui.input>
        </div>

        {{-- Email --}}
        <x-ui.input
          name="email"
          type="email"
          label="Correo institucional"
          placeholder="nombre@incadev.edu.pe"
          :value="old('email')"
          autocomplete="username"
          required
          :messages="$errors->get('email')"
        >
          <x-slot:prefix>
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M4 6h16a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M22 8l-10 6L2 8"/>
            </svg>
          </x-slot:prefix>
        </x-ui.input>

        {{-- Teléfono (opcional) --}}
        <x-ui.input
          name="phone_number"
          type="tel"
          label="Teléfono (opcional)"
          placeholder="+51 999 888 777"
          :value="old('phone_number')"
          autocomplete="tel"
          :messages="$errors->get('phone_number')"
        >
          <x-slot:prefix>
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M3 5a2 2 0 012-2h3l2 4-2 1a12 12 0 006 6l1-2 4 2v3a2 2 0 01-2 2h-1C8.82 19 5 15.18 5 10V9a2 2 0 012-2H7z"/>
            </svg>
          </x-slot:prefix>
        </x-ui.input>

        {{-- Passwords --}}
        <div class="grid md:grid-cols-2 gap-4">
          <x-ui.input
            name="password"
            type="password"
            label="Contraseña"
            placeholder="••••••••"
            autocomplete="new-password"
            required
            toggle="true"
            :messages="$errors->get('password')"
          >
            <x-slot:prefix>
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M12 15a3 3 0 100-6 3 3 0 000 6z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M2.46 12C3.73 7.94 7.52 5 12 5s8.27 2.94 9.54 7c-1.27 4.06-5.06 7-9.54 7S3.73 16.06 2.46 12z"/>
              </svg>
            </x-slot:prefix>
          </x-ui.input>

          <x-ui.input
            name="password_confirmation"
            type="password"
            label="Confirmar contraseña"
            placeholder="••••••••"
            autocomplete="new-password"
            required
            toggle="true"
            :messages="$errors->get('password_confirmation')"
          >
            <x-slot:prefix>
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M12 15a3 3 0 100-6 3 3 0 000 6z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M2.46 12C3.73 7.94 7.52 5 12 5s8.27 2.94 9.54 7c-1.27 4.06-5.06 7-9.54 7S3.73 16.06 2.46 12z"/>
              </svg>
            </x-slot:prefix>
          </x-ui.input>
        </div>

        {{-- Acciones --}}
        <div class="flex items-center justify-between pt-2">
          <a href="{{ route('auditoria.auth.login') }}" class="text-sm text-brand-400 hover:text-brand-300 font-medium">
            ¿Ya tienes una cuenta? Inicia sesión
          </a>

          <x-ui.button type="submit" variant="primary" x-bind:disabled="loading">
            <svg x-show="loading" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
            <span x-text="loading ? 'Creando…' : 'Registrarme'"></span>
          </x-ui.button>
        </div>
      </form>
    </div>
  </div>
</x-guest-layout>
