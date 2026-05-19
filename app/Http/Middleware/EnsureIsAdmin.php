<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->guard('api')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Token tidak ditemukan atau sesi telah berakhir.'
            ], 401);
        }

        if (auth()->guard('api')->user()->role === 'admin') {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Forbidden: Akses ditolak. Halaman ini hanya untuk Administrator.'
        ], 403);
    }
}