<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Services\FonnteService;
use Carbon\Carbon;

class OtpController extends Controller
{
    protected FonnteService $fonnte;

    public function __construct(FonnteService $fonnte)
    {
        $this->fonnte = $fonnte;
    }

    public function showVerifyForm()
    {
        if (!session('otp_user_id')) {
            return redirect()->route('login');
        }

        $userId   = session('otp_user_id');
        $cacheKey = 'otp_resend_cooldown_' . $userId;
        $cooldown = 0;

        if (Cache::has($cacheKey)) {
            $expiresAt = Cache::get($cacheKey);
            $cooldown  = Carbon::now()->diffInSeconds($expiresAt, false);

            if ($cooldown < 0) {
                $cooldown = 0;
            }
        }

        return view('auth.otp', compact('cooldown'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
        ]);

        $userId = session('otp_user_id');
        if (!$userId) {
            return redirect()
                ->route('login')
                ->with('error', 'Sesi verifikasi habis. Silakan login ulang.');
        }

        $user = User::find($userId);

        if (!$user || $request->otp != $user->otp_code) {
            return back()->withErrors(['otp' => 'Kode OTP salah.']);
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Kode OTP sudah kadaluarsa.']);
        }

        $user->is_verified    = true;
        $user->otp_code       = null;
        $user->otp_expires_at = null;
        $user->save();

        Auth::login($user, session('otp_remember', false));

        session()->forget([
            'otp_user_id',
            'otp_context',
            'otp_remember',
        ]);

        Cache::forget('otp_resend_cooldown_' . $userId);
        Cache::forget('otp_resend_attempts_' . $userId);

        $request->session()->regenerate();

        if ($user->role === 'admin') {
            return redirect()
                ->route('admin.dashboard')
                ->with('success', 'Login berhasil! Selamat datang Admin.');
        }

        return redirect()
            ->route('home')
            ->with('success', 'Verifikasi berhasil! Selamat datang.');
    }

    public function resend()
    {
        $userId = session('otp_user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $cooldownKey = 'otp_resend_cooldown_' . $userId;
        $attemptsKey = 'otp_resend_attempts_' . $userId;

        if (Cache::has($cooldownKey)) {
            $seconds = Carbon::now()->diffInSeconds(
                Cache::get($cooldownKey),
                false
            );

            if ($seconds > 0) {
                return back()->with(
                    'error',
                    "Mohon tunggu {$seconds} detik lagi sebelum mengirim ulang."
                );
            }
        }

        $attempts     = Cache::get($attemptsKey, 0) + 1;
        $minutes      = min($attempts, 5);
        $cooldownTime = now()->addMinutes($minutes);

        Cache::put($cooldownKey, $cooldownTime, $cooldownTime);
        Cache::put($attemptsKey, $attempts, now()->addHour());

        $user = User::find($userId);
        $userPhone = $user->profile->phone ?? null;

        if (!$userPhone) {
            return back()->with('error', 'Nomor telepon tidak ditemukan.');
        }

        $otp = $user->generateOtp();
        $this->fonnte->sendMessage(
            $userPhone,
            "Kode OTP Baru: *{$otp}*"
        );

        return back()->with(
            'success',
            "Kode OTP baru telah dikirim. Jeda berikutnya: {$minutes} menit."
        );
    }
}