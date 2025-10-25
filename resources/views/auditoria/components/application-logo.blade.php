@props([
  'preset' => 'nav', // nav | login | default
])

@php
  $src = asset('images/incadev-logo.png');
@endphp

@if($preset === 'nav')
  {{-- LOGO PARA NAV / SIDEBAR: IMG directo (sin clip/SVG) para máxima nitidez --}}
  <img
    src="{{ $src }}"
    alt="INCADEV"
    {{ $attributes->merge([
      'class' => 'h-8 w-8 shrink-0 rounded-full object-cover ring-1 ring-ink-400/30'
    ]) }}
    decoding="async"
    fetchpriority="high"
  />
@elseif($preset === 'login')
  {{-- LOGO LOGIN: si te gusta la máscara circular con SVG, lo dejamos aquí --}}
  <svg
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 500 500"
    {{ $attributes->merge([
      'class' => 'h-16 w-16 md:h-20 md:w-20 mx-auto drop-shadow-xl rounded-full overflow-hidden'
    ]) }}
  >
    <defs>
      <clipPath id="circleMask">
        <circle cx="250" cy="250" r="240" />
      </clipPath>
    </defs>
    <image href="{{ $src }}" width="500" height="500" preserveAspectRatio="xMidYMid slice" clip-path="url(#circleMask)"/>
  </svg>
@else
  {{-- fallback sencillo --}}
  <img
    src="{{ $src }}"
    alt="INCADEV"
    {{ $attributes->merge([
      'class' => 'h-10 w-10 rounded-full object-cover ring-1 ring-ink-400/30'
    ]) }}
  />
@endif
