<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOrAgentMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié. Veuillez vous connecter.',
                'redirect' => '/login'
            ], 401);
        }

        $user = $request->user();
        
        // Load role relationship if not already loaded
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }
        
        // Check if user has Admin or Agent role
        if (!$user->role || !in_array($user->role->nomRole, ['Admin', 'Agent'])) {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé. Seuls les Admins et Agents peuvent effectuer cette action.',
                'required_roles' => ['Admin', 'Agent'],
                'your_role' => $user->role ? $user->role->nomRole : 'none'
            ], 403);
        }

        return $next($request);
    }
}
