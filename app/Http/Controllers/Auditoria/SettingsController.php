<?php

// app/Http/Controllers/SettingsController.php
namespace App\Http\Controllers\Auditoria;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $prefs = (object)[
            'theme_mode' => $user->preferences['theme_mode'] ?? 'dark',
        ];
        $notifications = (object)($user->preferences['notifications'] ?? []);
        return view('auditoria.settings.index', compact('user','prefs','notifications'));
    }

    /** ===================== PERFIL ===================== */
 /** ===================== PERFIL ===================== */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:80'],
            'last_name'  => ['required', 'string', 'max:120'],
            'email'      => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone_number' => ['nullable', 'string', 'max:40'],
            'country'    => ['nullable', 'string', 'max:80'],
            'timezone'   => ['nullable', 'string', 'max:60'],
            'birth_date' => ['nullable', 'date'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        // Manejo de imagen
        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $path = $request->file('profile_photo')->store('profiles', 'public');
            $data['profile_photo'] = $path;
        }

        $user->update($data);

        return back()->with('success', 'Perfil actualizado correctamente.');
    }
    /** ===================== PREFERENCIAS ===================== */
    public function updatePreferences(Request $request)
    {
        // ⚠️ Este método ya no guardará en BD
        // El cambio de tema se maneja en el navegador con localStorage.
        return response()->json([
            'status' => 'ok',
            'message' => 'Preferencia de tema actualizada localmente.'
        ]);
    }

    public function updateNotifications(Request $request)
    {
        $user = Auth::user();
        $prefs = $user->preferences ?? [];

        $prefs['notifications'] = [
            'email_audits'   => (bool)$request->boolean('email_audits'),
            'email_findings' => (bool)$request->boolean('email_findings'),
            'email_reports'  => (bool)$request->boolean('email_reports'),
            'browser_push'   => (bool)$request->boolean('browser_push'),
        ];
        $user->preferences = $prefs;   // cast json
        $user->save();

        return back()->with('status','notifications-updated');
    }

    public function logoutOthers(Request $request)
    {
        // Si usas Fortify, puedes cerrar otras sesiones con:
        $request->validate(['password' => ['nullable']]); // placeholder
        // Auth::logoutOtherDevices($request->password); // si requieres password
        return back()->with('status','other-sessions-closed');
    }

    public function exportData()
    {
        // TODO: Generar exportación (queue + zip). Placeholder:
        return back()->with('status','export-started');
    }

    public function deactivate()
    {
        $user = Auth::user();
        $user->status = 'inactive';
        $user->save();
        Auth::logout();
        return redirect()->route('auditoria.login')->with('status','Cuenta desactivada. Contáctanos para reactivarla.');
    }
}
