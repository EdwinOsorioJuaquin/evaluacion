<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Verifica si el usuario autenticado tiene al menos uno de los roles permitidos.
     *
     * Ejemplo de uso en rutas:
     * Route::get('/panel', [PanelController::class, 'index'])->middleware('role:admin,auditor');
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        // 🔒 Si no hay usuario autenticado
        if (!$user) {
            // Si estás en el módulo de auditoría, redirige a su login
            if ($request->is('auditoria/*')) {
                return redirect()->route('auditoria.login')
                    ->withErrors(['auth' => 'Debes iniciar sesión para continuar.']);
            }

            // Caso general
            return redirect()->route('login')
                ->withErrors(['auth' => 'Debes iniciar sesión para continuar.']);
        }

        // 🧩 Obtener y normalizar el campo role (puede ser string, JSON o array)
        $userRoles = $this->normalizeRole($user->role);

        // 🚦 Validar si el usuario tiene alguno de los roles permitidos
        foreach ($roles as $role) {
            if (in_array(strtolower($role), $userRoles)) {
                return $next($request);
            }
        }

        // 🚫 Si no tiene permisos
        return redirect()->route('login')
            ->withErrors(['auth' => 'No tienes permisos para acceder a esta sección.']);
    }

    /**
     * Normaliza el campo role en un array de strings.
     */
    private function normalizeRole($role): array
    {
        // Caso 1: ya es array (["admin", "auditor"])
        if (is_array($role)) {
            return array_map(fn($r) => strtolower(trim($r)), $role);
        }

        // Caso 2: JSON válido
        if (is_string($role)) {
            $decoded = json_decode($role, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                if (is_array($decoded)) {
                    // Ejemplo: ["admin"] o {"role": "admin"}
                    if (isset($decoded['role'])) {
                        return [strtolower(trim($decoded['role']))];
                    }
                    return array_map(fn($r) => strtolower(trim($r)), $decoded);
                } else {
                    // Ejemplo: "admin"
                    return [strtolower(trim($decoded))];
                }
            }
        }

        // Caso 3: Objeto
        if (is_object($role) && property_exists($role, 'role')) {
            return [strtolower(trim($role->role))];
        }

        // Caso 4: string simple
        if (is_string($role)) {
            return [strtolower(trim($role))];
        }

        // Por defecto, sin rol
        return [];
    }
}
