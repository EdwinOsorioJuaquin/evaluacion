<?php

namespace App\Http\Controllers\Impacto;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('impacto.register');
    }

    public function register(Request $request)
    {
        // Validación
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'dni' => 'required|string|max:20|unique:users,dni',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'first_name.required' => 'El nombre es obligatorio',
            'last_name.required' => 'El apellido es obligatorio',
            'dni.required' => 'El DNI es obligatorio',
            'dni.unique' => 'Este DNI ya está registrado',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.unique' => 'Este correo ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Crear usuario
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'full_name' => $request->first_name . ' ' . $request->last_name,
            'dni' => $request->dni,
            'document' => $request->dni,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => json_encode('student'),
            'status' => 'active',
            'timezone' => 'America/Lima',
            'synchronized' => true,
        ]);

        return redirect()->route('impacto.login')
            ->with('success', '¡Registro exitoso! Ahora puedes iniciar sesión.');
    }

    public function showLoginForm()
    {
        return view('impacto.login');
    }

    public function login(Request $request)
    {
        // Validación
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'Ingresa un correo válido',
            'password.required' => 'La contraseña es obligatoria',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Buscar usuario por email
        $user = User::where('email', $request->email)->first();

        // Verificar si existe el usuario
        if (!$user) {
            return redirect()->back()
                ->withErrors(['email' => 'El correo electrónico no está registrado'])
                ->withInput();
        }

        // Verificar si el usuario está activo
        if ($user->status !== 'active') {
            return redirect()->back()
                ->withErrors(['email' => 'Tu cuenta está inactiva. Contacta al administrador.'])
                ->withInput();
        }

        // Verificar contraseña
        if (!Hash::check($request->password, $user->password)) {
            return redirect()->back()
                ->withErrors(['password' => 'La contraseña es incorrecta'])
                ->withInput();
        }

        // Actualizar información de acceso
        $user->update([
            'last_access' => now(),
            'last_connection' => now(),
            'last_access_ip' => $request->ip(),
        ]);

        // Iniciar sesión
        Auth::login($user);

        // Redirigir al dashboard
        return redirect()->route('impacto.dashboard')->with('success', '¡Bienvenido ' . $user->first_name . '!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('impacto.login')->with('success', 'Has cerrado sesión correctamente');
    }
}