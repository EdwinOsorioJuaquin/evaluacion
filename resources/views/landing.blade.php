<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'INCADEV') }} | Plataforma Unificada</title>
  <meta name="description" content="Plataforma unificada de Evaluación, Impacto, Auditoría y Satisfacción Educativa del Instituto de Capacitación y Desarrollo Virtual (INCADEV)">
  <meta name="author" content="Grupo 06 - Procesos de Evaluación y Mejora">
  <meta name="keywords" content="INCADEV, evaluación, auditoría, satisfacción estudiantil, impacto, docencia, educación">

  {{-- Favicon --}}
  <link rel="icon" type="image/png" href="{{ asset('images/incadev-logo.png') }}" />
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}" />
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}" />

  {{-- Fonts & Icons --}}
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  {{-- Tailwind / Vite --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#0f0f1c] text-neutral-100 font-figtree antialiased selection:bg-cyan-400/30 selection:text-black flex flex-col">

  {{-- ===== NAVBAR ===== --}}
  <header class="sticky top-0 z-50 border-b border-cyan-500/20 bg-[#0f0f1c]/90 backdrop-blur">
    <div class="max-w-7xl mx-auto px-5 h-16 flex items-center justify-between">
      <a href="#" class="flex items-center gap-2 group">
        <img src="{{ asset('images/incadev-logo.png') }}" class="h-9 w-9 rounded-md" alt="Logo INCADEV">
        <span class="text-cyan-300 font-semibold text-lg group-hover:text-cyan-400 transition">INCADEV</span>
      </a>

      <nav class="hidden md:flex items-center gap-6">
        <a href="#about" class="hover:text-cyan-400 transition">Nosotros</a>
        <a href="#modulos" class="hover:text-cyan-400 transition">Módulos</a>
        <a href="#contact" class="hover:text-cyan-400 transition">Contáctanos</a>
      </nav>
    </div>
  </header>

  {{-- ===== HERO ===== --}}
  <section class="relative overflow-hidden py-20 md:py-28">
    <div class="absolute inset-0 -z-10">
      <div class="absolute -top-40 -left-40 h-96 w-96 bg-cyan-500/10 rounded-full blur-3xl"></div>
      <div class="absolute bottom-0 right-0 h-96 w-96 bg-cyan-400/10 rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-10 items-center">
      {{-- Texto --}}
      <div class="space-y-6 animate-[fadeIn_0.8s_ease]">
        <h1 class="text-4xl md:text-5xl font-semibold leading-tight text-white">
          Plataforma Integral de <span class="text-cyan-400">Evaluación y Auditoría Educativa</span>
        </h1>
        <p class="text-neutral-300 text-lg">
          Impulsando la calidad educativa mediante la gestión digital de procesos de
          <span class="text-cyan-300 font-medium">evaluación, satisfacción, impacto y auditorías institucionales</span>.
        </p>
        <div class="flex flex-wrap gap-4 pt-2">
          <a href="#modulos" class="px-6 py-3 bg-cyan-500 hover:bg-cyan-600 text-black font-semibold rounded-lg transition">
            Explorar Módulos
          </a>
          <a href="#about" class="px-6 py-3 border border-cyan-400 text-cyan-300 rounded-lg hover:bg-cyan-400/10 transition">
            Conoce INCADEV
          </a>
        </div>
      </div>

      {{-- Imagen --}}
      <div class="grid place-items-center animate-[riseIn_0.9s_ease]">
        <img src="{{ asset('images/dashboard-preview.png') }}"
             alt="INCADEV Dashboard"
             class="w-[90%] max-w-lg rounded-2xl border border-cyan-400/20 shadow-[0_0_50px_rgba(38,187,255,0.25)]" />
      </div>
    </div>
  </section>

  {{-- ===== SOBRE NOSOTROS ===== --}}
  <section id="about" class="py-20 bg-[#141426] border-t border-cyan-500/10">
    <div class="max-w-6xl mx-auto px-6 text-center space-y-8">
      <h2 class="text-3xl font-bold text-cyan-300 mb-6">Sobre INCADEV</h2>
      <p class="text-neutral-300 text-lg max-w-3xl mx-auto">
        El <span class="text-cyan-300 font-semibold">Instituto de Capacitación y Desarrollo Virtual (INCADEV)</span> promueve la excelencia educativa a través de la tecnología,
        ofreciendo herramientas digitales que permiten gestionar y mejorar los procesos de evaluación, auditoría y satisfacción estudiantil.
      </p>
      <div class="grid md:grid-cols-3 gap-8 mt-12 text-left">
        <div class="bg-[#0f0f1c]/80 border border-cyan-400/20 rounded-2xl p-6 hover:shadow-[0_0_24px_rgba(38,187,255,0.15)] transition">
          <i class="fa-solid fa-bullseye text-cyan-400 text-3xl mb-4"></i>
          <h3 class="text-xl font-semibold mb-2">Misión</h3>
          <p class="text-neutral-400 text-sm">Fortalecer la calidad educativa mediante sistemas digitales que impulsen la mejora continua y la transparencia institucional.</p>
        </div>

        <div class="bg-[#0f0f1c]/80 border border-cyan-400/20 rounded-2xl p-6 hover:shadow-[0_0_24px_rgba(38,187,255,0.15)] transition">
          <i class="fa-solid fa-eye text-cyan-400 text-3xl mb-4"></i>
          <h3 class="text-xl font-semibold mb-2">Visión</h3>
          <p class="text-neutral-400 text-sm">Ser una plataforma líder en innovación educativa, promoviendo procesos institucionales transparentes, digitales y sostenibles.</p>
        </div>

        <div class="bg-[#0f0f1c]/80 border border-cyan-400/20 rounded-2xl p-6 hover:shadow-[0_0_24px_rgba(38,187,255,0.15)] transition">
          <i class="fa-solid fa-users text-cyan-400 text-3xl mb-4"></i>
          <h3 class="text-xl font-semibold mb-2">Valores</h3>
          <p class="text-neutral-400 text-sm">Innovación, compromiso, transparencia y sostenibilidad educativa, pilares de cada módulo desarrollado por INCADEV.</p>
        </div>
      </div>
    </div>
  </section>

  {{-- ===== MÓDULOS ===== --}}
  <section id="modulos" class="py-24 max-w-7xl mx-auto px-6">
    <h2 class="text-3xl font-bold text-center text-cyan-300 mb-12">Nuestros Módulos</h2>
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
      {{-- Satisfacción Estudiantil --}}
      <x-modulo-card
        title="Satisfacción Estudiantil"
        description="Encuestas automáticas, análisis y tabulación de la experiencia académica estudiantil."
        image="images/avatar-students.png"
        route="{{ route('satisfaccion.welcome') }}"
      />
      {{-- Evaluación Docente --}}
      <x-modulo-card
        title="Evaluación Docente"
        description="Sistema digital para evaluar el desempeño docente con métricas automatizadas."
        image="images/avatar-teacher.png"
        route="{{ route('evaluacion.welcome') }}"
      />
      {{-- Impacto --}}
      <x-modulo-card
        title="Impacto y Egresados"
        description="Seguimiento de egresados, empleabilidad y resultados institucionales."
        image="images/avatar-impact.png"
        route="{{ route('impacto.welcome') }}"
      />
      {{-- Auditoría --}}
      <x-modulo-card
        title="Auditoría Académica"
        description="Gestión y monitoreo digital de auditorías académicas y administrativas."
        image="images/avatar-audit.png"
        route="{{ route('auditoria.auth.login') }}"
      />
    </div>
  </section>

  {{-- ===== CONTACTO ===== --}}
  <section id="contact" class="py-20 bg-[#141426] border-t border-cyan-400/10">
    <div class="max-w-5xl mx-auto px-6 text-center space-y-10">
      <h2 class="text-3xl font-bold text-cyan-300">Contáctanos</h2>
      <p class="text-neutral-300 max-w-2xl mx-auto">
        ¿Tienes dudas o sugerencias? Escríbenos y nos pondremos en contacto contigo.
      </p>

      <form method="POST" action="#" class="grid md:grid-cols-2 gap-6 text-left">
        @csrf
        <input type="text" name="name" placeholder="Tu nombre"
               class="p-3 rounded bg-[#0f0f1c]/70 border border-cyan-400/20 focus:border-cyan-400 outline-none transition">
        <input type="email" name="email" placeholder="Tu correo"
               class="p-3 rounded bg-[#0f0f1c]/70 border border-cyan-400/20 focus:border-cyan-400 outline-none transition">
        <textarea name="message" rows="4" placeholder="Tu mensaje"
                  class="md:col-span-2 p-3 rounded bg-[#0f0f1c]/70 border border-cyan-400/20 focus:border-cyan-400 outline-none transition"></textarea>
        <button type="submit"
                class="md:col-span-2 w-full bg-cyan-500 hover:bg-cyan-600 text-black font-semibold py-3 rounded-lg transition">
          Enviar Mensaje
        </button>
      </form>

      <div class="flex justify-center gap-6 mt-6">
        <a href="#" class="text-cyan-400 hover:text-cyan-300 text-2xl"><i class="fab fa-facebook"></i></a>
        <a href="#" class="text-cyan-400 hover:text-cyan-300 text-2xl"><i class="fab fa-twitter"></i></a>
        <a href="#" class="text-cyan-400 hover:text-cyan-300 text-2xl"><i class="fab fa-instagram"></i></a>
        <a href="#" class="text-cyan-400 hover:text-cyan-300 text-2xl"><i class="fab fa-linkedin"></i></a>
      </div>
    </div>
  </section>

  {{-- ===== FOOTER ===== --}}
  <footer class="mt-auto border-t border-cyan-400/20 bg-[#0f0f1c]/90 backdrop-blur py-8">
    <div class="max-w-7xl mx-auto px-6 text-center text-neutral-400 text-sm">
      <p>© {{ date('Y') }} <span class="text-cyan-300 font-medium">INCADEV</span>. Todos los derechos reservados.</p>
      <p class="text-xs mt-1">Desarrollado por el Grupo 06 — Procesos de Evaluación y Mejora</p>
    </div>
  </footer>

  {{-- Animaciones --}}
  <style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes riseIn { from { opacity: 0; transform: translateY(32px) scale(.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
  </style>
</body>
</html>
