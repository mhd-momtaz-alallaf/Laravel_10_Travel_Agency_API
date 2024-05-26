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
        if (! auth()->check()) { // if user is not logged in.
            abort(401); // Unauthorized (Unauthenticated) code.
        }

        if (! $request->user()->roles()->where('name', $role)->exists()) { // if the user doesn't have the permission (admin or editor Role), the role parameter will be passed when the middleware will be used.
            abort(403); // Forbidden (Unauthorized) code.
        }

        // Otherwise continue performing the request.
        return $next($request);
    }
}
