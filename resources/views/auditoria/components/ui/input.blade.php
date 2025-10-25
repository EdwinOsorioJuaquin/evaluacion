@props([
  'id' => null,
  'name' => null,
  'type' => 'text',
  'label' => null,
  'hint' => null,
  'placeholder' => null,
  'value' => null,
  'autocomplete' => null,
  'required' => false,
  'autofocus' => false,
  'disabled' => false,
  'readonly' => false,

  // Toggle para password con Alpine
  'toggle' => false,

  // Errores (array|string|null)
  'messages' => [],
])

@php
  use Illuminate\Support\Str;
  $id = $id ?: Str::uuid();

  // Normaliza errores
  $errorsList = is_array($messages) ? $messages : (empty($messages) ? [] : [$messages]);
  $errorsList = array_values(array_filter($errorsList, fn($m) => filled($m)));
  $hasError = count($errorsList) > 0;

  // ¿Hay prefix/suffix via slot? (en componentes anónimos, los named slots se exponen como variables)
  $hasPrefix = isset($prefix) && filled($prefix);
  $hasSuffix = isset($suffix) && filled($suffix);

  $classes = collect([
    'block w-full rounded-xl',
    'bg-ink-600 text-neutral-100 placeholder-neutral-400',
    'border border-ink-400/30',
    'focus:border-brand-400 focus:ring-2 focus:ring-brand-300',
    'px-3 py-2',
    $hasPrefix ? 'pl-10' : '',
    ($hasSuffix || $toggle) ? 'pr-10' : '',
    $disabled ? 'opacity-60 cursor-not-allowed' : '',
    $readonly ? 'opacity-75' : '',
    $hasError ? 'border-danger-500 focus:ring-danger-500/30 focus:border-danger-500' : '',
  ])->filter()->implode(' ');
@endphp

<div {{ $attributes->class('w-full') }} x-data="{ show:false }">
  {{-- Label --}}
  @if($label)
    <label for="{{ $id }}" class="block text-sm font-medium text-neutral-200 mb-1.5">
      {{ $label }} @if($required)<span class="text-danger-500">*</span>@endif
    </label>
  @endif

  {{-- Campo con adornos --}}
  <div class="relative">
    {{-- Prefix (slot) --}}
    @isset($prefix)
      <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-neutral-400 pointer-events-none">
        {{ $prefix }}
      </span>
    @endisset

    {{-- Input --}}
    <input
      id="{{ $id }}"
      name="{{ $name }}"
      @if($toggle && $type === 'password')
        :type="show ? 'text' : 'password'"
      @else
        type="{{ $type }}"
      @endif
      @if(!is_null($value)) value="{{ $value }}" @endif
      @if($placeholder) placeholder="{{ $placeholder }}" @endif
      @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
      @if($required) required @endif
      @if($autofocus) autofocus @endif
      @if($disabled) disabled @endif
      @if($readonly) readonly @endif
      class="{{ $classes }}"
    />

    {{-- Suffix (toggle password o slot) --}}
    @if($toggle && $type === 'password')
      <button type="button"
              class="absolute inset-y-0 right-2.5 flex items-center px-2 text-neutral-400 hover:text-brand-300 transition"
              x-on:click="show=!show"
              x-bind:aria-pressed="show.toString()"
              aria-label="Mostrar u ocultar contraseña">
        <template x-if="!show">
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M2.458 12C3.73 7.94 7.52 5 12 5s8.27 2.94 9.54 7c-1.27 4.06-5.06 7-9.54 7S3.73 16.06 2.46 12z"/>
            <circle cx="12" cy="12" r="3"/>
          </svg>
        </template>
        <template x-if="show">
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M3 3l18 18M10.58 10.58A3 3 0 0113.42 13.42M9.88 4.21A10 10 0 0112 4c4.48 0 8.27 2.94 9.54 7a11.1 11.1 0 01-1.57 3.04M6.47 6.47C4.55 7.77 3.23 9.71 2.46 12c1.27 4.06 5.06 7 9.54 7 1.03 0 2.02-.16 2.95-.47"/>
          </svg>
        </template>
      </button>
    @elseif($hasSuffix)
      <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-neutral-400">
        {{ $suffix }}
      </span>
    @endif
  </div>

  {{-- Hint / Errores --}}
  @if($hint && !$hasError)
    <p class="mt-1 text-xs text-neutral-400">{{ $hint }}</p>
  @endif

  @if($hasError)
    <ul class="mt-2 text-xs text-danger-500 space-y-1">
      @foreach ($errorsList as $message)
        <li>{{ $message }}</li>
      @endforeach
    </ul>
  @endif
</div>
