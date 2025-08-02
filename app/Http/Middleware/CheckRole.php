<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Vérifier si l'utilisateur est authentifié
        if (!auth()->check()) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        $user = auth()->user();
        
        // Vérifier si l'utilisateur a un des rôles autorisés
        if (!in_array($user->role, $roles)) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        return $next($request);
    }
}
