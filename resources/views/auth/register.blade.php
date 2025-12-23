@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center pt-24 pb-12 px-4 bg-gray-50/50">
    
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat z-0 blur-[2px]"
         style="background-image: url('{{ asset('images/bg.png') }}'); opacity: 0.15;">
    </div>

    <div class="w-full max-w-6xl bg-white shadow-2xl rounded-3xl overflow-hidden flex flex-col md:flex-row-reverse animate-fade-in-up relative z-10">
        
        <div class="hidden md:flex md:w-1/2 bg-[#1e293b] text-white p-12 flex-col justify-between relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1555529733-0e670560f7e1?q=80&w=1974&auto=format&fit=crop')] bg-cover bg-center opacity-30 hover:scale-105 transition-transform duration-10000"></div>
            <div class="relative z-10 text-right">
                <div class="flex items-center gap-3 mb-8 justify-end">
                     <span class="text-2xl font-bold tracking-tight">TeluConsign</span>
                     <div class="bg-white/20 backdrop-blur-md p-2 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                     </div>
                </div>
                <h2 class="text-4xl font-extrabold leading-tight mb-6">Bergabung sekarang juga.</h2>
                <p class="text-white/80 text-lg leading-relaxed">Jual beli aman, nyaman, dan terpercaya bersama komunitas TeluConsign.</p>
            </div>
            <div class="relative z-10 mt-auto text-right">
                 <div class="flex items-center gap-4 justify-end text-sm font-medium text-white/60">
                    <span>&copy; 2025 TeluConsign</span>
                </div>
            </div>
        </div>

        <div class="w-full md:w-1/2 p-6 md:p-10 bg-white relative">
            <div class="max-w-md mx-auto">
                <div class="text-center mb-8">
                    <h3 class="text-3xl font-bold text-gray-900 mb-2">Buat Akun Baru 🚀</h3>
                    <p class="text-gray-500">Lengkapi data diri untuk memulai.</p>
                </div>

                <form class="space-y-3" action="{{ route('register.submit') }}" method="POST">
                    @csrf

                    <div class="space-y-1">
                        <label for="name" class="text-sm font-bold text-gray-700 ml-1">Nama Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" 
                               class="w-full px-5 py-3 rounded-xl font-medium bg-gray-50 border border-gray-200 placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#EC1C25] focus:border-transparent transition-all duration-300 focus:bg-white" 
                               placeholder="Nama lengkap Anda" required autofocus>
                        @error('name') <p class="text-red-500 text-xs font-bold pl-1 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="username" class="text-sm font-bold text-gray-700 ml-1">Username</label>
                        <input type="text" name="username" id="username" value="{{ old('username') }}" 
                               class="w-full px-5 py-3 rounded-xl font-medium bg-gray-50 border border-gray-200 placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#EC1C25] focus:border-transparent transition-all duration-300 focus:bg-white" 
                               placeholder="Username unik" required>
                        @error('username') <p class="text-red-500 text-xs font-bold pl-1 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="email" class="text-sm font-bold text-gray-700 ml-1">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" 
                               class="w-full px-5 py-3 rounded-xl font-medium bg-gray-50 border border-gray-200 placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#EC1C25] focus:border-transparent transition-all duration-300 focus:bg-white" 
                               placeholder="contoh@telkomuniversity.ac.id" required>
                        @error('email') <p class="text-red-500 text-xs font-bold pl-1 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="phone" class="text-sm font-bold text-gray-700 ml-1">Nomor WhatsApp</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-500 text-sm font-bold bg-transparent pointer-events-none">+62</span>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" 
                                   class="w-full pl-14 pr-5 py-3 rounded-xl font-medium bg-gray-50 border border-gray-200 placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#EC1C25] focus:border-transparent transition-all duration-300 focus:bg-white" 
                                   placeholder="81234567890" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                        </div>
                        @error('phone') <p class="text-red-500 text-xs font-bold pl-1 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="password" class="text-sm font-bold text-gray-700 ml-1">Kata Sandi</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" 
                                   class="w-full px-5 py-3 pr-12 rounded-xl font-medium bg-gray-50 border border-gray-200 placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#EC1C25] focus:border-transparent transition-all duration-300 focus:bg-white" 
                                   placeholder="Min. 8 karakter, Huruf Besar, Kecil & Angka" required>
                            <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition-colors focus:outline-none">
                                <svg id="icon-password" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </button>
                        </div>
                        @error('password') <p class="text-red-500 text-xs font-bold pl-1 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="password_confirmation" class="text-sm font-bold text-gray-700 ml-1">Konfirmasi Kata Sandi</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                   class="w-full px-5 py-3 pr-12 rounded-xl font-medium bg-gray-50 border border-gray-200 placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#EC1C25] focus:border-transparent transition-all duration-300 focus:bg-white" 
                                   placeholder="Ulangi kata sandi Anda" required>
                            <button type="button" onclick="togglePassword('password_confirmation')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition-colors focus:outline-none">
                                <svg id="icon-password_confirmation" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-start pt-2">
                        <div class="flex items-center h-5">
                            <input id="terms" name="terms" type="checkbox" class="w-4 h-4 rounded border-gray-300 text-[#EC1C25] focus:ring-[#EC1C25]" required>
                        </div>
                        <label for="terms" class="ml-2 text-sm text-gray-600">
                            Saya menyetujui <a href="#" class="text-[#EC1C25] hover:underline font-medium">Syarat & Ketentuan</a> TeluConsign.
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-[#EC1C25] hover:bg-[#c4161e] text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-red-500/30 transition-all transform hover:-translate-y-0.5 active:scale-95 text-sm uppercase tracking-wider">
                        Daftar Sekarang
                    </button>

                    <div class="text-center pt-4">
                        <p class="text-sm text-gray-600">Sudah punya akun? 
                            <a href="{{ route('login') }}" class="font-bold text-[#EC1C25] hover:text-[#c4161e] hover:underline transition-all">Masuk disini</a>
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