@props([
  'href' => '#',
  'active' => false,
  'danger' => false,
])

<a href="{{ $href }}"
   {{ $attributes->class([
      'block px-3 py-2 text-sm rounded-lg transition-colors',
      $danger
        ? 'text-danger-500 hover:bg-danger-500/10'
        : ($active ? 'bg-ink-600 text-neutral-100' : 'text-neutral-100 hover:bg-ink-600/70')
   ]) }}>
  {{ $slot }}
</a>
