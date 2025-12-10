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

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Flowbite CDN (Untuk jaga-jaga jika npm belum setup sempurna) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />

    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-telu-red { background-color: #EC1C25; }
        .text-telu-red { color: #EC1C25; }
        .border-telu-red { border-color: #EC1C25; }
        .hover-bg-telu-red:hover { background-color: #c4161e; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 flex flex-col min-h-screen">

    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-200 fixed w-full z-50 top-0 start-0">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4 gap-4">

            <!-- 1. Logo -->
            <a href="{{ url('/') }}" class="flex items-center space-x-3 rtl:space-x-reverse">
                <!-- Ganti src dengan logo asli nanti -->
                <span class="self-center text-2xl font-bold whitespace-nowrap text-gray-800">LOGO</span>
            </a>

            <!-- 2. Search Bar (Lebar & Responsive) -->
            <div class="flex-grow order-last md:order-none w-full md:w-auto md:mx-8">
                <form action="#" method="GET" class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="search" id="default-search" class="block w-full p-3 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-[#EC1C25] focus:border-[#EC1C25]" placeholder="Apa yang ingin anda cari hari ini?" required>
                </form>
            </div>

            <!-- 3. Actions (Mail, Cart, Login) -->
            <div class="flex items-center space-x-3 md:space-x-6 rtl:space-x-reverse">

                <!-- Icon Email/Pesan -->
                <button type="button" class="relative inline-flex items-center text-sm font-medium text-center text-gray-500 hover:text-gray-900 focus:outline-none">
                    <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span class="sr-only">Notifications</span>
                </button>

                <!-- Icon Keranjang -->
                <a href="{{ route('cart.index') }}" class="relative inline-flex items-center text-sm font-medium text-center text-gray-500 hover:text-gray-900 focus:outline-none">
                    <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="sr-only">Cart</span>
                </a>

                <!-- Logic Login/Register vs User Profile -->
                @auth
                    <!-- Dropdown User jika sudah login -->
                    <button type="button" class="flex text-sm bg-gray-800 rounded-full md:me-0 focus:ring-4 focus:ring-gray-300" id="user-menu-button" aria-expanded="false" data-dropdown-toggle="user-dropdown" data-dropdown-placement="bottom">
                        <span class="sr-only">Open user menu</span>
                        <img class="w-8 h-8 rounded-full" src="{{ Auth::user()->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name) }}" alt="user photo">
                    </button>
                    <!-- Dropdown menu -->
                    <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow" id="user-dropdown">
                        <div class="px-4 py-3">
                            <span class="block text-sm text-gray-900">{{ Auth::user()->name }}</span>
                            <span class="block text-sm  text-gray-500 truncate">{{ Auth::user()->email }}</span>
                        </div>
                        <ul class="py-2" aria-labelledby="user-menu-button">
                            <li><a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a></li>
                            <li><a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Pesanan Saya</a></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Keluar</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <!-- Tombol Masuk jika belum login -->
                    <a href="{{ route('login') }}" class="text-white bg-telu-red hover-bg-telu-red focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-6 py-2 text-center">
                        Masuk
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow pt-24 pb-10">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-800 text-white py-10">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <h3 class="text-lg font-bold mb-4">Tentang Kami</h3>
                    <p class="text-gray-400 text-sm">Platform konsinyasi terpercaya di lingkungan Telkom University.</p>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Kontak</h3>
                    <ul class="text-gray-400 text-sm space-y-2">
                        <li>Gedung Bangkit Telkom University</li>
                        <li>info@telkomuniversity.ac.id</li>
                        <li>(022) 7566456</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Kerjasama</h3>
                    <ul class="text-gray-400 text-sm space-y-2">
                        <li>Bagian Kerjasama</li>
                        <li>Bagian Karir & Alumni</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Akademik</h3>
                    <ul class="text-gray-400 text-sm space-y-2">
                        <li>Open Library</li>
                        <li>iGracias</li>
                        <li>LMS</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 pt-6 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} Telkom University - Open Library. All rights reserved.
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
</body>
</html>
