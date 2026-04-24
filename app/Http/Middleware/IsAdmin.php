<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Vérifier si l'utilisateur est connecté ET est admin
        if (!$request->user() || !$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Accès refusé. Droits administrateur requis.'
            ], 403);
        }

        return $next($request);
    }
}
