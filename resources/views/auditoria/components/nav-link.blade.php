@props(['href' => '#', 'active' => false])

<a href="{{ $href }}"
   {{ $attributes->class([
     'inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm transition-colors',
     $active
       ? 'bg-ink-600 text-neutral-100'
       : 'text-neutral-300 hover:text-neutral-100 hover:bg-ink-600/60'
   ]) }}>
  {{ $slot }}
</a>
