<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Seller Center - {{ config('app.name', 'Telu Consign') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8F9FA; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Sidebar Transition */
        .sidebar-transition { transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        
        /* Active State for Sidebar */
        .sidebar-item-active {
            background: linear-gradient(135deg, #EC1C25 0%, #a50f15 100%);
            color: white !important;
            box-shadow: 0 4px 12px rgba(236, 28, 37, 0.25);
        }
        .sidebar-item-active svg { color: white !important; }

        /* Utility Helpers for "Panze" look */
        .card-premium {
            background: white;
            border-radius: 1rem; /* rounded-2xl matches xl in tailwind usually */
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05); /* soft shadow */
            border: 1px solid rgba(0,0,0,0.03);
        }

        [x-cloak] { display: none !important; }
    </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 
                            50:'#fff1f2', 100:'#ffe4e6', 500:'#f43f5e', 
                            600:'#EC1C25', 700:'#b91c1c', 800:'#991b1b' 
                        },
                        telu: {
                            red: '#EC1C25',
                            dark: '#1e293b',
                            light: '#f8f9fa'
                        }
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                        'card': '0 0 0 1px rgba(0,0,0,0.03), 0 2px 8px rgba(0,0,0,0.04)',
                    },
                    borderRadius: {
                        'xl': '1rem',
                        '2xl': '1.5rem',
                    }
                }
            }
        }

        // Custom SweetAlert Mixin
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        const ConfirmSwal = Swal.mixin({
            customClass: {
                confirmButton: 'bg-telu-red text-white px-6 py-2.5 rounded-xl font-medium shadow-lg hover:shadow-red-500/30 transition-all duration-200 mx-2',
                cancelButton: 'bg-gray-100 text-gray-700 px-6 py-2.5 rounded-xl font-medium hover:bg-gray-200 transition-all duration-200 mx-2'
            },
            buttonsStyling: false,
            width: '32em',
            padding: '2em',
            background: '#fff',
            backdrop: `rgba(0,0,0,0.4)`
        });
    </script>
