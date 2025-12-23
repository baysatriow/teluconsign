@extends('layouts.app')

@section('content')
<div class="fixed inset-0 flex items-center justify-center overflow-hidden z-40 bg-gray-50">
    <!-- Background -->
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat z-0 blur-[2px]"
         style="background-image: url('{{ asset('images/bg.png') }}'); opacity: 0.15;">
    </div>

    <!-- Card -->
    <div class="relative z-10 w-full max-w-md p-8 bg-white border border-gray-100 rounded-3xl shadow-2xl animate-fade-in-up">

        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-6 text-[#EC1C25] shadow-sm transform -rotate-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Lupa Kata Sandi?</h1>
            <p class="text-sm text-gray-500 mt-2 leading-relaxed">Jangan khawatir! Masukkan email atau username Anda untuk mencari akun.</p>
        </div>

        @if(session('error'))
            <div class="flex items-center gap-3 p-4 mb-6 text-sm text-red-800 bg-red-50 rounded-xl border border-red-100">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('password.search') }}" method="POST">
            @csrf

            <div class="mb-6">
                <label class="block mb-2 text-xs font-bold text-gray-700 uppercase tracking-wide">Username atau Email</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none text-gray-400 group-focus-within:text-[#EC1C25] transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <input type="text" name="credential" class="block w-full ps-11 p-4 text-sm text-gray-900 border border-gray-200 rounded-xl bg-gray-50 focus:ring-2 focus:ring-[#EC1C25] focus:border-transparent focus:bg-white transition-all shadow-sm" placeholder="Contoh: johndoe123" required autofocus>
                </div>
            </div>

            <button type="submit" class="w-full text-white bg-[#EC1C25] hover:bg-[#c0151d] focus:ring-4 focus:outline-none focus:ring-red-300 font-bold rounded-xl text-sm px-5 py-4 shadow-lg hover:shadow-red-500/30 transition-all transform hover:-translate-y-1">
                Cari Akun
            </button>
        </form>

        <div class="mt-8 text-center pt-6 border-t border-gray-100">
            <a href="{{ route('login') }}" class="inline-flex items-center text-sm font-bold text-gray-500 hover:text-[#EC1C25] transition-colors gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Login
            </a>
        </div>
    </div>
</div>
@endsection
