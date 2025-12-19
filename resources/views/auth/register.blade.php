@extends('layouts.app')

@section('content')
<!-- Container Utama dengan min-h-screen agar background full tapi konten bisa di-scroll -->
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8 relative">

    <!-- Background -->
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat z-0 blur-[2px]"
         style="background-image: url('{{ asset('images/bg.png') }}'); opacity: 0.15;">
    </div>

    <!-- Card Container -->
    <div class="relative z-10 w-full max-w-md bg-white border border-gray-100 rounded-3xl shadow-2xl p-8 animate-fade-in-up">

        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-[#EC1C25] tracking-tight">
                Daftar Akun
            </h1>
            <p class="text-sm text-gray-500 mt-2 font-medium">Bergabung dengan Komunitas Tel-U Consign</p>
        </div>

        <form class="space-y-5" action="{{ route('register.submit') }}" method="POST">
            @csrf

            <!-- Nama Lengkap -->
            <div>
                <label for="name" class="block mb-1.5 text-xs font-bold text-gray-700 uppercase tracking-wide">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-3 transition-shadow focus:shadow-md" placeholder="John Doe" required>
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Username -->
            <div>
                <label for="username" class="block mb-1.5 text-xs font-bold text-gray-700 uppercase tracking-wide">Username</label>
                <input type="text" name="username" id="username" value="{{ old('username') }}" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-3 transition-shadow focus:shadow-md" placeholder="johndoe123" required>
                @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block mb-1.5 text-xs font-bold text-gray-700 uppercase tracking-wide">Email (SSO)</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-3 transition-shadow focus:shadow-md" placeholder="name@student.telkomuniversity.ac.id" required>
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- WhatsApp -->
            <div>
                <label for="phone" class="block mb-1.5 text-xs font-bold text-gray-700 uppercase tracking-wide">Nomor WhatsApp</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <input type="number" name="phone" id="phone" value="{{ old('phone') }}" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full ps-10 p-3 transition-shadow focus:shadow-md" placeholder="081234567890" required>
                </div>
                <p class="mt-1 text-[10px] text-gray-400">Kode OTP verifikasi akan dikirim ke nomor ini.</p>
                @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block mb-1.5 text-xs font-bold text-gray-700 uppercase tracking-wide">Kata Sandi</label>
                <div class="relative">
                    <input type="password" name="password" id="password" placeholder="••••••••" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-3 pr-10 transition-shadow focus:shadow-md" required>
                    <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg id="icon-password" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    </button>
                </div>
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block mb-1.5 text-xs font-bold text-gray-700 uppercase tracking-wide">Konfirmasi Kata Sandi</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="••••••••" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-3 pr-10 transition-shadow focus:shadow-md" required>
                    <button type="button" onclick="togglePassword('password_confirmation')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg id="icon-password_confirmation" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    </button>
                </div>
            </div>

            <!-- Button -->
            <div class="pt-4">
                <button type="submit" class="w-full text-white bg-[#EC1C25] hover:bg-[#c0151d] focus:ring-4 focus:outline-none focus:ring-red-300 font-bold rounded-xl text-sm px-5 py-3.5 text-center shadow-lg shadow-red-500/30 transition-all transform hover:-translate-y-0.5 active:scale-95">
                    Buat Akun Baru
                </button>
            </div>

            <!-- Footer Link -->
            <div class="text-sm font-medium text-gray-500 text-center border-t border-gray-100 pt-6">
                Sudah punya akun? <a href="{{ route('login') }}" class="text-[#EC1C25] hover:underline font-bold hover:text-[#c4161e]">Masuk disini</a>
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
