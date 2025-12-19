@extends('layouts.app')

@section('content')
<div class="fixed inset-0 flex items-center justify-center overflow-hidden z-40 bg-gray-50">
    <!-- Background -->
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat z-0 blur-[2px]"
         style="background-image: url('{{ asset('images/bg.png') }}'); opacity: 0.15;">
    </div>

    <!-- Card -->
    <div class="relative z-10 w-full max-w-md p-8 bg-white border border-gray-100 rounded-3xl shadow-2xl animate-fade-in-up">

        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-[#EC1C25] tracking-tight">Lupa Password?</h1>
            <p class="text-sm text-gray-500 mt-2">Masukkan username atau email Anda untuk mencari akun.</p>
        </div>

        @if(session('error'))
            <div class="p-3 mb-4 text-xs font-medium text-red-800 bg-red-100 rounded-lg border border-red-200">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('password.search') }}" method="POST">
            @csrf

            <div class="mb-6">
                <label class="block mb-2 text-xs font-bold text-gray-700 uppercase tracking-wide">Email / Username</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <input type="text" name="credential" class="block w-full ps-10 p-3 text-sm text-gray-900 border border-gray-200 rounded-xl bg-gray-50 focus:ring-[#EC1C25] focus:border-[#EC1C25] transition-shadow focus:shadow-md" placeholder="Contoh: johndoe123" required autofocus>
                </div>
            </div>

            <button type="submit" class="w-full text-white bg-[#EC1C25] hover:bg-[#c0151d] focus:ring-4 focus:outline-none focus:ring-red-300 font-bold rounded-xl text-sm px-5 py-3.5 shadow-lg transition-all transform hover:-translate-y-0.5">
                Cari Akun
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-[#EC1C25] font-medium transition-colors">Kembali ke Login</a>
        </div>
    </div>
</div>
@endsection
