@props([
  'messages' => [], // puede venir como string|array|null
])

@php
  // Normalizamos a array de strings y filtramos vacíos
  $items = is_array($messages) ? $messages : (empty($messages) ? [] : [$messages]);
  $items = array_filter($items, fn($m) => filled($m));
@endphp

@if ($items)
  <ul {{ $attributes->merge(['class' => 'mt-2 text-xs text-danger-500 space-y-1']) }}>
    @foreach ($items as $message)
      <li>{{ $message }}</li>
    @endforeach
  </ul>
@endif
