@extends('layouts.app')

@section('content')
<div class="fixed inset-0 flex flex-col items-center justify-center overflow-hidden z-40 bg-gray-50">

    <!-- Background Image -->
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat z-0 blur-[2px]"
         style="background-image: url('{{ asset('images/bg.png') }}'); opacity: 0.15;">
    </div>

    <!-- Card Container -->
    <div class="relative z-10 w-full max-w-sm p-8 bg-white border border-gray-100 rounded-3xl shadow-2xl animate-fade-in-up">

        <!-- Icon Header -->
        <div class="mb-6 text-center">
            <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-[#EC1C25] shadow-sm transform rotate-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Verifikasi OTP</h1>
            <p class="text-sm text-gray-500 mt-2 px-4 leading-relaxed">
                Kami telah mengirimkan 6 digit kode ke WhatsApp Anda.
            </p>
        </div>

        @if(session('success'))
            <div class="p-3 mb-4 text-xs font-medium text-green-800 bg-green-100 rounded-lg border border-green-200">
                {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="p-3 mb-4 text-xs font-medium text-yellow-800 bg-yellow-100 rounded-lg border border-yellow-200">
                {{ session('warning') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-3 mb-4 text-xs font-medium text-red-800 bg-red-100 rounded-lg border border-red-200">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('otp.verify.submit') }}" method="POST">
            @csrf

            <div class="mb-6">
                <label class="block mb-2 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Masukkan Kode</label>
                <input type="text" name="otp"
                       class="block w-full p-4 text-center text-3xl font-extrabold tracking-[0.5em] text-gray-900 border border-gray-200 rounded-xl bg-gray-50 focus:ring-4 focus:ring-red-100 focus:border-[#EC1C25] transition-all placeholder-gray-300"
                       placeholder="••••••" maxlength="6" required autofocus autocomplete="one-time-code">
                @error('otp')
                    <p class="text-red-500 text-xs mt-2 text-center font-medium">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full text-white bg-[#EC1C25] hover:bg-[#c0151d] focus:ring-4 focus:outline-none focus:ring-red-300 font-bold rounded-xl text-sm px-5 py-3.5 shadow-lg shadow-red-500/30 transition-all transform hover:-translate-y-0.5 active:scale-95">
                Konfirmasi Kode
            </button>
        </form>

        <div class="mt-8 text-center border-t border-gray-100 pt-6">
            <p class="text-sm text-gray-500 mb-2">Belum menerima kode?</p>

            <form action="{{ route('otp.resend') }}" method="POST" id="resendForm">
                @csrf
                <button type="submit" id="resendBtn" class="text-[#EC1C25] hover:text-[#a01219] font-bold text-sm bg-transparent border-0 p-0 cursor-pointer transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    Kirim Ulang OTP
                </button>
                <span id="timer" class="text-gray-400 text-sm font-medium ml-1 hidden"></span>
            </form>
        </div>

    </div>

    <!-- Footer Action -->
    <div class="relative z-20 mt-8">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="flex items-center gap-2 text-sm text-gray-500 hover:text-[#EC1C25] font-medium transition-colors bg-white/80 px-4 py-2 rounded-full shadow-sm hover:shadow backdrop-blur-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"></path></svg>
                Kembali ke Login
            </button>
        </form>
    </div>

</div>

<!-- Script Countdown Timer -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let cooldown = {{ $cooldown ?? 0 }};
        const btn = document.getElementById('resendBtn');
        const timer = document.getElementById('timer');

        function startTimer(duration) {
            let timerSec = duration;
            btn.disabled = true;
            btn.classList.add('text-gray-400', 'cursor-not-allowed');
            btn.classList.remove('text-[#EC1C25]', 'hover:text-[#a01219]');
            timer.classList.remove('hidden');

            const interval = setInterval(function () {
                let minutes = parseInt(timerSec / 60, 10);
                let seconds = parseInt(timerSec % 60, 10);

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                timer.textContent = "(" + minutes + ":" + seconds + ")";

                if (--timerSec < 0) {
                    clearInterval(interval);
                    btn.disabled = false;
                    btn.classList.remove('text-gray-400', 'cursor-not-allowed');
                    btn.classList.add('text-[#EC1C25]', 'hover:text-[#a01219]');
                    timer.classList.add('hidden');
                    timer.textContent = "";
                }
            }, 1000);
        }

        if (cooldown > 0) {
            startTimer(cooldown);
        }
    });
</script>
@endsection
