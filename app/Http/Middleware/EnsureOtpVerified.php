<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureOtpVerified
{
    /**
     * ============================================================
     *  OTP VERIFICATION GUARD
     * ============================================================
     *  Middleware untuk memastikan user yang sudah login
     *  benar-benar telah melewati proses verifikasi OTP
     */

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if (!$user->is_verified) {
                Auth::logout();

                return redirect()
                    ->route('login')
                    ->with(
                        'warning',
                        'Akun Anda belum aktif. Silakan login kembali untuk verifikasi.'
                    );
            }
        }

        return $next($request);
    }
}
