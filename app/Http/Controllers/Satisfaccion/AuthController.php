<?php

namespace App\Http\Controllers\Satisfaccion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('satisfaccion.auth.login');
    }

    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return back()->withErrors(['email' => '❌ Usuario no encontrado']);
    }

    // 🔒 Verificar con bcrypt
    if (\Hash::check($request->password, $user->password)) {

        Auth::login($user);

        if ($user->isAdmin()) {
            return redirect()->route('satisfaccion.admin.dashboard');
        }

        if ($user->isStudent()) {
            return redirect()->route('satisfaccion.student.dashboard');
        }

        return back()->withErrors(['email' => '⚠️ Rol no reconocido']);
    }

    return back()->withErrors(['email' => '❌ Contraseña incorrecta']);
}

    public function logout()
    {
        Auth::logout();
        return redirect()->route('satisfaccion.login');
    }
}
