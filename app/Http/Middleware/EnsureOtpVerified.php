<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureOtpVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Jika user login tapi entah kenapa belum verified (misal bypass atau db manual)
            // Logoutkan dia dan suruh login ulang agar kena flow OTP
            if (!$user->is_verified) {
                Auth::logout();
                return redirect()->route('login')->with('warning', 'Akun Anda belum aktif. Silakan login kembali untuk verifikasi.');
            }
        }

        return $next($request);
    }
}
