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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8F9FA; color: #1e293b; }
        .font-inter { font-family: 'Inter', sans-serif; }
        
        /* Modern Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Smooth Transitions */
        a, button, input { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }

        /* Glassmorphism Utilities */
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255, 255, 255, 0.3); }
        
        /* Custom Utilities */
        .text-balanced { text-wrap: balance; }
        .shadow-soft { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02); }
        .shadow-card { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.03), 0 4px 6px -2px rgba(0, 0, 0, 0.01); }
        .input-modern { @apply bg-white border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-[#EC1C25]/5 focus:border-[#EC1C25] block w-full px-4 py-3 transition-all duration-200 outline-none; }
    </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        inter: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: { 50:'#fff1f2', 100:'#ffe4e6', 500:'#f43f5e', 600:'#e11d48', 700:'#EC1C25', 800:'#9f1239' },
                        secondary: { 50: '#f8fafc', 100: '#f1f5f9', 900: '#0f172a' }
                    },
                    borderRadius: {
                        'xl': '0.75rem',
                        '2xl': '1rem',
                        '3xl': '1.5rem',
                    },
                    boxShadow: {
                        'glow': '0 0 15px rgba(236, 28, 37, 0.15)',
                    }
                }
            }
        }
        }
    </script>
    <script>
        console.log('DEBUG: Auth ID = {{ Auth::id() }}');
        console.log('DEBUG: Auth Check = {{ Auth::check() ? "TRUE" : "FALSE" }}');
    </script>
