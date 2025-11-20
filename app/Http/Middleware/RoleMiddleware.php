<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if user is authenticated
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié. Veuillez vous connecter.',
                'redirect' => '/login'
            ], 401);
        }

        // Load role relationship if not already loaded
        $user = $request->user();
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }

        // Check if user has role and if it matches required role
        if (!$user->role || $user->role->nomRole !== $role) {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé. Vous n\'avez pas les permissions nécessaires.',
                'required_role' => $role,
                'your_role' => $user->role ? $user->role->nomRole : 'none'
            ], 403);
        }

        return $next($request);
    }
}
