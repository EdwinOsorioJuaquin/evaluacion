@props(['status'])

@if ($status)
  <div {{ $attributes->class('rounded-xl border border-brand-400/30 bg-brand-500/10 text-brand-300 px-4 py-3') }}>
    {{ $status }}
  </div>
@endif
