<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<html lang="es" x-data x-bind:class="localStorage.getItem('theme_mode') || 'dark'">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'INCADEV') }}</title>
    <meta name="description" content="Plataforma de Evaluación y Auditoría Educativa del Instituto de Capacitación y Desarrollo Virtual (INCADEV)">
    <meta name="author" content="Grupo 06 - Procesos de Evaluación y Mejora">
    <meta name="keywords" content="INCADEV, auditoría, evaluación, educativa, satisfacción, docente, monitoreo, mejora continua">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/incadev-logo.png') }}" class="w-12 h-12 p-2 " />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}" />
    <link rel="manifest" href="{{ asset('site.webmanifest') }}" />
    {{-- Font Awesome for Icons --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

  {{-- Fonts --}}
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

  {{-- Assets --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    :root { --topbar-h: 56px; } /* h-14 */
  </style>
</head>
<body
  x-data="{
    collapsed: localStorage.getItem('sidebarCollapsed') === '1',
    calcPad() {
      // Desktop: 16rem (w-64) expandido, 5rem (w-20) colapsado; móvil: 0
      const isDesktop = window.innerWidth >= 768;
      return isDesktop ? (this.collapsed ? '5rem' : '16rem') : '0px';
    },
    applyPad() {
      document.documentElement.style.setProperty('--sidebar-pad', this.calcPad());
    }
  }"
  x-init="
    applyPad();
    window.addEventListener('resize', () => applyPad());
    // el navigation.blade.php dispara 'sidebar:changed' con detail=true/false
    window.addEventListener('sidebar:changed', (e) => { collapsed = !!e.detail; applyPad(); });
  "
  class="min-h-screen bg-ink-900 text-neutral-100 antialiased font-sans overflow-x-hidden"
>

  {{-- NAV global (TOPBAR + SIDEBAR) --}}
  @include('layouts.navigation')

  {{-- HEADER DE PÁGINA (opcional, queda debajo del topbar) --}}
  @isset($header)
    <header
      class="sticky z-30 bg-ink-900/80 backdrop-blur border-b border-ink-400/20"
      style="top: var(--topbar-h); padding-left: var(--sidebar-pad);"
    >
      <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
        {{ $header }}
      </div>
    </header>
  @endisset

  {{-- CONTENIDO PRINCIPAL (respeta sidebar y topbar) --}}
  <main
    style="padding-left: var(--sidebar-pad);"
    class="pt-[calc(var(--topbar-h)+16px)] pb-10 transition-[padding-left] duration-200"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="rounded-2xl bg-ink-700/80 border border-ink-400/20 shadow-soft p-6">
        {{ $slot }}
      </div>
    </div>
  </main>

</body>
</html>
