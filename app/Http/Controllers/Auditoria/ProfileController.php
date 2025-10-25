<?php

namespace App\Http\Controllers\Auditoria;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('auditoria.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'first_name'      => ['required','string','max:80'],
            'last_name'       => ['required','string','max:80'],
            'email'           => ['required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'phone_number'    => ['nullable','string','max:30'],
            'address'         => ['nullable','string','max:255'],
            'birth_date'      => ['nullable','date'],
            'gender'          => ['nullable', Rule::in(['male','female','other'])],
            'country'         => ['nullable','string','max:80'],
            'country_location'=> ['nullable','string','max:120'],
            'timezone'        => ['nullable','string','max:80'],
            'profile_photo'   => ['nullable','image','max:2048'],
            'remove_photo'    => ['nullable','in:0,1'],
        ]);

        // Foto de perfil
        if (($data['remove_photo'] ?? '0') === '1') {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
                $user->profile_photo = null;
            }
        } elseif ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $path = $request->file('profile_photo')->store('avatars', 'public');
            $user->profile_photo = $path;
        }

        // Datos básicos
        $user->first_name       = $data['first_name'];
        $user->last_name        = $data['last_name'];
        $user->full_name        = trim($data['first_name'].' '.$data['last_name']);
        $user->email            = $data['email'];
        $user->phone_number     = $data['phone_number'] ?? null;
        $user->address          = $data['address'] ?? null;
        $user->birth_date       = $data['birth_date'] ?? null;
        $user->gender           = $data['gender'] ?? null;
        $user->country          = $data['country'] ?? null;
        $user->country_location = $data['country_location'] ?? null;
        $user->timezone         = $data['timezone'] ?? null;

        $user->save();

        return Redirect::route('auditoria.profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/auditoria/login');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password'      => ['required', 'current_password'],
            'password'              => ['required', 'confirmed', 'min:8'],
        ]);

        $user = $request->user();
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return Redirect::route('auditoria.profile.password.update')->with('status', 'password-updated');
    }

}