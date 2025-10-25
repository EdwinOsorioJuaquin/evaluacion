@props(['show' => false, 'maxWidth' => '2xl']) {{-- md|lg|xl|2xl --}}

<div
  x-data="{ open: @js($show) }"
  x-show="open"
  x-on:keydown.escape.window="open=false"
  class="fixed inset-0 z-50"
  aria-modal="true"
  role="dialog"
>
  <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" x-show="open" x-transition.opacity></div>

  <div class="flex items-center justify-center min-h-full p-4">
    <div
      x-show="open"
      x-transition
      class="w-full max-w-{{ $maxWidth }} rounded-2xl bg-ink-700 border border-ink-400/20 shadow-soft"
    >
      <div class="p-6">
        {{ $slot }}
      </div>
    </div>
  </div>
</div>
