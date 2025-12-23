@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4 bg-gray-50/50">
    <div class="w-full max-w-5xl bg-white shadow-2xl rounded-3xl overflow-hidden flex flex-col md:flex-row animate-fade-in-up">
        
        <!-- Left Side: Brand/Image -->
        <div class="hidden md:flex md:w-1/2 bg-[#EC1C25] text-white p-12 flex-col justify-between relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?q=80&w=2070&auto=format&fit=crop')] bg-cover bg-center opacity-20 hover:scale-105 transition-transform duration-10000"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-8">
                     <div class="bg-white/20 backdrop-blur-md p-2 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                     </div>
                     <span class="text-2xl font-bold tracking-tight">TeluConsign</span>
                </div>
                <h2 class="text-4xl font-extrabold leading-tight mb-6">Nikmati pengalaman belanja terbaik.</h2>
                <p class="text-white/80 text-lg leading-relaxed">Temukan ribuan barang berkualitas dengan harga terbaik hanya di TeluConsign.</p>
            </div>
            <div class="relative z-10 mt-auto">
                <div class="text-sm font-medium text-white/60">
                    <span>&copy; 2025 TeluConsign</span>
                </div>
            </div>
        </div>

        <!-- Right Side: Form -->
        <div class="w-full md:w-1/2 p-8 md:p-12 lg:p-16 bg-white relative">
            <div class="max-w-md mx-auto">
                <div class="text-center mb-10">
                    <h3 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang! 👋</h3>
                    <p class="text-gray-500">Masuk untuk mulai menjelajahi toko.</p>
                </div>

                <form class="space-y-6" action="{{ route('login.submit') }}" method="POST">
                    @csrf

                    <!-- Email/Username -->
                    <div class="space-y-2">
                        <label for="login" class="text-sm font-bold text-gray-700 ml-1">Email atau Username</label>
                        <div class="relative">
                            <input type="text" name="login" id="login" value="{{ old('login') }}" 
                                   class="w-full px-5 py-4 pl-12 rounded-xl font-medium bg-gray-50 border border-gray-200 placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#EC1C25] focus:border-transparent transition-all duration-300 focus:bg-white" 
                                   placeholder="Masukan email atau username" required autofocus>
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                        </div>
                        @error('login') <p class="text-red-500 text-xs font-bold pl-1 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <div class="flex justify-between items-center ml-1">
                            <label for="password" class="text-sm font-bold text-gray-700">Kata Sandi</label>
                            <a href="{{ route('password.request') }}" class="text-xs font-bold text-[#EC1C25] hover:text-[#c4161e] hover:underline">Lupa Password?</a>
                        </div>
                        <div class="relative">
                            <input type="password" name="password" id="password" 
                                   class="w-full px-5 py-4 pl-12 pr-12 rounded-xl font-medium bg-gray-50 border border-gray-200 placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#EC1C25] focus:border-transparent transition-all duration-300 focus:bg-white" 
                                   placeholder="••••••••" required>
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition-colors focus:outline-none">
                                <svg id="icon-password" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </button>
                        </div>
                        @error('password') <p class="text-red-500 text-xs font-bold pl-1 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center">
                        <label class="inline-flex items-center cursor-pointer group">
                            <input id="remember" name="remember" type="checkbox" class="rounded border-gray-300 text-[#EC1C25] shadow-sm focus:border-[#EC1C25] focus:ring focus:ring-red-200 focus:ring-opacity-50 cursor-pointer">
                            <span class="ml-2 text-sm text-gray-600 group-hover:text-gray-900 transition-colors">Ingat saya</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-[#EC1C25] hover:bg-[#c4161e] text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-red-500/30 transition-all transform hover:-translate-y-0.5 active:scale-95 text-sm uppercase tracking-wider">
                        Masuk Sekarang
                    </button>

                    <div class="relative my-8">
                        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                        <div class="relative flex justify-center text-sm"><span class="px-2 bg-white text-gray-500">Atau</span></div>
                    </div>

                    <div class="text-center">
                        <p class="text-sm text-gray-600">Belum memiliki akun? 
                            <a href="{{ route('register') }}" class="font-bold text-[#EC1C25] hover:text-[#c4161e] hover:underline transition-all">Daftar sekarang</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
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
