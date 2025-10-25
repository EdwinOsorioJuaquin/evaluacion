<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRole
{
    public function handle(Request $request, Closure $next, $roles)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        // "admin"  o  "admin,auditor"
        $allowed = array_filter(array_map('trim', explode(',', (string) $roles)));

        if (!$user->hasRole($allowed)) {
            abort(403, 'No tienes permisos suficientes.');
        }

        return $next($request);
    }
}
