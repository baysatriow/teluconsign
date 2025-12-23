@extends('layouts.app')

@section('content')
<div class="bg-white min-h-screen">
    
    <!-- Hero Section -->
    <div class="relative bg-gray-900 overflow-hidden">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1555529733-0e670560f7e1?q=80&w=2074&auto=format&fit=crop" class="w-full h-full object-cover opacity-40">
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/40 to-transparent"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
            <div class="max-w-3xl animate-fade-in-up">
                <h1 class="text-4xl md:text-6xl font-extrabold text-white tracking-tight leading-tight mb-6">
                    Belanja Aman & Hemat <br>
                    <span class="text-[#EC1C25]">Komunitas Telkom University</span>
                </h1>
                <p class="text-lg md:text-xl text-gray-300 mb-8 leading-relaxed max-w-2xl">
                    Platform jual beli barang pre-loved dan baru khusus mahasiswa. Temukan kebutuhan kuliah, gadget, hingga fashion dengan harga terbaik.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('search.index') }}" class="px-8 py-4 bg-[#EC1C25] hover:bg-[#c4161e] text-white font-bold rounded-xl shadow-lg hover:shadow-red-500/30 transition-all transform hover:-translate-y-1 text-center">
                        Mulai Belanja
                    </a>
                    <a href="{{ route('shop.products.create') }}" class="px-8 py-4 bg-white/10 hover:bg-white/20 backdrop-blur-md text-white border border-white/30 font-bold rounded-xl transition-all text-center">
                        Jual Barang
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 relative z-10">
        <!-- Stats / Features -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-20">
            <div class="bg-white p-6 rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-50 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-[#EC1C25]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Terverifikasi</h3>
                    <p class="text-sm text-gray-500">Khusus mahasiswa aktif</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-50 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-[#EC1C25]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Harga Terjangkau</h3>
                    <p class="text-sm text-gray-500">Sesuai kantong mahasiswa</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-50 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-[#EC1C25]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Transaksi Cepat</h3>
                    <p class="text-sm text-gray-500">COD lingkungan kampus</p>
                </div>
            </div>
        </div>

        <!-- Categories -->
        <div class="mb-20">
            <div class="flex items-end justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Kategori Pilihan</h2>
                    <p class="text-gray-500 mt-2">Cari barang berdasarkan kategori populer</p>
                </div>
                <a href="{{ route('search.index') }}" class="hidden md:flex items-center gap-2 text-[#EC1C25] font-bold hover:underline">
                    Lihat Semua
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
                @php
                    $categoryIcons = [
                        'pakaian' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h8a4 4 0 004-4V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4z"/>',
                        'aksesoris' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>',
                        'elektronik' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>',
                        'perabotan' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
                        'otomotif' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>',
                    ];
                @endphp
                
                @foreach($categories as $category)
                <a href="{{ route('search.index', ['category' => $category->slug]) }}" class="group relative bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl"></div>
                    <div class="relative z-10 flex flex-col items-center text-center">
                        <div class="w-16 h-16 rounded-2xl bg-gray-50 text-gray-500 group-hover:bg-[#EC1C25] group-hover:text-white flex items-center justify-center mb-4 transition-colors duration-300 shadow-sm">
                             <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {!! $categoryIcons[strtolower($category->slug)] ?? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>' !!}
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 group-hover:text-[#EC1C25] transition-colors">{{ $category->name }}</h3>
                        <p class="text-xs text-gray-400 mt-1">{{ $category->products_count }} Produk</p>
                    </div>
                </a>
                @endforeach
            </div>
             <div class="mt-6 text-center md:hidden">
                <a href="{{ route('search.index') }}" class="text-[#EC1C25] font-bold text-sm hover:underline">Lihat Semua Kategori</a>
            </div>
        </div>

        <!-- Latest Products -->
        <div class="mb-20">
             <div class="flex items-end justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Baru Upload 🔥</h2>
                    <p class="text-gray-500 mt-2">Produk terbaru yang baru saja ditambahkan</p>
                </div>
                 <a href="{{ route('search.index', ['sort' => 'latest']) }}" class="hidden md:flex items-center gap-2 text-[#EC1C25] font-bold hover:underline">
                    Lihat Semua
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                @forelse($products as $product)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col h-full group overflow-hidden relative">
                         <a href="{{ route('product.show', $product->slug ?? $product->product_id) }}" class="absolute inset-0 z-10"></a>

                        <!-- Image -->
                        <div class="relative aspect-square bg-gray-100 overflow-hidden">
                             <img class="object-cover w-full h-full group-hover:scale-110 transition duration-500"
                                     src="{{ $product->main_image ? asset('storage/'.$product->main_image) : 'https://placehold.co/400x400?text=No+Image' }}"
                                     alt="{{ $product->title }}">
                            
                            <!-- Badges -->
                            <div class="absolute top-3 left-3 z-20 flex flex-col gap-2">
                                @if($product->condition == 'new')
                                    <span class="bg-green-500 text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-sm">BARU</span>
                                @else
                                    <span class="bg-gray-800 text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-sm">BEKAS</span>
                                @endif
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-4 flex flex-col flex-grow">
                             <!-- Cat & Rating -->
                             <div class="flex justify-between items-start mb-2">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $product->category->name }}</span>
                                <div class="flex items-center gap-1 text-yellow-400 text-xs font-bold">
                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                    <span class="text-gray-700">{{ $product->reviews_avg_rating ? number_format($product->reviews_avg_rating, 1) : '0' }}</span>
                                </div>
                             </div>

                             <h3 class="text-sm font-bold text-gray-900 line-clamp-2 mb-2 group-hover:text-[#EC1C25] transition-colors leading-relaxed">
                                 {{ $product->title }}
                             </h3>

                             <div class="mt-auto">
                                <p class="text-lg font-extrabold text-[#EC1C25]">Rp{{ number_format($product->price, 0, ',', '.') }}</p>
                                
                                <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-50">
                                    <div class="w-6 h-6 rounded-full bg-gray-100 overflow-hidden">
                                        <img src="{{ $product->seller->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($product->seller->name) }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-medium text-gray-700 truncate max-w-[100px]">{{ $product->seller->name }}</span>
                                        <span class="text-[10px] text-gray-400">{{ $product->seller->addresses->first()->city ?? 'Bandung' }}</span>
                                    </div>
                                </div>
                             </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Belum ada produk</h3>
                        <p class="text-gray-500 mt-1">Jadilah yang pertama berjualan!</p>
                        <a href="{{ route('shop.products.create') }}" class="inline-block mt-4 bg-[#EC1C25] text-white px-6 py-2.5 rounded-xl font-bold hover:bg-[#c4161e] transition-all shadow-lg shadow-red-500/30">Mulai Jualan</a>
                    </div>
                @endforelse
            </div>
             <div class="mt-8 text-center md:hidden">
                <a href="{{ route('search.index') }}" class="inline-block px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">Lihat Semua Produk</a>
            </div>
        </div>

        <!-- Promo Section -->
        @if(!auth()->check() || (auth()->check() && auth()->user()->role !== 'seller'))
        <div class="bg-[#EC1C25] rounded-3xl p-8 md:p-12 relative overflow-hidden mb-20 animate-fade-in-up">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8 text-center md:text-left">
                <div>
                     <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-2">Ingin Jualan di TeluConsign?</h2>
                     <p class="text-red-100 text-lg">Daftar toko gratis dan mulai hasilkan uang tambahan dari barang tak terpakai.</p>
                </div>
                <a href="{{ route('shop.products.create') }}" class="px-8 py-4 bg-white text-[#EC1C25] font-bold rounded-xl shadow-lg hover:bg-gray-50 transition-all transform hover:scale-105 whitespace-nowrap">
                    Buka Toko Gratis
                </a>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
