<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
</head>
<body
  class="bg-ink-900 text-neutral-100 antialiased font-sans relative
         overflow-x-hidden overflow-y-auto
         min-h-screen min-h-[100svh] supports-[min-height:100dvh]:min-h-[100dvh]">

  {{-- Halos sutiles --}}
  <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10">
    <div class="absolute -top-40 -left-40 w-[500px] h-[500px] bg-brand-500/12 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-40 -right-40 w-[520px] h-[520px] bg-brand-500/10 rounded-full blur-3xl"></div>
  </div>

  {{-- Contenido principal (scrollable en móvil) --}}
  <div class="mx-auto max-w-7xl px-6 py-10 md:py-14">
    <div class="min-h-screen flex flex-col md:flex-row md:items-center md:justify-center gap-10 md:gap-16">

      {{-- Izquierda: marca / branding --}}
    <section class="flex items-center justify-center w-full md:w-2/5 p-8 md:p-12">
        <div class="flex flex-col items-center text-center space-y-6">
            <x-application-logo preset="login" class="mx-auto w-24 h-24 rounded-full object-cover" />
            
            <h1 class="text-4xl md:text-5xl font-semibold text-brand-400 tracking-tight">
                INCADEV
            </h1>
            
            <p class="text-neutral-400 text-sm md:text-base max-w-sm leading-relaxed">
                Instituto de Capacitación y Desarrollo Virtual<br>
                <span class="text-brand-300 font-medium">Formando profesionales digitales</span>
            </p>
        </div>
    </section>

    {{-- Derecha: formulario de login --}}
    <section class="flex items-center justify-center w-full md:w-3/5 bg-ink-800/40 p-8 md:p-12">
        <div id="login" class="w-full max-w-md rounded-2xl bg-ink-700/80 border border-ink-400/20 shadow-soft p-8 md:p-10">
            {{ $slot }}
        </div>
    </section>

    </div>
  </div>

  {{-- Footer en flujo (no fixed/absolute) --}}
  <footer class="py-6 text-center text-neutral-500 text-xs">
    © {{ date('Y') }} <span class="text-brand-400 font-medium">INCADEV</span>. Todos los derechos reservados.
  </footer>
</body>
</html>
