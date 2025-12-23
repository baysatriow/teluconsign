@extends('layouts.app')

@section('content')
<div class="fixed inset-0 flex items-center justify-center overflow-hidden z-40 bg-gray-50">

    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat z-0 blur-[2px]"
         style="background-image: url('{{ asset('images/bg.png') }}'); opacity: 0.15;">
    </div>

    <div class="relative z-10 w-full max-w-md p-8 bg-white border border-gray-100 rounded-3xl shadow-2xl animate-fade-in-up">

        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-green-50 rounded-2xl flex items-center justify-center mx-auto mb-6 text-green-600 shadow-sm transform rotate-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight">Verifikasi Kepemilikan</h1>
            <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                Akun ditemukan! Masukkan nomor WhatsApp lengkap yang berakhiran: <br>
                <span class="font-mono font-bold text-lg text-gray-900 tracking-wider bg-gray-100 px-2 py-0.5 rounded ml-1">{{ $maskedPhone }}</span>
            </p>
        </div>

        @if(session('error'))
            <div class="flex items-center gap-3 p-4 mb-6 text-sm text-red-800 bg-red-50 rounded-xl border border-red-100">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('password.verify.submit') }}" method="POST">
            @csrf

            <div class="mb-6">
                <label class="block mb-2 text-xs font-bold text-gray-700 uppercase tracking-wide text-center">Nomor WhatsApp Lengkap</label>
                <div class="relative group">
                    <input type="number" name="phone" class="block w-full p-4 text-center text-lg font-bold text-gray-900 border border-gray-200 rounded-xl bg-gray-50 focus:ring-2 focus:ring-[#EC1C25] focus:border-transparent focus:bg-white transition-all shadow-sm placeholder-gray-300" placeholder="08xxxxxxxxxx" required autofocus>
                </div>
            </div>

            <button type="submit" class="w-full text-white bg-[#EC1C25] hover:bg-[#c0151d] focus:ring-4 focus:outline-none focus:ring-red-300 font-bold rounded-xl text-sm px-5 py-4 shadow-lg hover:shadow-red-500/30 transition-all transform hover:-translate-y-1">
                Verifikasi & Kirim OTP
            </button>
        </form>

        <div class="mt-8 text-center pt-6 border-t border-gray-100">
            <a href="{{ route('password.request') }}" class="inline-flex items-center text-sm font-bold text-gray-400 hover:text-gray-600 transition-colors gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Bukan akun ini? Cari lagi
            </a>
        </div>
    </div>
</div>
@endsection
