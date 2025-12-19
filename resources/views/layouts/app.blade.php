<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Telu Consign') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .bg-telu-red { background-color: #EC1C25; }
        .text-telu-red { color: #EC1C25; }
        .border-telu-red { border-color: #EC1C25; }
        .hover-text-telu:hover { color: #EC1C25; }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Animation Utilities */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translate3d(0, -10px, 0); }
            to { opacity: 1; transform: translate3d(0, 0, 0); }
        }
        .animate-fade-in-down { animation: fadeInDown 0.3s ease-out; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translate3d(0, 10px, 0); }
            to { opacity: 1; transform: translate3d(0, 0, 0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.4s ease-out; }
    </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#fff1f2', 100:'#ffe4e6', 500:'#f43f5e', 600:'#e11d48', 700:'#EC1C25', 800:'#9f1239' }
                    }
                }
            }
        }
    </script>
</head>
<body class="flex flex-col min-h-screen text-gray-800">

    <!-- Navbar Fixed -->
    <nav class="bg-white border-b border-gray-200 fixed w-full z-50 top-0 start-0 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 md:h-20 gap-4">

                <!-- 1. Logo (Kiri) -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                        <!-- Icon Logo -->
                        <div class="bg-[#EC1C25] text-white p-1.5 rounded-lg group-hover:scale-105 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                        <span class="self-center text-2xl font-extrabold whitespace-nowrap text-[#EC1C25] tracking-tight hidden md:block">
                            TEL-U<span class="text-gray-900">CONSIGN</span>
                        </span>
                    </a>
                </div>

                <!-- 2. Search Bar (Tengah - Hanya tampil jika bukan di halaman auth) -->
                @if(!request()->routeIs('login') && !request()->routeIs('register'))
                <div class="flex-grow max-w-2xl mx-4 hidden md:block">
                    <form action="{{ route('home') }}" method="GET" class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </div>
                        <input type="search" name="q" id="default-search"
                               class="block w-full p-3 ps-11 text-sm text-gray-900 border border-gray-300 rounded-full bg-gray-50 focus:ring-[#EC1C25] focus:border-[#EC1C25] placeholder-gray-400 focus:bg-white transition-all shadow-sm focus:shadow-md"
                               placeholder="Cari barang di sekitar Telkom University..." required>
                    </form>
                </div>
                @endif

                <!-- 3. Actions Icons (Kanan) -->
                <div class="flex items-center gap-2 sm:gap-4">

                    @auth
                        <!-- Jika Login: Tampilkan Cart & Profile -->
                        <a href="{{ route('cart.index') }}" class="p-2 text-gray-500 hover:text-[#EC1C25] hover:bg-red-50 rounded-full transition-all relative group">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            
                            @php
                                $cartCount = 0;
                                if(auth()->check()) {
                                    $cart = \App\Models\Cart::where('buyer_id', auth()->id())->first();
                                    if($cart) {
                                        $cartCount = $cart->items()->count();
                                    }
                                }
                            @endphp
                            
                            @if($cartCount > 0)
                                <div class="absolute -top-1 -right-1 bg-[#EC1C25] text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full border-2 border-white shadow-sm">
                                    {{ $cartCount > 99 ? '99+' : $cartCount }}
                                </div>
                            @endif
                        </a>

                        <div class="h-8 w-px bg-gray-200 mx-1 hidden md:block"></div>

                        <!-- User Profile Dropdown -->
                        <button type="button" class="flex items-center gap-3 text-sm rounded-full md:me-0 focus:ring-4 focus:ring-gray-100 transition-all p-1 pr-3 border border-transparent hover:border-gray-200 hover:bg-gray-50" id="user-menu-button" aria-expanded="false" data-dropdown-toggle="user-dropdown" data-dropdown-placement="bottom-end">
                            <img class="w-9 h-9 rounded-full object-cover border border-gray-200"
                                 src="{{ Auth::user()->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=EC1C25&color=fff' }}"
                                 alt="user photo">
                            <div class="hidden md:block text-left">
                                <span class="block text-sm font-semibold text-gray-900 truncate max-w-[100px]">{{ Str::limit(Auth::user()->name, 12) }}</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <!-- Isian Dropdown Menu -->
                        <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-xl shadow-xl w-64 border border-gray-100 animate-fade-in-down" id="user-dropdown">
                            <!-- Header Dropdown -->
                            <div class="px-4 py-4 bg-gray-50 rounded-t-xl">
                                <p class="text-sm font-medium text-gray-500">Halo,</p>
                                <p class="text-sm font-bold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            <div class="py-2">
                                <a href="{{ route('profile.index') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#EC1C25] transition-colors gap-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Profil & Alamat
                                </a>
                                <a href="{{ route('shop.index') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#EC1C25] transition-colors gap-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    Toko Saya
                                </a>
                            </div>
                            <div class="py-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors gap-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>

                    @else
                        <!-- Jika Belum Login (Guest) -->
                        @if(!request()->routeIs('login') && !request()->routeIs('register') && !request()->routeIs('password.*'))
                            <div class="flex items-center gap-3">
                                <a href="{{ route('login') }}" class="text-gray-700 hover:text-[#EC1C25] font-semibold text-sm px-4 py-2 transition-colors">
                                    Masuk
                                </a>
                                <a href="{{ route('register') }}" class="text-white bg-[#EC1C25] hover:bg-[#c4161e] focus:ring-4 focus:ring-red-100 font-medium rounded-full text-sm px-5 py-2.5 shadow-md hover:shadow-lg transition-all">
                                    Daftar
                                </a>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Content Wrapper -->
    <main class="flex-grow {{ !request()->routeIs('login') && !request()->routeIs('register') && !request()->routeIs('password.*') && !request()->routeIs('otp.*') ? 'pt-20 md:pt-24 pb-12' : 'pt-0' }}">
        @yield('content')
    </main>

    <!-- Footer Simple -->
    @if(!request()->routeIs('login') && !request()->routeIs('register') && !request()->routeIs('password.*') && !request()->routeIs('otp.*'))
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-1">
                    <span class="text-2xl font-extrabold text-[#EC1C25] tracking-tight">TEL-U<span class="text-gray-900">CONSIGN</span></span>
                    <p class="text-gray-500 text-sm mt-4 leading-relaxed">
                        Platform jual beli aman dan terpercaya khusus untuk mahasiswa dan civitas akademika Telkom University.
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Belanja</h3>
                    <ul class="space-y-3 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-[#EC1C25] transition-colors">Kategori</a></li>
                        <li><a href="#" class="hover:text-[#EC1C25] transition-colors">Cara Belanja</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Berjualan</h3>
                    <ul class="space-y-3 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-[#EC1C25] transition-colors">Mulai Berjualan</a></li>
                        <li><a href="#" class="hover:text-[#EC1C25] transition-colors">Ketentuan Seller</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Bantuan</h3>
                    <ul class="space-y-3 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-[#EC1C25] transition-colors">Hubungi Kami</a></li>
                        <li><a href="#" class="hover:text-[#EC1C25] transition-colors">FAQ</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-100 mt-12 pt-8 text-center text-sm text-gray-400">
                &copy; {{ date('Y') }} Tel-U Consign. Developed for Telkom University.
            </div>
        </div>
    </footer>
    @endif

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

    <!-- Configured SweetAlert2 -->
    <script>
        // Custom Styled SweetAlert Mixin
        const TeluSwal = Swal.mixin({
            customClass: {
                popup: 'rounded-2xl shadow-xl border border-gray-100',
                title: 'text-xl font-bold text-gray-900',
                confirmButton: 'bg-[#EC1C25] text-white px-5 py-2.5 rounded-xl font-bold hover:bg-[#c4161e] focus:ring-4 focus:ring-red-100 transition-all shadow-md mx-2',
                cancelButton: 'bg-gray-100 text-gray-800 px-5 py-2.5 rounded-xl font-medium hover:bg-gray-200 focus:ring-4 focus:ring-gray-100 transition-all mx-2',
                actions: 'flex gap-2 justify-center mt-4'
            },
            buttonsStyling: false // Penting: Matikan style default agar Tailwind jalan
        });

        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                TeluSwal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if(session('error'))
                TeluSwal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "{{ session('error') }}"
                });
            @endif

            @if(session('warning'))
                TeluSwal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: "{{ session('warning') }}"
                });
            @endif

            @if(session('info'))
                TeluSwal.fire({
                    icon: 'info',
                    title: 'Informasi',
                    text: "{{ session('info') }}"
                });
            @endif
        });

        // Global Delete Confirmation Helper
        window.confirmDelete = function(formId, itemName) {
            TeluSwal.fire({
                title: 'Hapus ' + itemName + '?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>
</body>
</html>
