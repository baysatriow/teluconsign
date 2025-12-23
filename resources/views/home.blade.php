@extends('layouts.app')

@section('content')
<div class="max-w-screen-xl mx-auto px-4">

    <!-- 1. Banner Carousel (No Changes) -->
    <div class="mb-10">
        <div id="default-carousel" class="relative w-full rounded-2xl overflow-hidden shadow-lg" data-carousel="slide">
            <div class="relative h-48 md:h-80 overflow-hidden bg-gray-200 group">
                <!-- Item 1 -->
                <div class="hidden duration-700 ease-in-out" data-carousel-item>
                    <img src="https://placehold.co/1200x400/EC1C25/FFFFFF?text=Promo+Tel-U+Consign" class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2 object-cover" alt="Banner 1">
                </div>
                <!-- Item 2 -->
                <div class="hidden duration-700 ease-in-out" data-carousel-item>
                    <img src="https://placehold.co/1200x400/333333/FFFFFF?text=Jual+Beli+Aman+Mahasiswa" class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2 object-cover" alt="Banner 2">
                </div>

                <!-- Controls -->
                <button type="button" class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-prev>
                    <span class="inline-flex items-center justify-center w-8 h-8 md:w-10 md:h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                        <svg class="w-4 h-4 text-white dark:text-gray-800 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1 1 5l4 4"/>
                        </svg>
                        <span class="sr-only">Previous</span>
                    </span>
                </button>
                <button type="button" class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-next>
                    <span class="inline-flex items-center justify-center w-8 h-8 md:w-10 md:h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                        <svg class="w-4 h-4 text-white dark:text-gray-800 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                        <span class="sr-only">Next</span>
                    </span>
                </button>
            </div>
        </div>
    </div>


    <!-- 2. Category Cards - Premium Design -->
    <div class="mb-12">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Kategori Pilihan</h2>
                <p class="text-gray-500 text-sm mt-1">Mau cari barang apa hari ini?</p>
            </div>
            <a href="{{ route('search.index') }}" class="text-[#EC1C25] font-semibold text-sm hover:underline flex items-center gap-1 group">
                Lihat Semua 
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @php
                $categoryIcons = [
                    'pakaian' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h8a4 4 0 004-4V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4z"/>',
                    'aksesoris' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>',
                    'elektronik' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>',
                    'perabotan' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
                    'otomotif' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>',
                ];
            @endphp
            
            @forelse($categories as $category)
                <a href="{{ route('search.index', ['category' => $category->slug]) }}" 
                   class="group relative bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden">
                   
                   <div class="absolute inset-0 bg-gradient-to-br from-transparent to-red-50/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-16 h-16 mb-4 bg-red-50 text-[#EC1C25] rounded-2xl flex items-center justify-center group-hover:scale-110 group-hover:bg-[#EC1C25] group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {!! $categoryIcons[strtolower($category->slug)] ?? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>' !!}
                            </svg>
                        </div>
                        <h3 class="text-center font-bold text-gray-900 group-hover:text-[#EC1C25] transition-colors mb-1">{{ $category->name }}</h3>
                        <p class="text-center text-xs text-gray-400 group-hover:text-gray-500 transition-colors">{{ number_format($category->products_count) }} items</p>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-8 text-gray-500 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                    Belum ada kategori tersedia
                </div>
            @endforelse
        </div>
    </div>

    <!-- 3. Produk Terbaru (Grid Layout) -->
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Produk Terbaru</h2>
            <p class="text-gray-500 text-sm mt-1">Barang-barang baru saja diupload oleh mahasiswa lain</p>
        </div>
        <a href="{{ route('search.index') }}" class="text-[#EC1C25] font-semibold text-sm hover:underline flex items-center gap-1 group">
            Lihat Semua <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
        @forelse($products as $product)
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col h-full group overflow-hidden relative">
                
                <a href="{{ route('product.show', $product->slug ?? $product->product_id) }}" class="absolute inset-0 z-10" title="{{ $product->title }}"></a>

                <!-- Image Container -->
                <div class="relative block bg-gray-100 overflow-hidden aspect-[4/3]">
                    <img class="object-cover w-full h-full group-hover:scale-110 transition duration-700 ease-out"
                         src="{{ $product->main_image ? asset('storage/'.$product->main_image) : 'https://placehold.co/400x300?text=No+Image' }}"
                         alt="{{ $product->title }}">
                    
                    <!-- Badge Condition -->
                    <div class="absolute top-3 left-3 z-20">
                        @if($product->condition == 'new')
                            <span class="bg-green-500/90 backdrop-blur-sm text-white text-[10px] font-bold px-2.5 py-1 rounded-lg shadow-sm">
                                BARU
                            </span>
                        @else
                            <span class="bg-gray-800/80 backdrop-blur-sm text-white text-[10px] font-bold px-2.5 py-1 rounded-lg shadow-sm">
                                BEKAS
                            </span>
                        @endif
                    </div>

                    <!-- Badge Like -->
                    <button class="absolute top-3 right-3 z-20 bg-white/50 hover:bg-white text-gray-700 hover:text-red-500 p-1.5 rounded-full backdrop-blur-sm transition-all shadow-sm opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </button>
                    
                </div>

                <!-- Info Content -->
                <div class="p-4 flex flex-col flex-grow relative z-20 pointer-events-none bg-white">
                    
                    <div class="mb-2 flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">{{ $product->category->name ?? 'Umum' }}</span>
                        
                        <!-- Rating & Sold Count -->
                        <div class="flex items-center gap-2 text-[10px] sm:text-xs">
                             <div class="flex items-center gap-0.5 text-yellow-400">
                                <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                <span class="text-gray-700 font-semibold">{{ $product->reviews_avg_rating ? number_format($product->reviews_avg_rating, 1) : '0' }}</span>
                            </div>
                            @if($product->order_items_sum_quantity > 0)
                                <span class="text-gray-300">|</span>
                                <span class="text-gray-500 font-medium whitespace-nowrap">Terjual {{ $product->order_items_sum_quantity > 99 ? '99+' : $product->order_items_sum_quantity }}</span>
                            @endif
                        </div>
                    </div>

                    <h5 class="text-sm font-bold text-gray-900 line-clamp-2 leading-relaxed group-hover:text-[#EC1C25] transition-colors mb-2 min-h-[2.5em]">
                        {{ $product->title }}
                    </h5>

                    <p class="text-lg font-extrabold text-[#EC1C25] mb-4">
                        Rp{{ number_format($product->price, 0, ',', '.') }}
                    </p>

                    <div class="mt-auto pt-3 border-t border-gray-50 space-y-2">
                        <!-- Seller Info -->
                        <div class="flex items-center gap-2 text-xs">
                            <img src="{{ $product->seller->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($product->seller->name).'&background=f3f4f6&color=6b7280' }}" 
                                 class="w-5 h-5 rounded-full object-cover border border-gray-100 flex-shrink-0">
                            <span class="truncate font-medium text-gray-700">{{ $product->seller->name ?? 'Seller' }}</span>
                        </div>
                        
                        <!-- Location Badge -->
                        <div class="flex items-center gap-1.5 text-xs">
                            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span class="text-gray-500 font-medium">{{ $product->seller->addresses->first()->city ?? 'Bandung' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Belum Ada Produk</h3>
                <p class="text-gray-500 mt-1">Jadilah yang pertama menjual barang disini!</p>
                <a href="{{ route('shop.products.create') }}" class="inline-block mt-4 bg-[#EC1C25] text-white px-6 py-2 rounded-full font-bold text-sm hover:bg-red-700 transition">Mulai Jualan</a>
            </div>
        @endforelse
    </div>

    <!-- Pagination is removed as we show max 20 latest products only -->

</div>
@endsection