</head>
<body class="bg-[#F8F9FA] text-gray-600 antialiased" x-data="{ sidebarOpen: window.innerWidth >= 1024, mobileMenuOpen: false }">

    <!-- Mobile Overlay -->
    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileMenuOpen = false"
         class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm lg:hidden" x-cloak></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'w-72' : 'w-24'"
           class="fixed top-0 left-0 z-50 h-screen transition-all duration-300 ease-in-out bg-white border-r border-gray-100 hidden lg:block shadow-soft">
        
        <!-- Logo Area -->
        <div class="h-20 flex items-center justify-center border-b border-gray-50 bg-white/80 backdrop-blur-sm">
            <a href="{{ route('shop.index') }}" class="flex items-center gap-3 overflow-hidden px-4">
                <div class="bg-gradient-to-br from-telu-red to-red-600 text-white p-2.5 rounded-xl shadow-lg shadow-red-500/20 flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <div class="flex flex-col transition-opacity duration-300" x-show="sidebarOpen">
                    <span class="text-lg font-bold text-gray-900 leading-tight">SellerCenter</span>
                    <span class="text-xs font-medium text-gray-400">TeluConsign</span>
                </div>
            </a>
        </div>

        <!-- Navigation -->
        <div class="h-[calc(100vh-80px)] overflow-y-auto py-6 px-4 space-y-2">
            
            <!-- Dashboard -->
            <a href="{{ route('shop.index') }}" 
               class="flex items-center px-4 py-3.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('shop.index') ? 'sidebar-item-active' : 'text-gray-500 hover:bg-red-50 hover:text-telu-red' }}">
                <svg class="w-6 h-6 flex-shrink-0 transition-colors {{ request()->routeIs('shop.index') ? 'text-white' : 'text-gray-400 group-hover:text-telu-red' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <span class="ml-3 font-medium whitespace-nowrap" x-show="sidebarOpen">Dashboard</span>
                <div class="absolute left-16 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 pointer-events-none transition-opacity group-hover:opacity-100 z-50 lg:hidden" x-show="!sidebarOpen">Dashboard</div>
            </a>

            <!-- Address (New) -->
            <a href="{{ route('shop.address.index') }}" 
               class="flex items-center px-4 py-3.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('shop.address*') ? 'sidebar-item-active' : 'text-gray-500 hover:bg-red-50 hover:text-telu-red' }}">
                <svg class="w-6 h-6 flex-shrink-0 transition-colors {{ request()->routeIs('shop.address*') ? 'text-white' : 'text-gray-400 group-hover:text-telu-red' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span class="ml-3 font-medium whitespace-nowrap" x-show="sidebarOpen">Alamat Toko</span>
                <div class="absolute left-16 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 pointer-events-none transition-opacity group-hover:opacity-100 z-50 lg:hidden" x-show="!sidebarOpen">Alamat</div>
            </a>

            <!-- Products -->
            <a href="{{ route('shop.products.index') }}" 
               class="flex items-center px-4 py-3.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('shop.products*') ? 'sidebar-item-active' : 'text-gray-500 hover:bg-red-50 hover:text-telu-red' }}">
                <svg class="w-6 h-6 flex-shrink-0 transition-colors {{ request()->routeIs('shop.products*') ? 'text-white' : 'text-gray-400 group-hover:text-telu-red' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                <span class="ml-3 font-medium whitespace-nowrap" x-show="sidebarOpen">Produk Saya</span>
                <div class="absolute left-16 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 pointer-events-none transition-opacity group-hover:opacity-100 z-50 lg:hidden" x-show="!sidebarOpen">Produk</div>
            </a>

            <!-- Orders -->
            <a href="{{ route('shop.orders') }}" 
               class="flex items-center justify-between px-4 py-3.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('shop.orders') ? 'sidebar-item-active' : 'text-gray-500 hover:bg-red-50 hover:text-telu-red' }}">
                <div class="flex items-center">
                    <svg class="w-6 h-6 flex-shrink-0 transition-colors {{ request()->routeIs('shop.orders') ? 'text-white' : 'text-gray-400 group-hover:text-telu-red' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    <span class="ml-3 font-medium whitespace-nowrap" x-show="sidebarOpen">Pesanan</span>
                </div>
                @if(\App\Models\Order::where('seller_id', Auth::id())->where('status', 'pending')->count() > 0)
                    <span class="flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-orange-500 rounded-full shadow-sm" x-show="sidebarOpen">
                        {{ \App\Models\Order::where('seller_id', Auth::id())->where('status', 'pending')->count() }}
                    </span>
                    <div class="w-2 h-2 rounded-full bg-orange-500 absolute top-2 right-2" x-show="!sidebarOpen"></div>
                @endif
                <div class="absolute left-16 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 pointer-events-none transition-opacity group-hover:opacity-100 z-50 lg:hidden" x-show="!sidebarOpen">Pesanan</div>
            </a>

            <!-- Reports -->
            <a href="{{ route('shop.reports') }}" 
               class="flex items-center px-4 py-3.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('shop.reports') ? 'sidebar-item-active' : 'text-gray-500 hover:bg-red-50 hover:text-telu-red' }}">
                <svg class="w-6 h-6 flex-shrink-0 transition-colors {{ request()->routeIs('shop.reports') ? 'text-white' : 'text-gray-400 group-hover:text-telu-red' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                <span class="ml-3 font-medium whitespace-nowrap" x-show="sidebarOpen">Laporan</span>
                <div class="absolute left-16 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 pointer-events-none transition-opacity group-hover:opacity-100 z-50 lg:hidden" x-show="!sidebarOpen">Laporan</div>
            </a>

            <!-- Payouts -->
            <a href="{{ route('shop.payouts') }}" 
               class="flex items-center px-4 py-3.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('shop.payouts') ? 'sidebar-item-active' : 'text-gray-500 hover:bg-red-50 hover:text-telu-red' }}">
                <svg class="w-6 h-6 flex-shrink-0 transition-colors {{ request()->routeIs('shop.payouts') ? 'text-white' : 'text-gray-400 group-hover:text-telu-red' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="ml-3 font-medium whitespace-nowrap" x-show="sidebarOpen">Saldo & Penarikan</span>
                <div class="absolute left-16 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 pointer-events-none transition-opacity group-hover:opacity-100 z-50 lg:hidden" x-show="!sidebarOpen">Saldo & Penarikan</div>
            </a>

            <!-- Divider -->
            <div class="my-4 border-t border-gray-100 mx-2"></div>
            
            <!-- Homepage Link -->
            <a href="{{ route('shop.show', Auth::user()->username ?? Auth::id()) }}" target="_blank"
               class="flex items-center px-4 py-3.5 rounded-xl transition-all duration-200 group text-gray-500 hover:bg-red-50 hover:text-telu-red">
                <svg class="w-6 h-6 flex-shrink-0 transition-colors text-gray-400 group-hover:text-telu-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                <span class="ml-3 font-medium whitespace-nowrap" x-show="sidebarOpen">Lihat Toko (Live)</span>
                 <div class="absolute left-16 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 pointer-events-none transition-opacity group-hover:opacity-100 z-50 lg:hidden" x-show="!sidebarOpen">Lihat Toko</div>
            </a>

        </div>
    </aside>

    <!-- Mobile Sidebar (Drawer) -->
    <aside class="fixed top-0 left-0 z-50 h-screen w-72 bg-white transition-transform duration-300 lg:hidden"
           :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'" x-cloak>
           
           <div class="h-20 flex items-center justify-between px-6 border-b border-gray-100">
               <span class="text-xl font-bold text-gray-900">SellerCenter</span>
               <button @click="mobileMenuOpen = false" class="text-gray-500 hover:text-red-500">
                   <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
               </button>
           </div>
           
           <div class="p-4 space-y-2">
               <!-- Mobile Links -->
                <a href="{{ route('shop.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('shop.index') ? 'bg-telu-red text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                    <span class="font-medium">Dashboard</span>
                </a>
                <a href="{{ route('shop.products.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('shop.products*') ? 'bg-telu-red text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                    <span class="font-medium">Produk</span>
                </a>
                <a href="{{ route('shop.orders') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('shop.orders') ? 'bg-telu-red text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                    <span class="font-medium">Pesanan</span>
                </a>
                <a href="{{ route('shop.reports') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('shop.reports') ? 'bg-telu-red text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                    <span class="font-medium">Laporan</span>
                </a>
                <a href="{{ route('shop.payouts') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('shop.payouts') ? 'bg-telu-red text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                    <span class="font-medium">Saldo & Penarikan</span>
                </a>
                <a href="{{ route('home') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-50">
                    <span class="font-medium">Lihat Toko</span>
                </a>
           </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div :class="sidebarOpen ? 'lg:ml-72' : 'lg:ml-24'" class="min-h-screen transition-all duration-300 ease-in-out p-6 pt-0">
        
        <!-- Top Navbar -->
        <header class="sticky top-0 z-30 flex items-center justify-between h-20 -mx-6 px-8 bg-[#F8F9FA]/80 backdrop-blur-md mb-6">
            
            <div class="flex items-center gap-4">
                <!-- Mobile Toggle -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 text-gray-500 rounded-lg lg:hidden hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                </button>

                <!-- Desktop Toggle -->
                <button @click="sidebarOpen = !sidebarOpen" class="hidden lg:flex items-center justify-center p-2 text-gray-500 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                </button>

                <!-- Title -->
                <h1 class="text-xl font-bold text-gray-800 hidden sm:block">Seller Dashboard</h1>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-4">
                <!-- User Menu -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-3 focus:outline-none">
                        <div class="text-right hidden sm:block">
                            <div class="text-sm font-bold text-gray-900">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-gray-500">Seller</div>
                        </div>
                        <img class="w-10 h-10 rounded-full border-2 border-white shadow-sm object-cover" src="{{ Auth::user()->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=EC1C25&color=fff' }}" alt="user photo">
                    </button>
                    
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         class="absolute right-0 mt-3 w-48 bg-white rounded-xl shadow-lg py-2 border border-gray-100 origin-top-right ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                        <div class="px-4 py-2 border-b border-gray-50">
                             <p class="text-sm font-bold text-gray-900">{{ Auth::user()->name }}</p>
                             <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <a href="{{ route('home') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Back to Website</a>
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100 mt-2 pt-1">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-medium">Log Out</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
        
        <!-- Footer -->
        <footer class="mt-12 text-center text-sm text-gray-400 pb-6">
            &copy; {{ date('Y') }} TeluConsign Seller Center. All rights reserved.
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Success Notification
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'bg-telu-red px-6 py-2.5 rounded-xl text-white font-semibold shadow-lg shadow-red-500/30 border-none outline-none focus:outline-none'
                    },
                    buttonsStyling: false
                });
            @endif

            // Error Notification
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                    confirmButtonText: 'Tutup',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'bg-gray-800 px-6 py-2.5 rounded-xl text-white font-semibold shadow-lg border-none outline-none focus:outline-none'
                    },
                    buttonsStyling: false
                });
            @endif

            // Confirmation Delete Helper
            window.confirmDelete = function(formId) {
                ConfirmSwal.fire({
                    title: 'Apakah anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(formId).submit();
                    }
                });
            }
        });
    </script>
</body>
</html>
