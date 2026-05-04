<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminWebMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!Auth::user()->is_admin) {
            abort(403, 'Akses ditolak. Hanya admin yang diizinkan.');
        }

        // Jika semua pengecekan lolos, lanjutkan ke halaman yang dituju
        return $next($request);
    }
}
