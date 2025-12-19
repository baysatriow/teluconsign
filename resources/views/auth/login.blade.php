@extends('layouts.app')

@section('content')
<div class="fixed inset-0 flex items-center justify-center overflow-y-auto z-40 bg-gray-50 py-10">

    <!-- Background -->
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat z-0 blur-[2px] fixed"
         style="background-image: url('{{ asset('images/bg.png') }}'); opacity: 0.15;">
    </div>

    <!-- Card Container -->
    <div class="relative z-10 w-full max-w-sm p-8 bg-white border border-gray-100 rounded-3xl shadow-2xl animate-fade-in-up mx-4 my-auto">

        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-[#EC1C25] tracking-tight">
                Selamat Datang
            </h1>
            <p class="text-sm text-gray-500 mt-2 font-medium">Masuk untuk melanjutkan belanja</p>
        </div>

        <form class="space-y-6" action="{{ route('login.submit') }}" method="POST">
            @csrf

            <!-- Email / Username Input -->
            <div>
                <label for="login" class="block mb-1.5 text-xs font-bold text-gray-700 uppercase tracking-wide">Email atau Username</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 16"><path d="m10.036 8.278 9.258-7.79A1.979 1.979 0 0 0 18 0H2A1.987 1.987 0 0 0 .641.541l9.395 7.737Z"/><path d="M11.241 9.817c-.36.275-.801.425-1.255.427-.428 0-.845-.138-1.187-.395L0 2.6V14a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V2.5l-8.759 7.317Z"/></svg>
                    </div>
                    <!-- Ubah name menjadi 'login' agar bisa menangani keduanya -->
                    <input type="text" name="login" id="login" value="{{ old('login') }}" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full ps-10 p-3 transition-shadow focus:shadow-md" placeholder="Email atau Username" required autofocus>
                </div>
                @error('login') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block mb-1.5 text-xs font-bold text-gray-700 uppercase tracking-wide">Kata Sandi</label>
                <div class="relative">
                    <input type="password" name="password" id="password" placeholder="••••••••" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full ps-10 p-3 pr-10 transition-shadow focus:shadow-md" required>
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 20"><path d="M14 7h-1.5V4.5a4.5 4.5 0 1 0-9 0V7H2a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2Zm-5 8a1 1 0 1 1-2 0v-3a1 1 0 1 1 2 0v3Z"/></svg>
                    </div>
                    <!-- Toggle Button -->
                    <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg id="icon-password" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    </button>
                </div>
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
                <div class="flex items-center h-5">
                    <input id="remember" name="remember" type="checkbox" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-red-300 text-[#EC1C25]">
                    <label for="remember" class="ms-2 text-sm font-medium text-gray-600">Ingat saya</label>
                </div>
                <a href="{{ route('password.request') }}" class="text-sm text-[#EC1C25] hover:underline font-semibold hover:text-[#c4161e] transition-colors">Lupa password?</a>
            </div>

            <!-- Button -->
            <button type="submit" class="w-full text-white bg-[#EC1C25] hover:bg-[#c0151d] focus:ring-4 focus:outline-none focus:ring-red-300 font-bold rounded-xl text-sm px-5 py-3.5 text-center shadow-lg shadow-red-500/30 transition-all transform hover:-translate-y-0.5 active:scale-95">
                Masuk Sekarang
            </button>

            <!-- Footer Link -->
            <div class="text-sm font-medium text-gray-500 text-center border-t border-gray-100 pt-6">
                Belum punya akun? <a href="{{ route('register') }}" class="text-[#EC1C25] hover:underline font-bold hover:text-[#c4161e]">Daftar disini</a>
            </div>
        </form>
    </div>
</div>

<script>
    function togglePassword(fieldId) {
        const input = document.getElementById(fieldId);
        const icon = document.getElementById('icon-' + fieldId);

        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
        } else {
            input.type = 'password';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
        }
    }
</script>
@endsection
