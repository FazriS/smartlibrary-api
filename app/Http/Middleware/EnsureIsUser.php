<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Validasi Token: Pastikan user terautentikasi melalui Guard API (JWT)
        if (!auth()->guard('api')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Token tidak ditemukan atau sesi telah berakhir.'
            ], 401);
        }

        // 2. Validasi Otoritas: Pastikan pengguna memiliki peran sebagai pengguna umum
        if (auth()->guard('api')->user()->role === 'user') {
            return $next($request);
        }

        // 3. Penolakan Akses jika role tidak sesuai
        return response()->json([
            'success' => false,
            'message' => 'Forbidden: Akses ditolak. Halaman ini hanya untuk Pengguna Umum.'
        ], 403);
    }
}