<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Not authenticated
        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Not admin
        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'Forbidden: admins only'
            ], 403);
        }

        return $next($request);
    }
}
