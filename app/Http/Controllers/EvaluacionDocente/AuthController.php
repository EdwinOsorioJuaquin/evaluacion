<?php

namespace App\Http\Controllers\EvaluacionDocente;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('evaluacion.auth.login');
    }

    public function login(Request $request)
    {
        // Validación
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Buscar usuario manualmente (más confiable)
        $user = User::where('email', $request->email)
                    ->where('status', 'active')
                    ->first();

        // Verificar usuario y contraseña
        if ($user && Hash::check($request->password, $user->password)) {
            // Login manual
            Auth::login($user, $request->remember);
            
            // Actualizar último acceso
            $user->update([
                'last_access' => now(),
                'last_access_ip' => $request->ip()
            ]);

            $request->session()->regenerate();
            
            // Redirigir al dashboard
            return redirect('/evaluacion/dashboard');
        }

        // Si falla el login
        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->withInput($request->only('email', 'remember'));
    }

    public function logout(Request $request)
    {
        // Actualizar última conexión
        if (Auth::check()) {
            $user = Auth::user();
            $user->update([
                'last_connection' => now()
            ]);
        }

        // Logout
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/evaluacion');
    }
}