<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * ============================================================
     *  ADMIN ACCESS GUARD
     * ============================================================
     *  Middleware untuk membatasi akses halaman khusus Administrator
     */

    public function handle(Request $request, Closure $next): Response
    {
        /**
         * --------------------------------------------------------
         *  Validasi Autentikasi & Role
         * --------------------------------------------------------
         */
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        /**
         * --------------------------------------------------------
         *  Akses Ditolak
         * --------------------------------------------------------
         *  Redirect ke halaman utama dengan pesan error
         */
        return redirect('/')
            ->with('error', 'Akses ditolak. Halaman ini khusus Administrator.');
    }
}
