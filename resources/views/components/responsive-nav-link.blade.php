@props(['href' => '#', 'active' => false])

<a href="{{ $href }}"
   {{ $attributes->class([
     'block w-full text-left px-3 py-2 rounded-lg text-sm',
     $active ? 'bg-ink-600 text-neutral-100' : 'text-neutral-300 hover:bg-ink-600/60 hover:text-neutral-100',
   ]) }}>
  {{ $slot }}
</a>
