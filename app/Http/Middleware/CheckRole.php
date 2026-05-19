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
     * @param Closure(Request): (Response) $next
     */
    public function handle(
        Request $request,
        Closure $next,
        ...$roles
    ): Response {
        if (
            !auth()->check() ||
            !in_array(auth()->user()->role, $roles)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: Kamu tidak memiliki hak akses.'
            ], 403);
        }

        return $next($request);
    }
}