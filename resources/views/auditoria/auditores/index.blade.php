<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-semibold text-xl text-neutral-100 leading-tight">Auditores & Administradores</h2>
        <p class="text-sm text-neutral-400 mt-1">Gestiona cuentas con rol de acceso.</p>
      </div>
      <x-ui.button as="a" href="{{ route('auditoria.auditores.create') }}" variant="primary" class="min-w-36">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-width="1.8" d="M12 5v14M5 12h14"/>
        </svg>
        Nuevo usuario
      </x-ui.button>
    </div>
  </x-slot>

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="rounded-2xl bg-ink-700/80 border border-ink-400/20 shadow-soft p-6 space-y-6">

        {{-- Filtros --}}
        <form method="GET" action="{{ route('auditoria.auditores.index') }}" class="grid sm:grid-cols-3 gap-3">
          <div class="sm:col-span-2">
            <x-ui.input
              name="q"
              :value="$q"
              placeholder="Buscar por nombre o correo…"
              label="Buscar"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-neutral-200 mb-1">Estado</label>
            <select name="status"
                    class="w-full h-10 rounded-xl bg-ink-800/70 border border-ink-400/30 text-neutral-100 focus:ring-brand-300 focus:border-brand-400">
              <option value="active"   {{ $status==='active'?'selected':'' }}>Activos</option>
              <option value="inactive" {{ $status==='inactive'?'selected':'' }}>Inactivos</option>
              <option value="all"      {{ $status==='all'?'selected':'' }}>Todos</option>
            </select>
          </div>
        </form>

        {{-- Tabla --}}
        <div class="overflow-x-auto rounded-xl border border-ink-400/20">
          <table class="min-w-full text-sm">
            <thead class="bg-ink-800/70 text-neutral-300">
              <tr>
                <th class="text-left px-4 py-3 font-medium">Usuario</th>
                <th class="text-left px-4 py-3 font-medium">Correo</th>
                <th class="text-left px-4 py-3 font-medium">Teléfono</th>
                <th class="text-left px-4 py-3 font-medium">Rol</th>
                <th class="text-left px-4 py-3 font-medium">Estado</th>
                <th class="text-right px-4 py-3 font-medium">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-ink-400/20">
              @forelse($users as $u)
                <tr class="hover:bg-ink-800/40">
                  <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                      <img src="{{ $u->profile_photo ? asset('storage/'.$u->profile_photo) : asset('images/avatar-students.png') }}"
                           class="h-8 w-8 rounded-full border border-ink-400/30 object-cover" alt="">
                      <div>
                        <div class="text-neutral-100 font-medium">{{ $u->first_name }} {{ $u->last_name }}</div>
                        <div class="text-[11px] text-neutral-400">ID: {{ $u->id }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-4 py-3 text-neutral-200">{{ $u->email }}</td>
                  <td class="px-4 py-3 text-neutral-200">{{ $u->phone_number ?? '—' }}</td>
                  <td class="px-4 py-3">
                    @php $r = is_array($u->role) ? $u->role[0] ?? '' : $u->role; @endphp
                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs
                                 {{ $r==='admin' ? 'bg-brand-500/15 text-brand-300 border border-brand-400/20' : 'bg-ink-600 text-neutral-200 border border-ink-400/30' }}">
                      <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-width="1.6" d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/>
                        <path stroke-linecap="round" stroke-width="1.6" d="M12 7v5l3 3"/>
                      </svg>
                      {{ $r === 'admin' ? 'Admin' : 'Auditor' }}
                    </span>
                  </td>
                  <td class="px-4 py-3">
                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs
                                 {{ $u->status==='active' ? 'bg-green-500/15 text-green-300 border border-green-400/20' : 'bg-warning-500/15 text-yellow-300 border border-yellow-400/20' }}">
                      <span class="h-1.5 w-1.5 rounded-full
                                   {{ $u->status==='active' ? 'bg-green-400' : 'bg-yellow-400' }}"></span>
                      {{ $u->status==='active' ? 'Activo' : 'Inactivo' }}
                    </span>
                  </td>
                  <td class="px-4 py-3">
                    <div class="flex items-center justify-end gap-2">
                      <x-ui.button as="a" href="{{ route('auditoria.auditores.edit',$u) }}" size="sm" variant="secondary">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                          <path stroke-linecap="round" stroke-width="1.8" d="M4 21h4l11-11a2.828 2.828 0 10-4-4L4 17v4z"/>
                        </svg>
                        <span class="sr-only">Editar</span>
                      </x-ui.button>

                      <form action="{{ route('auditoria.auditores.toggle',$u) }}" method="POST" onsubmit="return confirm('¿Cambiar estado?')">
                        @csrf
                        <x-ui.button type="submit" size="sm" :variant="$u->status==='active' ? 'warning' : 'success'">
                          @if($u->status==='active')
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                              <path stroke-linecap="round" stroke-width="1.8" d="M18 12H6"/>
                            </svg>
                            <span class="hidden sm:inline">Inactivar</span>
                          @else
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                              <path stroke-linecap="round" stroke-width="1.8" d="M12 6v12M6 12h12"/>
                            </svg>
                            <span class="hidden sm:inline">Activar</span>
                          @endif
                        </x-ui.button>
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="px-4 py-10 text-center text-neutral-400">Sin resultados.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div>
          {{ $users->links() }}
        </div>

      </div>
    </div>
  </div>
</x-app-layout>
