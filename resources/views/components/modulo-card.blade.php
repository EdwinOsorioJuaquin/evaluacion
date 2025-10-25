@props(['title', 'description', 'image', 'route'])

<article class="rounded-2xl bg-[#141426]/90 border border-cyan-400/20 p-6 hover:shadow-[0_0_24px_rgba(38,187,255,0.18)] transition">
  <div class="flex items-center gap-4 mb-3">
    <img src="{{ asset($image) }}" alt="{{ $title }}" class="h-14 w-14 rounded-full border border-cyan-400/40" />
    <h3 class="text-lg font-semibold text-neutral-50">{{ $title }}</h3>
  </div>
  <p class="text-neutral-300 text-sm mb-4">{{ $description }}</p>
  <a href="{{ $route }}"
     class="inline-block px-4 py-2 bg-cyan-500 hover:bg-cyan-600 text-black rounded-md text-sm font-medium transition">
    Ingresar
  </a>
</article>
