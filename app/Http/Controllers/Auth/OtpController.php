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
    /**
     * ------------------------------------------------------------
     *  Dependency Injection
     * ------------------------------------------------------------
     *  Service WhatsApp (Fonnte) untuk pengiriman OTP
     */
    protected FonnteService $fonnte;

    public function __construct(FonnteService $fonnte)
    {
        $this->fonnte = $fonnte;
    }

    /**
     * ============================================================
     *  SHOW OTP VERIFICATION FORM
     * ============================================================
     */

    /**
     * Menampilkan halaman input OTP + info cooldown resend
     */
    public function showVerifyForm()
    {
        /**
         * --------------------------------------------------------
         *  Validasi Session OTP
         * --------------------------------------------------------
         */
        if (!session('otp_user_id')) {
            return redirect()->route('login');
        }

        /**
         * --------------------------------------------------------
         *  Hitung Sisa Cooldown Tombol Resend
         * --------------------------------------------------------
         */
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

    /**
     * ============================================================
     *  VERIFY OTP
     * ============================================================
     */

    /**
     * Proses verifikasi OTP login / aktivasi
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
        ]);

        /**
         * --------------------------------------------------------
         *  Ambil User dari Session OTP
         * --------------------------------------------------------
         */
        $userId = session('otp_user_id');
        if (!$userId) {
            return redirect()
                ->route('login')
                ->with('error', 'Sesi verifikasi habis. Silakan login ulang.');
        }

        $user = User::find($userId);

        /**
         * --------------------------------------------------------
         *  Validasi OTP & Masa Berlaku
         * --------------------------------------------------------
         */
        if (!$user || $request->otp != $user->otp_code) {
            return back()->withErrors(['otp' => 'Kode OTP salah.']);
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Kode OTP sudah kadaluarsa.']);
        }

        /**
         * ========================================================
         *  OTP VERIFIED SUCCESSFULLY
         * ========================================================
         */

        /**
         * 1️⃣ Update status user & bersihkan OTP
         */
        $user->is_verified      = true;
        $user->otp_code         = null;
        $user->otp_expires_at   = null;
        $user->save();

        /**
         * 2️⃣ Login user secara resmi
         */
        Auth::login($user, session('otp_remember', false));

        /**
         * 3️⃣ Bersihkan session & cache OTP
         */
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

    /**
     * ============================================================
     *  RESEND OTP (RATE LIMITED)
     * ============================================================
     */

    /**
     * Kirim ulang OTP dengan sistem cooldown & progressive delay
     */
    public function resend()
    {
        $userId = session('otp_user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        /**
         * --------------------------------------------------------
         *  Rate Limiting Configuration
         * --------------------------------------------------------
         */
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

        /**
         * --------------------------------------------------------
         *  Progressive Cooldown
         *  1 menit → 2 menit → ... → max 5 menit
         * --------------------------------------------------------
         */
        $attempts     = Cache::get($attemptsKey, 0) + 1;
        $minutes      = min($attempts, 5);
        $cooldownTime = now()->addMinutes($minutes);

        Cache::put($cooldownKey, $cooldownTime, $cooldownTime);
        Cache::put($attemptsKey, $attempts, now()->addHour());

        /**
         * --------------------------------------------------------
         *  Generate & Send New OTP
         * --------------------------------------------------------
         */
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
