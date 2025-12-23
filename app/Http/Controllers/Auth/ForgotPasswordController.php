<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\FonnteService;

class ForgotPasswordController extends Controller
{
    /**
     * ------------------------------------------------------------
     *  Dependency Injection
     * ------------------------------------------------------------
     *  Service WhatsApp (Fonnte) untuk pengiriman OTP / Link Reset
     */
    protected FonnteService $fonnte;

    public function __construct(FonnteService $fonnte)
    {
        $this->fonnte = $fonnte;
    }

    /**
     * ============================================================
     *  STEP 1 — SEARCH ACCOUNT
     * ============================================================
     */

    /**
     * Menampilkan form pencarian akun (email / username)
     */
    public function showSearchForm()
    {
        return view('auth.forgot-password.search');
    }

    /**
     * Proses pencarian akun berdasarkan email atau username
     */
    public function search(Request $request)
    {
        $request->validate([
            'credential' => 'required|string',
        ]);

        $user = User::where('email', $request->credential)
            ->orWhere('username', $request->credential)
            ->first();

        if (!$user) {
            return back()->with('error', 'Akun tidak ditemukan.');
        }

        /**
         * --------------------------------------------------------
         *  Validasi Nomor Telepon pada Profile
         * --------------------------------------------------------
         */
        $phone = $user->profile->phone ?? null;
        if (!$phone) {
            return back()->with(
                'error',
                'Akun ini tidak memiliki nomor telepon terdaftar. Hubungi admin.'
            );
        }

        // Simpan user sementara untuk step berikutnya
        session(['reset_user_id' => $user->user_id]);

        return redirect()->route('password.verify.show');
    }

    /**
     * ============================================================
     *  STEP 2 — PHONE VERIFICATION
     * ============================================================
     */

    /**
     * Menampilkan form verifikasi nomor telepon (masked)
     */
    public function showVerifyForm()
    {
        if (!session('reset_user_id')) {
            return redirect()->route('password.request');
        }

        $user = User::find(session('reset_user_id'));
        if (!$user) {
            return redirect()->route('password.request');
        }

        /**
         * --------------------------------------------------------
         *  Masking Nomor Telepon
         *  Example: 081234567890 → 0812*****890
         * --------------------------------------------------------
         */
        $phone = $user->profile->phone;
        $length = strlen($phone);

        $visibleStart = 4;
        $visibleEnd   = 3;

        $maskedPhone =
            substr($phone, 0, $visibleStart)
            . str_repeat('*', $length - ($visibleStart + $visibleEnd))
            . substr($phone, -$visibleEnd);

        return view(
            'auth.forgot-password.verify',
            compact('maskedPhone')
        );
    }

    /**
     * Verifikasi kecocokan nomor telepon & kirim link reset password
     */
    public function verify(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric',
        ]);

        $userId = session('reset_user_id');
        if (!$userId) {
            return redirect()->route('password.request');
        }

        $user = User::find($userId);
        $savedPhone = $user->profile->phone;

        /**
         * --------------------------------------------------------
         *  Normalisasi Nomor Telepon
         *  (08xxx, 8xxx, dan 62xxx dianggap sama)
         * --------------------------------------------------------
         */
        $inputPhone = $this->normalizePhone($request->phone);
        $dbPhone    = $this->normalizePhone($savedPhone);

        if ($inputPhone !== $dbPhone) {
            return back()->with(
                'error',
                'Nomor telepon tidak cocok dengan data kami.'
            );
        }

        /**
         * --------------------------------------------------------
         *  Generate & Store Reset Token
         * --------------------------------------------------------
         */
        $token = Str::random(64);

        DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->delete();

        DB::table('password_reset_tokens')->insert([
            'email'      => $user->email,
            'token'      => $token,
            'created_at' => Carbon::now(),
        ]);

        /**
         * --------------------------------------------------------
         *  Send Reset Link via WhatsApp
         * --------------------------------------------------------
         */
        $link = route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]);

        $message =
            "Halo {$user->name},\n\n"
            . "Kami menerima permintaan reset password untuk akun Anda.\n"
            . "Klik link berikut untuk membuat password baru:\n\n"
            . "{$link}\n\n"
            . "Link ini hanya berlaku selama 60 menit dan hanya bisa digunakan 1 kali.\n\n"
            . "Jika Anda tidak meminta ini, abaikan pesan ini.";

        $this->fonnte->sendMessage($savedPhone, $message);

        // Bersihkan session agar tidak bisa back-step
        session()->forget('reset_user_id');

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Link reset password telah dikirim ke WhatsApp Anda. Silakan cek.'
            );
    }

    /**
     * ============================================================
     *  STEP 3 — RESET PASSWORD (VIA LINK)
     * ============================================================
     */

    /**
     * Menampilkan form reset password dari link
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.forgot-password.reset')->with([
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Proses reset password user
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/',
            ],
        ], [
            'password.regex' =>
                'Password harus mengandung huruf besar, kecil, angka, dan simbol.',
        ]);

        /**
         * --------------------------------------------------------
         *  Validasi Token Reset
         * --------------------------------------------------------
         */
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$record) {
            return back()->with(
                'error',
                'Link reset password tidak valid atau salah.'
            );
        }

        /**
         * --------------------------------------------------------
         *  Validasi Expired Token (60 Menit)
         * --------------------------------------------------------
         */
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            return back()->with(
                'error',
                'Link reset password sudah kadaluarsa. Silakan minta ulang.'
            );
        }

        /**
         * --------------------------------------------------------
         *  Update Password User
         * --------------------------------------------------------
         */
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->with('error', 'User tidak ditemukan.');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Token hanya boleh sekali pakai
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Password berhasil diubah! Silakan login dengan password baru.'
            );
    }

    /**
     * ============================================================
     *  HELPER
     * ============================================================
     */

    /**
     * Normalisasi format nomor telepon Indonesia
     */
    private function normalizePhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (substr($phone, 0, 2) === '08') {
            return '62' . substr($phone, 1);
        }

        if (substr($phone, 0, 1) === '8') {
            return '62' . $phone;
        }

        return $phone;
    }
}
