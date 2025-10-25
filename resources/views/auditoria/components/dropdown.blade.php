{{-- resources/views/components/ui/dropdown.blade.php --}}
@props([
  'align' => 'right',                 // left | right
  'width' => '48',                    // 40|44|48|56|64|72|auto (o clases custom)
  'contentClasses' => 'bg-ink-700 border border-ink-400/20 shadow-soft py-2 rounded-xl',
  'asButton' => true,                 // false si pasas trigger custom
  'teleport' => true,                 // render en <body> para evitar clipping
  'matchWidth' => false,              // si true, panel = ancho del trigger
  'offset' => 8,                      // separación vertical en px
  'closeOnItemClick' => true,         // cierra al click en items internos
])

@php
  use Illuminate\Support\Str;
  $uid = Str::uuid();

  $widthClass = match ($width) {
      'auto' => 'w-auto',
      '40' => 'w-40', '44' => 'w-44', '48' => 'w-48',
      '56' => 'w-56', '64' => 'w-64', '72' => 'w-72',
      default => (is_numeric($width) ? "w-[{$width}px]" : $width),
  };

  $triggerBase = 'inline-flex items-center gap-2 rounded-xl px-3 py-2
                  bg-ink-600 hover:bg-ink-500 text-neutral-100
                  focus:outline-none focus:ring-2 focus:ring-ink-400';
@endphp

<div x-data="dropdown('{{$align}}', {{$offset}}, {{$matchWidth ? 'true':'false'}}, {{$closeOnItemClick ? 'true':'false'}})"
     class="relative inline-block text-left">

  {{-- Trigger --}}
  <div>
    @if ($asButton)
      <button type="button"
              x-ref="trigger"
              :id="triggerId"
              x-on:click="toggle"
              :aria-expanded="open.toString()"
              :aria-controls="panelId"
              aria-haspopup="menu"
              {{ $attributes->merge(['class' => $triggerBase]) }}>
        {{ $trigger ?? $slot }}
        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
          <path d="M5.25 7.5l4.5 4.5 4.5-4.5"/>
        </svg>
      </button>
    @else
      <button type="button"
              x-ref="trigger"
              :id="triggerId"
              x-on:click="toggle"
              :aria-expanded="open.toString()"
              :aria-controls="panelId"
              aria-haspopup="menu"
              class="inline-flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-brand-300">
        {{ $trigger ?? $slot }}
      </button>
    @endif
  </div>

  {{-- Panel --}}
  <template x-if="open">
    @if($teleport)
      <div x-teleport="body">
        <div x-ref="panel"
             :id="panelId"
             role="menu" aria-orientation="vertical"
             class="z-[10000] {{ $matchWidth ? '' : $widthClass }}"
             :style="panelStyle"
             x-on:click.outside="close"
             x-transition.opacity.duration.120ms>
          <div class="{{ $contentClasses }}"
               @click.stop
               :data-close-on-click="closeOnItemClick ? '1' : '0'"
               x-init="
                 // delega cierre si se hace click en un <a> o <button> interno
                 $el.addEventListener('click', (e) => {
                   if ($el.dataset.closeOnClick !== '1') return;
                   const tag = (e.target.closest('a,button') || {}).tagName || '';
                   if (tag) close();
                 }, { capture: true });
               "
               style="max-height: min(70vh, 560px); overflow:auto;">
            {{ $content ?? '' }}
          </div>
        </div>
      </div>
    @else
      <div x-cloak x-show="open"
           :id="panelId"
           role="menu" aria-orientation="vertical"
           class="absolute mt-2 {{ $widthClass }} z-50"
           x-on:click.outside="close"
           x-transition.opacity.duration.120ms
           style="max-height: min(70vh, 560px); overflow:auto;">
        <div class="{{ $contentClasses }}" @click.stop>
          {{ $content ?? '' }}
        </div>
      </div>
    @endif
  </template>
</div>

<script>
  // Alpine dropdown utility
  function dropdown(align = 'right', offset = 8, matchWidth = false, closeOnItemClick = true) {
    return {
      open: false,
      panelStyle: '',
      triggerId: 'dd-trigger-{{ $uid }}',
      panelId: 'dd-panel-{{ $uid }}',
      align,
      offset,
      matchWidth,
      closeOnItemClick,

      toggle(){ this.open ? this.close() : this.openMenu() },
      close(){ this.open = false },

      openMenu(){
        this.open = true;
        this.$nextTick(() => {
          this.position();
          // Listeners para re-posicionar
          window.addEventListener('resize', this._onResize = () => this.position(), { passive: true });
          window.addEventListener('scroll', this._onScroll = () => this.position(), { passive: true });
        });
      },

      position(){
        const t = this.$refs.trigger.getBoundingClientRect();
        const p = this.$refs.panel;

        // Position fixed (teleport) para no “bailar” con scroll interno
        p.style.position = 'fixed';
        p.style.visibility = 'hidden'; // evita flicker al medir
        p.style.left = '0px'; p.style.top = '0px';
        p.style.width = ''; // reset para recalcular

        // ancho opcional
        if (this.matchWidth) p.style.width = `${t.width}px`;

        // tamaño del panel (tras aplicar width)
        const desiredWidth = this.matchWidth ? t.width : (p.offsetWidth || t.width);
        let left = (this.align === 'left') ? t.left : (t.right - desiredWidth);
        let top  = t.bottom + this.offset;

        // viewport bounds
        const vw = window.innerWidth;
        const vh = window.innerHeight;

        // Flip horizontal si se sale a la derecha/izquierda
        if (left + desiredWidth > vw - 8) { // 8px padding visual
          left = vw - desiredWidth - 8;
        }
        if (left < 8) { left = 8; }

        // Flip vertical si se sale por abajo (lo pone arriba del trigger)
        const panelHeight = p.offsetHeight;
        if (top + panelHeight > vh - 8) {
          const upTop = t.top - this.offset - panelHeight;
          if (upTop >= 8) top = upTop;
        }

        // aplica estilos finales
        p.style.left = `${left}px`;
        p.style.top = `${top}px`;
        p.style.visibility = 'visible';

        // foco accesible
        const focusable = p.querySelector('[href],button,input,select,textarea,[tabindex]:not([tabindex="-1"])');
        focusable?.focus({preventScroll:true});
      },

      // Limpieza de listeners
      init(){
        this.$watch('open', (v) => {
          if(!v){
            window.removeEventListener('resize', this._onResize || (()=>{}));
            window.removeEventListener('scroll', this._onScroll || (()=>{}));
          }
        });
        // ESC cierre
        document.addEventListener('keydown', (e) => {
          if(e.key === 'Escape') this.close();
        });
      }
    }
  }
</script>
