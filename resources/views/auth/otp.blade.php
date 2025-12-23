@extends('layouts.app')

@section('content')
<div class="fixed inset-0 flex flex-col items-center justify-center overflow-hidden z-40 bg-gray-50/50 backdrop-blur-sm">

    <!-- Background Decoration -->
    <div class="absolute inset-0 z-0 opacity-10">
        <div class="absolute top-0 -left-4 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
        <div class="absolute top-0 -right-4 w-72 h-72 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
    </div>

    <!-- Card Container -->
    <div class="relative z-10 w-full max-w-sm p-8 bg-white border border-gray-100 rounded-3xl shadow-xl animate-fade-in-up">

        <!-- Icon Header -->
        <div class="mb-8 text-center">
            <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-6 text-[#EC1C25] shadow-sm transform hover:rotate-12 transition-transform duration-300">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight mb-2">Verifikasi OTP</h1>
            <p class="text-sm text-gray-500 leading-relaxed px-2">
                Kami telah mengirimkan 6 digit kode keamanan ke WhatsApp Anda.
            </p>
        </div>

        @if(session('success'))
            <div class="p-4 mb-6 text-sm font-medium text-green-800 bg-green-50 rounded-xl border border-green-100 flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="p-4 mb-6 text-sm font-medium text-yellow-800 bg-yellow-50 rounded-xl border border-yellow-100 flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                {{ session('warning') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 mb-6 text-sm font-medium text-red-800 bg-red-50 rounded-xl border border-red-100 flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('otp.verify.submit') }}" method="POST">
            @csrf

            <div class="mb-8">
                <label class="block mb-3 text-xs font-bold text-gray-500 uppercase tracking-widest text-center">Masukkan Kode 6 Digit</label>
                <input type="text" name="otp"
                       class="block w-full p-4 text-center text-4xl font-extrabold tracking-[0.5em] text-gray-900 border border-gray-200 rounded-2xl bg-gray-50 focus:ring-4 focus:ring-red-100 focus:border-[#EC1C25] transition-all placeholder-gray-300 shadow-inner"
                       placeholder="••••••" maxlength="6" inputmode="numeric" 
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                       required autofocus autocomplete="one-time-code">
                @error('otp')
                    <p class="text-red-500 text-xs mt-2 text-center font-bold animate-pulse">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full text-white bg-[#EC1C25] hover:bg-[#c0151d] focus:ring-4 focus:outline-none focus:ring-red-200 font-bold rounded-xl text-sm px-6 py-4 shadow-lg shadow-red-500/30 transition-all transform hover:-translate-y-1 active:scale-95 uppercase tracking-wide">
                Verifikasi
            </button>
        </form>

        <form action="{{ route('logout') }}" method="POST" class="mt-4 text-center">
            @csrf
            <button type="submit" class="text-sm font-bold text-gray-500 hover:text-[#EC1C25] transition-colors py-2 px-4 rounded-lg hover:bg-gray-50">
                &larr; Kembali ke Login
            </button>
        </form>

        <div class="mt-8 text-center border-t border-gray-100 pt-6">
            <p class="text-sm text-gray-500 mb-3">Tidak menerima kode?</p>

            <form action="{{ route('otp.resend') }}" method="POST" id="resendForm">
                @csrf
                <button type="submit" id="resendBtn" class="inline-flex items-center justify-center gap-2 text-[#EC1C25] hover:text-[#a01219] font-bold text-sm bg-red-50 hover:bg-red-100 px-4 py-2 rounded-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Kirim Ulang OTP
                </button>
                <div id="timer" class="text-gray-400 text-xs font-mono mt-2 hidden"></div>
            </form>
        </div>

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
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            timer.classList.remove('hidden');

            const interval = setInterval(function () {
                let minutes = parseInt(timerSec / 60, 10);
                let seconds = parseInt(timerSec % 60, 10);

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                timer.textContent = "Tunggu " + minutes + ":" + seconds;

                if (--timerSec < 0) {
                    clearInterval(interval);
                    btn.disabled = false;
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
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
