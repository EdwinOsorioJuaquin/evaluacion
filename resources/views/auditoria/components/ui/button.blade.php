@props([
  'variant' => 'primary',   // primary | secondary | ghost | danger
  'size' => 'md',           // sm | md | lg
  'icon' => false,          // cuadrado para solo ícono
  'full' => false,          // w-full
  'loading' => false,

  // Navegación / semántica
  'href' => null,           // si viene => renderiza <a>
  'as' => null,             // forzar 'a' o 'button' si quieres
  'type' => 'button',       // submit|button|reset (cuando es <button>)
  'target' => null,         // _blank etc (cuando es <a>)
  'rel' => null,            // noopener etc (cuando es <a>)
  'form' => null,           // para <button>
])

@php
  // Clases base
  $base = 'inline-flex items-center justify-center gap-2 font-medium rounded-2xl
           transition-colors shadow-soft focus:outline-none focus:ring-2
           disabled:opacity-50 disabled:pointer-events-none';

  $sizes = [
    'sm' => 'text-sm px-3 py-1.5',
    'md' => 'text-sm px-4 py-2',
    'lg' => 'text-base px-5 py-2.5',
  ][$size] ?? 'text-sm px-4 py-2';

  if ($icon) { $sizes = 'h-10 w-10 p-0'; }

  $variants = [
    'primary'   => 'bg-brand-500 hover:bg-brand-400 active:bg-brand-600 text-black focus:ring-brand-300',
    'secondary' => 'bg-ink-600 hover:bg-ink-500 active:bg-ink-400 text-neutral-100 border border-ink-400/30 focus:ring-ink-400',
    'ghost'     => 'bg-transparent hover:bg-ink-600/60 text-neutral-100 border border-transparent focus:ring-ink-400',
    'danger'    => 'bg-danger-500 hover:bg-danger-700 text-white focus:ring-danger-500/40',
  ][$variant] ?? 'bg-brand-500 hover:bg-brand-400 active:bg-brand-600 text-black focus:ring-brand-300';

  $width = $full ? 'w-full' : '';

  // ¿Qué tag usar?
  $tag = $as ?? ($href ? 'a' : 'button');

  // Clases finales
  $classes = trim("$base $sizes $variants $width");

@endphp

@if ($tag === 'a')
  <a href="{{ $href }}"
     @if($target) target="{{ $target }}" @endif
     @if($rel) rel="{{ $rel }}" @endif
     {{ $attributes->merge(['class' => $classes, 'role' => 'button']) }}>
    @if($loading)
      <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
      </svg>
    @endif
    {{ $slot }}
  </a>
@else
  <button type="{{ $type }}" @if($form) form="{{ $form }}" @endif
          {{ $attributes->merge(['class' => $classes]) }}>
    @if($loading)
      <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
      </svg>
    @endif
    {{ $slot }}
  </button>
@endif
