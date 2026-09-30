<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Vérifie que l'utilisateur est connecté
        if (!auth()->check()) {
            return redirect('/login');
        }

        // Vérifie si le rôle de l'utilisateur fait partie des rôles autorisés
        if (!in_array(auth()->user()->role->name, $roles)) {
            abort(403, 'Accès refusé');
        }

        return $next($request);
    }
}
