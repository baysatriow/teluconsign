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
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Kategori Pilihan</h2>
            <p class="text-gray-600 text-sm mt-1">Temukan barang sesuai kebutuhanmu</p>
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
                   class="group bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex flex-col items-center">
                    <div class="w-16 h-16 mb-4 bg-gradient-to-br from-red-50 to-red-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $categoryIcons[strtolower($category->slug)] ?? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>' !!}
                        </svg>
                    </div>
                    <h3 class="text-center font-bold text-gray-900 text-sm mb-1">{{ $category->name }}</h3>
                    <p class="text-center text-xs text-gray-500">{{ number_format($category->products_count) }} items</p>
                </a>
            @empty
                <div class="col-span-full text-center py-8 text-gray-500">
                    Belum ada kategori tersedia
                </div>
            @endforelse
        </div>
    </div>

    <!-- 3. Rekomendasi (New Grid Layout) -->
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 leading-tight">Rekomendasi Terkini</h2>
            <p class="text-gray-500 text-sm mt-1">Temukan barang menarik di sekitarmu</p>
        </div>
        <a href="{{ route('search.index') }}" class="text-[#EC1C25] font-medium text-sm hover:underline flex items-center gap-1">
            Lihat Semua <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-5">
        @forelse($products as $product)
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col h-full group overflow-hidden relative">
                
                <a href="{{ route('product.show', $product->product_id) }}" class="absolute inset-0 z-10" title="{{ $product->title }}"></a>

                <div class="relative block h-52 overflow-hidden bg-gray-50">
                    <img class="object-cover w-full h-full group-hover:scale-105 transition duration-700"
                         src="{{ $product->main_image ? asset('storage/'.$product->main_image) : 'https://placehold.co/400x400?text=No+Image' }}"
                         alt="{{ $product->title }}">
                    
                    @if($product->condition == 'new')
                        <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-green-600 border border-green-100 text-[10px] font-bold px-2.5 py-1 rounded-full shadow-sm z-20">
                            BARU
                        </div>
                    @else
                        <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-gray-700 border border-gray-200 text-[10px] font-bold px-2.5 py-1 rounded-full shadow-sm z-20">
                            BEKAS
                        </div>
                    @endif
                </div>

                <div class="p-4 flex flex-col flex-grow relative z-20 pointer-events-none">
                    <div class="flex px-2 py-1 bg-gray-50 rounded w-fit mb-2">
                         <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-wide">{{ $product->category->name ?? 'Umum' }}</span>
                    </div>

                    <h5 class="text-base font-semibold text-gray-900 line-clamp-2 leading-snug group-hover:text-[#EC1C25] transition-colors mb-2">
                        {{ $product->title }}
                    </h5>

                    <p class="text-lg font-bold text-[#EC1C25] mb-4">
                        Rp{{ number_format($product->price, 0, ',', '.') }}
                    </p>

                    <div class="mt-auto pt-3 border-t border-gray-50 flex items-center justify-between text-xs text-gray-500">
                        <div class="flex items-center gap-2 max-w-[75%]">
                            <div class="w-6 h-6 rounded-full bg-gray-200 overflow-hidden flex-shrink-0 border border-white shadow-sm">
                                <img src="{{ $product->seller->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($product->seller->name).'&background=random' }}" class="w-full h-full object-cover">
                            </div>
                            <span class="truncate font-medium text-gray-700">{{ $product->seller->name ?? 'Seller' }}</span>
                        </div>
                        <span class="truncate max-w-[25%]">{{ $product->location ?? 'Bdg' }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-gray-400">
                <p>Belum ada produk untuk ditampilkan.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-12 mb-8 flex justify-center">
        {{ $products->links('pagination::tailwind') }}
    </div>

</div>
@endsection
