<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) { // if user is not logged in.
            return response()->json([
                'message' => 'Unauthorized'
            ], 401); // Unauthorized (Unauthenticated) code.
        }

        $roles = array_map('trim', $roles); // Remove the spaces between the roles array items.

        foreach ($roles as $role) {
            try {
                if ($request->user()->roles()->where('name', $role)->exists()) {  // Verify if the user is having the required role.
                    return $next($request); // User has the required role, continue performing the request.
                }
            } catch (ModelNotFoundException $exception) {
                // Role does not exist, continue checking the next role
            }
        }

        // User does not have any of the required roles
        return response()->json([
            'message' => 'Forbidden'
        ], 403); // Forbidden (Unauthorized) code.
    }
}
