@extends('layouts.app')

@section('content')
<div class="fixed inset-0 flex items-center justify-center overflow-hidden z-40 bg-gray-50">

    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat z-0 blur-[2px]"
         style="background-image: url('{{ asset('images/bg.png') }}'); opacity: 0.15;">
    </div>

    <div class="relative z-10 w-full max-w-md p-8 bg-white border border-gray-100 rounded-3xl shadow-2xl animate-fade-in-up">

        <div class="text-center mb-6">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3 text-green-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h1 class="text-xl font-bold text-gray-900">Verifikasi Kepemilikan</h1>
            <p class="text-sm text-gray-500 mt-2">
                Akun ditemukan! Untuk keamanan, masukkan nomor WhatsApp lengkap yang terdaftar berakhiran: <br>
                <span class="font-mono font-bold text-lg text-gray-800 tracking-wider">{{ $maskedPhone }}</span>
            </p>
        </div>

        @if(session('error'))
            <div class="p-3 mb-4 text-xs font-medium text-red-800 bg-red-100 rounded-lg border border-red-200 text-center">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('password.verify.submit') }}" method="POST">
            @csrf

            <div class="mb-6">
                <label class="block mb-2 text-xs font-bold text-gray-700 uppercase tracking-wide text-center">Nomor WhatsApp Lengkap</label>
                <input type="number" name="phone" class="block w-full p-3 text-center text-lg font-bold text-gray-900 border border-gray-200 rounded-xl bg-gray-50 focus:ring-[#EC1C25] focus:border-[#EC1C25] transition-shadow focus:shadow-md" placeholder="08..." required autofocus>
            </div>

            <button type="submit" class="w-full text-white bg-[#EC1C25] hover:bg-[#c0151d] focus:ring-4 focus:outline-none focus:ring-red-300 font-bold rounded-xl text-sm px-5 py-3.5 shadow-lg transition-all transform hover:-translate-y-0.5">
                Verifikasi & Kirim Link
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('password.request') }}" class="text-sm text-gray-400 hover:text-gray-600 font-medium">Bukan akun ini? Cari lagi</a>
        </div>
    </div>
</div>
@endsection
