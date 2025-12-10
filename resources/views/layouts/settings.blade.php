@extends('layouts.app')

@section('content')
<div class="flex min-h-screen w-full">

    <aside class="w-72 bg-white border-r border-gray-200 flex-shrink-0 hidden lg:block min-h-screen">
        <div class="h-full flex flex-col">
            <div class="h-16 flex items-center px-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-[var(--tc-text-main)]">Pengaturan</h2>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2">
                {{-- 1. PROFIL SAYA --}}
                <a href="{{ route('profile.index') }}"
                   class="flex items-center px-4 py-3.5 rounded-lg transition-all font-medium text-sm
                   {{ request()->routeIs('profile.*')
                      ? 'bg-[var(--tc-btn-bg)] text-white shadow-md'
                      : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Profil Saya
                </a>

                {{-- 2. PRODUK SAYA (BARU) --}}
                <a href="{{ route('products.index') }}"
                   class="flex items-center px-4 py-3.5 rounded-lg transition-all font-medium text-sm
                   {{ request()->routeIs('products.*')
                      ? 'bg-[var(--tc-btn-bg)] text-white shadow-md'
                      : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    {{-- Icon Box --}}
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Produk Saya
                </a>

                {{-- 3. BUKU ALAMAT --}}
                <a href="{{ route('address.index') }}"
                   class="flex items-center px-4 py-3.5 rounded-lg transition-all font-medium text-sm
                   {{ request()->routeIs('address.*')
                      ? 'bg-[var(--tc-btn-bg)] text-white shadow-md'
                      : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Buku Alamat
                </a>

                {{-- 4. REKENING BANK --}}
                <a href="{{ route('bank.index') }}"
                   class="flex items-center px-4 py-3.5 rounded-lg transition-all font-medium text-sm
                   {{ request()->routeIs('bank.*')
                      ? 'bg-[var(--tc-btn-bg)] text-white shadow-md'
                      : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    Rekening Bank
                </a>

                {{-- 5. KATEGORI --}}
                <a href="{{ route('categories.index') }}"
                   class="flex items-center px-4 py-3.5 rounded-lg transition-all font-medium text-sm
                   {{ request()->routeIs('categories.*')
                      ? 'bg-[var(--tc-btn-bg)] text-white shadow-md'
                      : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    Kategori Produk
                </a>
            </nav>

            <div class="p-4 border-t border-gray-100">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-3 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 transition-colors font-medium text-sm">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Keluar Aplikasi
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <main class="flex-1 p-6 lg:p-10 overflow-y-auto h-screen">
        <div class="max-w-4xl mx-auto">
            @yield('settings_content')
        </div>
    </main>
</div>
@endsection
