<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckToken
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated
        if (!$request->user()) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthenticated. Please log in again.',
            ], 401);
        }
        return $next($request);
    }
}
