<?php

namespace App\Http\Controllers\Auditoria;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Mostrar el formulario de login del módulo Auditoría.
     */
    public function showLoginForm()
    {
        // Si ya está autenticado, redirigir al dashboard directamente
        if (Auth::check()) {
            return redirect()->route('auditoria.dashboard.index');
        }

        return view('auditoria.auth.login');
    }

    /**
     * Procesar inicio de sesión.
     */
    public function login(Request $request)
    {
        // 1️⃣ Validar datos
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'Debes ingresar un correo válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        // 2️⃣ Buscar usuario activo
        $user = User::where('email', $request->email)
                    ->where('status', 'active')
                    ->first();

        // 3️⃣ Validar existencia y contraseña
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'Credenciales incorrectas o usuario inactivo.',
            ])->withInput();
        }

        // 4️⃣ Verificar roles válidos para este módulo
        if (! $user->hasRole(['admin', 'auditor'])) {
            return back()->withErrors([
                'email' => 'No tienes permisos para acceder al módulo de Auditoría.',
            ]);
        }

        // 5️⃣ Autenticar manualmente
        Auth::login($user, $request->boolean('remember'));

        // 6️⃣ Actualizar últimos accesos
        $user->update([
            'last_access' => now(),
            'last_access_ip' => $request->ip(),
        ]);

        $request->session()->regenerate();

        // 7️⃣ Redirigir al dashboard
        return redirect()->route('auditoria.dashboard.index')
            ->with('success', 'Bienvenido al módulo de Auditoría, ' . $user->first_name . ' 👋');
    }

    /**
     * Cerrar sesión.
     */
    public function logout(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->update(['last_connection' => now()]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auditoria.auth.login')
            ->with('success', 'Has cerrado sesión correctamente.');
    }



    /**
     * Mostrar el formulario de registro.
     */
    public function showRegistrationForm()
    {
        return view('auditoria.auth.register');
    }

    /**
     * Procesar el registro de un nuevo usuario.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name'            => ['required', 'string', 'max:120'],
            'last_name'             => ['required', 'string', 'max:120'],
            'email'                 => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone_number'          => ['nullable', 'string', 'max:30'],
            'password'              => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'first_name'    => $validated['first_name'],
            'last_name'     => $validated['last_name'],
            'full_name'     => trim($validated['first_name'].' '.$validated['last_name']),
            'email'         => $validated['email'],
            'phone_number'  => $validated['phone_number'] ?? null,
            'password'      => Hash::make($validated['password']),
            'role'          => ['auditor'],         // tu modelo castea 'role' a json
            'status'        => 'active',         // opcional si manejas estados
        ]);

        event(new Registered($user));
        Auth::login($user);

        // Ajusta a tu ruta real de dashboard (parece que usas dashboard.index)
        return redirect()->route('auditoria.dashboard.index');
    }  

}