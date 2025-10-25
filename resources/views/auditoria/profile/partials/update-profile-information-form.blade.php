@php
  /** @var \App\Models\User $user */
  $user = auth()->user();
  $avatar = $user?->profile_photo ? asset('storage/'.$user->profile_photo) : asset('images/avatar-students.png');
@endphp

<section x-data="{ photoName: null, photoPreview: '{{ $avatar }}' }">
  <header class="mb-4">
    <h2 class="text-lg font-semibold text-neutral-100">Información del perfil</h2>
    <p class="text-sm text-neutral-400">Actualiza tu nombre, correo y otros datos básicos.</p>
  </header>

  {{-- Estado --}}
  @if (session('status') === 'profile-updated')
    <div class="mb-4 rounded-xl border border-green-400/30 bg-green-500/10 text-green-300 px-4 py-2">
      Cambios guardados.
    </div>
  @endif

  <form method="post" action="{{ route('auditoria.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('patch')

    {{-- Avatar --}}
    <div class="flex items-center gap-4">
      <img :src="photoPreview" alt="Avatar" class="h-16 w-16 rounded-full border border-ink-400/30 object-cover">
      <div class="space-x-2">
        <label
          for="profile_photo"
          class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5 bg-ink-800/70 border border-ink-400/20 text-neutral-100 hover:bg-ink-700 cursor-pointer"
        >
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-width="1.8" d="M12 5l4 4H8l4-4zM6 10h12v9H6z"/>
          </svg>
          Cambiar foto
        </label>
        <input id="profile_photo" name="profile_photo" type="file" class="hidden" accept="image/*"
               @change="
                 photoName = $event.target.files[0]?.name;
                 const reader = new FileReader();
                 reader.onload = e => photoPreview = e.target.result;
                 reader.readAsDataURL($event.target.files[0]);
               ">
        @if($user->profile_photo)
          <button type="button"
                  class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5 bg-ink-800/70 border border-ink-400/20 text-neutral-100 hover:bg-ink-700"
                  onclick="document.getElementById('remove_photo').value='1'; this.closest('form').submit()">
            Quitar foto
          </button>
          <input type="hidden" id="remove_photo" name="remove_photo" value="0">
        @endif
        <x-input-error :messages="$errors->get('profile_photo')" class="mt-2" />
      </div>
    </div>

    {{-- Nombres --}}
    <div class="grid sm:grid-cols-2 gap-4">
      <x-ui.input name="first_name" label="Nombres" :value="old('first_name',$user->first_name)" required />
      <x-ui.input name="last_name" label="Apellidos" :value="old('last_name',$user->last_name)" required />
    </div>

    {{-- Email & Teléfono --}}
    <div class="grid sm:grid-cols-2 gap-4">
      <x-ui.input name="email" type="email" label="Correo" :value="old('email',$user->email)" required autocomplete="username" />
      <x-ui.input name="phone_number" label="Teléfono" :value="old('phone_number',$user->phone_number)" />
    </div>

    {{-- Dirección --}}
    <x-ui.input name="address" label="Dirección" :value="old('address',$user->address)" />

    {{-- País / Ubicación / Zona horaria --}}
    <div class="grid sm:grid-cols-3 gap-4">
      <x-ui.input name="country" label="País" :value="old('country',$user->country)" />
      <x-ui.input name="country_location" label="Ciudad/Región" :value="old('country_location',$user->country_location)" />
      <div>
        <label class="block text-sm font-medium text-neutral-200 mb-1">Zona horaria</label>
        <select name="timezone"
                class="w-full h-10 rounded-xl bg-ink-800/70 border border-ink-400/30 text-neutral-100 focus:ring-brand-300 focus:border-brand-400">
          @foreach(timezone_identifiers_list() as $tz)
            <option value="{{ $tz }}" @selected(old('timezone',$user->timezone)===$tz)>{{ $tz }}</option>
          @endforeach
        </select>
        <x-input-error :messages="$errors->get('timezone')" class="mt-2" />
      </div>
    </div>

    {{-- Fecha de nacimiento / Género --}}
    <div class="grid sm:grid-cols-2 gap-4">
      <x-ui.input name="birth_date" type="date" label="Fecha de nacimiento"
                  :value="old('birth_date', optional($user->birth_date)->format('Y-m-d'))" />
      <div>
        <label class="block text-sm font-medium text-neutral-200 mb-1">Género</label>
        <select name="gender"
                class="w-full h-10 rounded-xl bg-ink-800/70 border border-ink-400/30 text-neutral-100">
          @php $g = old('gender',$user->gender) @endphp
          <option value="">—</option>
          <option value="male"   @selected($g==='male')>Masculino</option>
          <option value="female" @selected($g==='female')>Femenino</option>
          <option value="other"  @selected($g==='other')>Otro</option>
        </select>
        <x-input-error :messages="$errors->get('gender')" class="mt-2" />
      </div>
    </div>

    {{-- Verificación de email --}}
    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
      <div class="rounded-xl bg-warning-500/10 border border-warning-400/20 text-yellow-200 px-4 py-3">
        Tu correo no está verificado.
        <button form="send-verification" class="underline underline-offset-2 hover:text-yellow-100">
          Reenviar verificación
        </button>
      </div>
      <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
      </form>
    @endif

    {{-- Botones --}}
    <div class="flex items-center gap-3">
      <x-ui.button type="submit" variant="primary">Guardar cambios</x-ui.button>
      @if (session('status') === 'profile-updated')
        <span class="text-sm text-neutral-400">Guardado.</span>
      @endif
    </div>
  </form>
</section>
