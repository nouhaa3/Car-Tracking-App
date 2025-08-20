<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/*class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     *
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}*/

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (!$request->user() || $request->user()->role->nom !== $role) {
            return response()->json(['message' => 'Accès réfusé !'], 403);
        }

        return $next($request);
    }
}