</head>
<body class="flex flex-col min-h-screen text-gray-800">

    <!-- Navbar Fixed -->
    <nav class="bg-white/80 backdrop-blur-md border-b border-gray-100 fixed w-full z-50 top-0 start-0 shadow-sm transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 md:h-20 gap-6">

                <!-- 1. Logo (Kiri) -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                        <!-- Icon Logo -->
                        <div class="bg-gradient-to-br from-[#EC1C25] to-[#b9151c] text-white p-2 rounded-xl shadow-lg shadow-red-200 group-hover:scale-105 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                        <span class="self-center text-2xl font-bold whitespace-nowrap text-gray-900 tracking-tight group-hover:text-[#EC1C25] transition-colors duration-300 hidden md:block">
                            TeluConsign
                        </span>
                    </a>
                </div>

                <!-- 2. Search Bar (Tengah - Hanya tampil jika bukan di halaman auth) -->
                @if(!request()->routeIs('login') && !request()->routeIs('register'))
                <div class="flex-grow max-w-2xl hidden md:block">
                    <form action="{{ route('search.index') }}" method="GET" class="relative group">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400 group-focus-within:text-[#EC1C25] transition-colors" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </div>
                        <input type="search" name="search" id="default-search"
                               class="block w-full p-3 ps-12 text-sm text-gray-900 border border-gray-200 rounded-full bg-gray-50/50 focus:ring-2 focus:ring-[#EC1C25]/20 focus:border-[#EC1C25] placeholder-gray-400 focus:bg-white transition-all shadow-sm focus:shadow-md"
                               placeholder="Cari barang apa hari ini?" required>
                    </form>
                </div>
                @endif

                <!-- 3. Actions Icons (Kanan) -->
                <div class="flex items-center gap-3 sm:gap-4">

                    @auth
                        <!-- Jika Login: Tampilkan Cart & Profile -->
                        <a href="{{ route('cart.index') }}" class="p-2.5 text-gray-500 hover:text-[#EC1C25] hover:bg-red-50 rounded-xl transition-all relative group">
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
                                <div class="absolute -top-1 -right-1 bg-[#EC1C25] text-white text-[10px] font-bold min-w-[20px] h-5 flex items-center justify-center rounded-full border-2 border-white shadow-sm px-1">
                                    {{ $cartCount > 99 ? '99+' : $cartCount }}
                                </div>
                            @endif
                        </a>

                        <div class="h-8 w-px bg-gray-200 hidden md:block"></div>

                        <!-- User Profile Dropdown (Fixed relative positioning) -->
                        <div class="relative ml-3">
                            <button type="button" class="flex items-center gap-3 text-sm rounded-full md:me-0 focus:ring-4 focus:ring-gray-100 transition-all p-1 pr-3 border border-transparent hover:border-gray-200 hover:bg-gray-50" id="user-menu-button" aria-expanded="false">
                                <img class="w-9 h-9 rounded-full object-cover border border-gray-200 shadow-sm"
                                     src="{{ Auth::user()->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=EC1C25&color=fff' }}"
                                     alt="user photo">
                                <div class="hidden md:block text-left">
                                    <span class="block text-sm font-bold text-gray-900 truncate max-w-[100px]">{{ Str::limit(Auth::user()->name, 12) }}</span>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 hidden md:block transition-transform duration-200" id="dropdown-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>

                            <!-- Isian Dropdown Menu -->
                            <div class="absolute right-0 top-full mt-2 z-50 hidden text-base list-none bg-white divide-y divide-gray-100 rounded-2xl shadow-xl w-72 border border-gray-100 ring-1 ring-black ring-opacity-5 animate-fade-in-up origin-top-right" id="user-dropdown">
                                <!-- Header Dropdown -->
                                <div class="px-5 py-5 bg-gradient-to-br from-gray-50 to-white rounded-t-2xl border-b border-gray-100">
                                    <div class="flex items-center gap-3">
                                        <img class="w-10 h-10 rounded-full object-cover border border-gray-200" src="{{ Auth::user()->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=EC1C25&color=fff' }}">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                            <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-2">
                                    <a href="{{ route('profile.index') }}" class="flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 rounded-xl hover:bg-red-50 hover:text-[#EC1C25] transition-all gap-3">
                                        <svg class="w-5 h-5 text-gray-400 group-hover:text-[#EC1C25]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        Profil Saya
                                    </a>

                                    <!-- Logic Toko Saya / Buka Toko -->
                                    <a href="{{ route('shop.index') }}" class="flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 rounded-xl hover:bg-red-50 hover:text-[#EC1C25] transition-all gap-3">
                                        <svg class="w-5 h-5 text-gray-400 group-hover:text-[#EC1C25]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        {{ Auth::user()->role === 'seller' ? 'Toko Saya' : 'Buka Toko' }}
                                    </a>

                                    <a href="{{ route('orders.index') }}" class="flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 rounded-xl hover:bg-red-50 hover:text-[#EC1C25] transition-all gap-3">
                                        <svg class="w-5 h-5 text-gray-400 group-hover:text-[#EC1C25]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                        Riwayat Pembelian
                                    </a>
                                </div>
                                <div class="p-2">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex w-full items-center px-4 py-2.5 text-sm font-medium text-red-600 rounded-xl hover:bg-red-50 transition-all gap-3">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                            Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @else
                        <!-- Jika Belum Login (Guest) -->
                        @if(!request()->routeIs('login') && !request()->routeIs('register') && !request()->routeIs('password.*'))
                            <div class="flex items-center gap-2">
                                <a href="{{ route('login') }}" class="text-gray-700 hover:text-[#EC1C25] hover:bg-red-50 font-semibold text-sm px-5 py-2.5 rounded-full transition-all">
                                    Masuk
                                </a>
                                <a href="{{ route('register') }}" class="text-white bg-[#EC1C25] hover:bg-[#c4161e] focus:ring-4 focus:ring-red-100 font-bold rounded-full text-sm px-6 py-2.5 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
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
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 md:gap-12">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="bg-[#EC1C25] text-white p-1.5 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                        <span class="text-xl font-bold text-gray-900 tracking-tight">TeluConsign</span>
                    </div>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Platform jual beli aman dan terpercaya khusus untuk mahasiswa dan civitas akademika Telkom University.
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Belanja</h3>
                    <ul class="space-y-3 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-[#EC1C25] transition-colors">Semua Kategori</a></li>
                        <li><a href="#" class="hover:text-[#EC1C25] transition-colors">Produk Terbaru</a></li>
                        <li><a href="#" class="hover:text-[#EC1C25] transition-colors">Cara Belanja</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Berjualan</h3>
                    <ul class="space-y-3 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-[#EC1C25] transition-colors">Daftar Toko</a></li>
                        <li><a href="#" class="hover:text-[#EC1C25] transition-colors">Panduan Penjual</a></li>
                        <li><a href="#" class="hover:text-[#EC1C25] transition-colors">Kebijakan</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Dukungan</h3>
                    <ul class="space-y-3 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-[#EC1C25] transition-colors">Pusat Bantuan</a></li>
                        <li><a href="#" class="hover:text-[#EC1C25] transition-colors">Hubungi Kami</a></li>
                        <li><a href="#" class="hover:text-[#EC1C25] transition-colors">Tentang Kami</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-100 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-gray-400">
                <p>&copy; {{ date('Y') }} TeluConsign. All rights reserved.</p>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-[#EC1C25]">Privacy Policy</a>
                    <a href="#" class="hover:text-[#EC1C25]">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>
    @endif

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

    <!-- Configured SweetAlert2 -->
    <script>
        // ============================================
        // CUSTOM SWEETALERT2 CONFIGURATION
        // ============================================
        
        // Default Swal with Tel-U Consign Branding
        const Swal = window.Swal;
        
        // Set default configuration for ALL SweetAlerts
        Swal.mixin({
            customClass: {
                popup: 'rounded-2xl shadow-2xl border-0',
                title: 'text-2xl font-bold text-gray-800 mb-2',
                htmlContainer: 'text-gray-600 text-base leading-relaxed',
                confirmButton: 'swal-confirm-btn',
                cancelButton: 'swal-cancel-btn',
                actions: 'flex gap-3 justify-center mt-6'
            },
            buttonsStyling: false,
            showClass: {
                popup: 'animate__animated animate__fadeInDown animate__faster'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp animate__faster'
            }
        });

        // Custom Styled SweetAlert Mixin for Tel-U Consign
        const TeluSwal = Swal.mixin({
            customClass: {
                popup: 'rounded-2xl shadow-2xl border-0 overflow-hidden',
                title: 'text-2xl font-bold text-gray-900 mb-3 px-6 pt-6',
                htmlContainer: 'text-gray-600 text-base leading-relaxed px-6 pb-2',
                confirmButton: 'bg-[#EC1C25] hover:bg-[#c4161e] text-white font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 focus:ring-4 focus:ring-red-200 mx-2 min-w-[120px]',
                cancelButton: 'bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-xl shadow-md hover:shadow-lg transition-all focus:ring-4 focus:ring-gray-200 mx-2 min-w-[120px]',
                actions: 'flex gap-3 justify-center p-6 bg-gray-50'
            },
            buttonsStyling: false,
            showClass: {
                popup: 'animate__animated animate__zoomIn animate__faster'
            },
            hideClass: {
                popup: 'animate__animated animate__zoomOut animate__faster'
            },
            // Default button texts in Indonesian
            confirmButtonText: 'OK',
            cancelButtonText: 'Batal'
        });

        // Toast notification variant (top-right corner)


        // Custom CSS for SweetAlert2 buttons and toasts
        const style = document.createElement('style');
        style.textContent = `
            /* SweetAlert2 Custom Styling */
            .swal2-popup {
                font-family: 'Inter', sans-serif !important;
            }
            
            /* Icon styling */
            .swal2-icon {
                margin: 2rem auto 1rem !important;
                border-width: 3px !important;
            }
            
            .swal2-icon.swal2-success {
                border-color: #10b981 !important;
                color: #10b981 !important;
            }
            
            .swal2-icon.swal2-error {
                border-color: #ef4444 !important;
                color: #ef4444 !important;
            }
            
            .swal2-icon.swal2-warning {
                border-color: #f59e0b !important;
                color: #f59e0b !important;
            }
            
            .swal2-icon.swal2-info {
                border-color: #3b82f6 !important;
                color: #3b82f6 !important;
            }
            
            /* Success icon checkmark */
            .swal2-success-ring {
                border-color: rgba(16, 185, 129, 0.3) !important;
            }
            
            .swal2-success-line-tip,
            .swal2-success-line-long {
                background-color: #10b981 !important;
            }
            
            /* Error icon X */
            .swal2-x-mark-line-left,
            .swal2-x-mark-line-right {
                background-color: #ef4444 !important;
            }
            
            /* Warning icon ! */
            .swal2-icon.swal2-warning {
                border-color: #f59e0b !important;
                color: #f59e0b !important;
            }
            
            /* Timer progress bar */
            .swal2-timer-progress-bar {
                background: #EC1C25 !important;
            }
            
            /* Colored Toast Variants */
            .colored-toast.swal2-icon-success {
                background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
                color: white !important;
            }
            
            .colored-toast.swal2-icon-error {
                background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
                color: white !important;
            }
            
            .colored-toast.swal2-icon-warning {
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
                color: white !important;
            }
            
            .colored-toast.swal2-icon-info {
                background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
                color: white !important;
            }
            
            /* Input fields in SweetAlert */
            .swal2-input,
            .swal2-textarea {
                border: 2px solid #e5e7eb !important;
                border-radius: 0.75rem !important;
                padding: 0.75rem 1rem !important;
                font-size: 0.95rem !important;
                transition: all 0.2s !important;
            }
            
            .swal2-input:focus,
            .swal2-textarea:focus {
                border-color: #EC1C25 !important;
                box-shadow: 0 0 0 3px rgba(236, 28, 37, 0.1) !important;
                outline: none !important;
            }
            
            /* Validation message */
            .swal2-validation-message {
                background: #fef2f2 !important;
                border: 1px solid #fecaca !important;
                color: #dc2626 !important;
                border-radius: 0.5rem !important;
                padding: 0.5rem 1rem !important;
                font-size: 0.875rem !important;
            }
        `;
        document.head.appendChild(style);

        // ============================================
        // AUTO-SHOW ALERTS FROM SESSION
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                TeluSwal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    confirmButtonText: 'OK, Mengerti',
                    timer: 4000,
                    timerProgressBar: true
                });
            @endif

            @if(session('error'))
                TeluSwal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "{{ session('error') }}",
                    confirmButtonText: 'Tutup',
                    footer: '<p class="text-xs text-gray-400">Silakan periksa kembali dan coba lagi</p>'
                });
            @endif

            @if(session('warning'))
                TeluSwal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: "{{ session('warning') }}",
                    confirmButtonText: 'Saya Mengerti',
                });
            @endif

            @if(session('info'))
                TeluSwal.fire({
                    icon: 'info',
                    title: 'Informasi',
                    text: "{{ session('info') }}",
                    confirmButtonText: 'OK'
                });
            @endif
        });

        // ============================================
        // GLOBAL HELPER FUNCTIONS
        // ============================================
        
        // Delete Confirmation Helper
        window.confirmDelete = function(formId, itemName = 'item ini') {
            TeluSwal.fire({
                title: `Hapus ${itemName}?`,
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    TeluSwal.fire({
                        title: 'Menghapus...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    document.getElementById(formId).submit();
                }
            });
        }

        // Custom Dropdown Logic (Alternative to Flowbite)
        document.addEventListener('DOMContentLoaded', () => {
            const userBtn = document.getElementById('user-menu-button');
            const userDropdown = document.getElementById('user-dropdown');
            const arrow = document.getElementById('dropdown-arrow');

            if(userBtn && userDropdown) {
                userBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isHidden = userDropdown.classList.contains('hidden');
                    
                    if (isHidden) {
                        userDropdown.classList.remove('hidden');
                        userBtn.setAttribute('aria-expanded', 'true');
                        if(arrow) arrow.classList.add('rotate-180');
                    } else {
                        userDropdown.classList.add('hidden');
                        userBtn.setAttribute('aria-expanded', 'false');
                        if(arrow) arrow.classList.remove('rotate-180');
                    }
                });

                document.addEventListener('click', (e) => {
                    if(!userBtn.contains(e.target) && !userDropdown.contains(e.target)) {
                        userDropdown.classList.add('hidden');
                        userBtn.setAttribute('aria-expanded', 'false');
                        if(arrow) arrow.classList.remove('rotate-180');
                    }
                });
            }
        });

    </script>
    <!-- Global SweetAlert2 Configuration -->
    <script>
        // Global Mixin for consistent styling using Tailwind classes via customClass
        const SwalCustom = Swal.mixin({
            confirmButtonColor: '#EC1C25',
            cancelButtonColor: '#94a3b8',
            customClass: {
                container: 'font-sans',
                popup: 'rounded-2xl shadow-xl border border-gray-100',
                title: 'text-xl font-bold text-gray-900',
                htmlContainer: 'text-gray-600',
                confirmButton: 'px-5 py-2.5 rounded-xl text-sm font-bold bg-[#EC1C25] text-white hover:bg-[#c4161e] focus:ring-4 focus:ring-red-100 transition-all',
                cancelButton: 'px-5 py-2.5 rounded-xl text-sm font-bold bg-gray-100 text-gray-600 hover:bg-gray-200 focus:ring-4 focus:ring-gray-100 transition-all ml-2'
            },
            buttonsStyling: false,
            reverseButtons: true
        });

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            customClass: {
                popup: '!rounded-xl !bg-white !shadow-lg !border !border-gray-100'
            },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Flash Message Handler
        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        @endif

        @if(session('error'))
            Toast.fire({
                icon: 'error',
                title: "{{ session('error') }}"
            });
        @endif
    </script>
</body>
</html>
