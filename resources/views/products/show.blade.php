@extends('layouts.app')

@section('content')
<div class="max-w-screen-xl mx-auto px-4 py-8">

    <!-- Breadcrumb (Navigasi) -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-[#EC1C25]">
                    Home
                </a>
            </li>
            @if($product->category)
            <li>
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <a href="{{ route('search.index', ['category' => $product->category_id]) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-[#EC1C25] md:ml-2">
                        {{ $product->category->name }}
                    </a>
                </div>
            </li>
            @endif
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 truncate max-w-[200px]">{{ $product->title }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

        <!-- KOLOM KIRI: Galeri Foto -->
        <div class="lg:col-span-7">
            <div class="bg-white rounded-2xl p-2 sticky top-24">
                <!-- Main Image -->
                <div class="relative w-full aspect-[4/3] rounded-xl overflow-hidden mb-4 bg-gray-100 group border border-gray-100">
                    <img id="mainImage"
                         src="{{ $product->main_image ? asset('storage/'.$product->main_image) : 'https://placehold.co/800x800?text=No+Image' }}"
                         class="object-contain w-full h-full transition-transform duration-500 group-hover:scale-105 cursor-zoom-in"
                         alt="{{ $product->title }}">
                         
                    @if($product->condition == 'new')
                        <span class="absolute top-4 left-4 bg-green-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg z-20">BARU</span>
                    @else
                        <span class="absolute top-4 left-4 bg-gray-700 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg z-20">BEKAS</span>
                    @endif
                </div>

                <!-- Thumbnail Grid -->
                @if($product->images->count() > 0)
                <div class="grid grid-cols-5 gap-3">
                    <button onclick="changeImage('{{ $product->main_image ? asset('storage/'.$product->main_image) : 'https://placehold.co/800x800' }}')" class="aspect-square rounded-lg overflow-hidden border-2 border-[#EC1C25] hover:opacity-80 transition focus:outline-none ring-2 ring-transparent focus:ring-[#EC1C25]">
                        <img src="{{ $product->main_image ? asset('storage/'.$product->main_image) : 'https://placehold.co/800x800' }}" class="w-full h-full object-cover">
                    </button>

                    @foreach($product->images as $img)
                        @if($img->url !== $product->main_image)
                        <button onclick="changeImage('{{ asset('storage/'.$img->url) }}')" class="aspect-square rounded-lg overflow-hidden border-2 border-transparent hover:border-gray-300 transition focus:outline-none ring-2 ring-transparent focus:ring-[#EC1C25]">
                            <img src="{{ asset('storage/'.$img->url) }}" class="w-full h-full object-cover">
                        </button>
                        @endif
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- KOLOM KANAN: Info & Aksi -->
        <div class="lg:col-span-5">
            <div class="flex flex-col h-full">
                
                <!-- HEADER INFO -->
                <div class="mb-6">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2 leading-tight">{{ $product->title }}</h1>
                    
                    <div class="flex items-center gap-4 text-sm text-gray-500 mt-3">
                        <span class="font-medium text-gray-900">Terjual {{ $product->order_items_sum_quantity > 0 ? $product->order_items_sum_quantity : 0 }}</span>
                        <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            {{ number_format($product->reviews_avg_rating ?? 0, 1) }} ({{ $product->reviews_count ?? $product->reviews->count() }} ulasan)
                        </span>
                    </div>

                    <div class="mt-6">
                        <h2 class="text-4xl font-extrabold text-[#EC1C25]">Rp{{ number_format($product->price, 0, ',', '.') }}</h2>
                    </div>
                </div>

                <hr class="border-gray-100 mb-6">

                <!-- DESKRIPSI -->
                <div class="mb-8">
                    <h3 class="text-sm font-bold text-gray-900 mb-3 uppercase tracking-wide">Detail Produk</h3>
                    <div class="grid grid-cols-2 gap-y-2 text-sm text-gray-600 mb-4">
                        <div class="flex gap-2"><span class="text-gray-400 w-24">Kondisi</span> <span class="font-medium text-gray-900 capitalize">{{ $product->condition == 'new' ? 'Baru' : 'Bekas' }}</span></div>
                        <div class="flex gap-2"><span class="text-gray-400 w-24">Berat</span> <span class="font-medium text-gray-900">1000 gr</span></div>
                        <div class="flex gap-2"><span class="text-gray-400 w-24">Kategori</span> <span class="font-medium text-[#EC1C25]">{{ $product->category->name ?? '-' }}</span></div>
                        <div class="flex gap-2"><span class="text-gray-400 w-24">Stok</span> <span class="font-medium text-gray-900">{{ $product->stock }} buah</span></div>
                    </div>
                    
                    <p class="text-gray-600 text-sm leading-relaxed whitespace-pre-line">{{ $product->description }}</p>
                </div>

                <!-- SELLER PROFILE -->
                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100 mb-8">
                    <img class="w-12 h-12 rounded-full object-cover border border-gray-200"
                         src="{{ $product->seller->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($product->seller->name).'&background=random' }}"
                         alt="Seller">
                    <div class="flex-grow">
                        <div class="flex items-center gap-2">
                             <p class="text-sm font-bold text-gray-900">{{ $product->seller->name }}</p>
                        </div>
                        @php
                            $city = 'Indonesia';
                            if($product->seller->addresses->isNotEmpty()) {
                                $primary = $product->seller->addresses->where('is_primary', 1)->first();
                                if($primary && $primary->city) {
                                    $city = Str::replace(['KABUPATEN ', 'KOTA '], '', strtoupper($primary->city));
                                    $city = ucwords(strtolower($city));
                                }
                            }
                        @endphp
                        <p class="text-xs text-gray-500">{{ $city }} &bull; Online 5 menit lalu</p>
                    </div>
                    <a href="{{ route('shop.show', $product->seller->username ?? $product->seller_id) }}" class="text-xs font-bold text-[#EC1C25] border border-[#EC1C25] rounded-lg px-4 py-2 hover:bg-red-50 transition-colors">
                        Kunjungi Toko
                    </a>
                </div>

                <!-- ACTION BUTTONS STICKY -->
                <div class="mt-auto bg-white border border-gray-200 p-4 rounded-xl shadow-lg sticky bottom-4 z-30">
                     @if(Auth::id() == $product->seller_id)
                        <div class="flex gap-2">
                            <a href="{{ route('shop.products.edit', $product->slug) }}" class="flex-1 text-center bg-gray-100 text-gray-700 font-bold py-3 rounded-lg hover:bg-gray-200 text-sm">Edit Produk</a>
                        </div>
                     @else
                        @if($product->stock > 0)
                            <div class="flex flex-col gap-3">
                                <div class="flex gap-3">
                                    <button onclick="addToCart()" id="btn-add-cart" class="flex-1 flex items-center justify-center gap-2 text-[#EC1C25] bg-white border border-[#EC1C25] hover:bg-red-50 font-bold rounded-xl text-sm px-5 py-3 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        + Keranjang
                                    </button>
                                    
                                    <button onclick="buyNow()" id="btn-buy-now" class="flex-1 text-white bg-[#EC1C25] hover:bg-[#c4161e] font-bold rounded-xl text-sm px-5 py-3 shadow-md hover:shadow-lg transition-all">
                                        Beli Langsung
                                    </button>
                                </div>
                            </div>
                        @else
                             <button disabled class="w-full bg-gray-300 text-white font-bold py-3 rounded-xl cursor-not-allowed">Stok Habis</button>
                        @endif
                     @endif
                </div>

            </div>
        </div>

    </div>

    <!-- Reviews Section -->
    <div class="mt-16 border-t border-gray-200 pt-10">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Ulasan Pembeli <span class="text-gray-500 text-lg font-normal">({{ $product->reviews_count ?? $product->reviews->count() }})</span></h2>
            
            @if($product->reviews->count() > 0)
                <div class="flex items-center gap-2 text-yellow-400 bg-yellow-50 px-3 py-1.5 rounded-lg">
                     <span class="text-2xl font-bold text-gray-900">{{ number_format($product->reviews_avg_rating ?? 0, 1) }}</span>
                     <div class="flex">
                        @for($i=1; $i<=5; $i++)
                            <svg class="w-5 h-5 {{ $i <= round($product->reviews_avg_rating) ? 'fill-current' : 'text-gray-300 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                     </div>
                </div>
            @endif
        </div>
        
        <div class="space-y-6">
            @forelse($product->reviews as $review)
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-start gap-4">
                        <img src="{{ $review->user->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($review->user->name) }}" class="w-12 h-12 rounded-full object-cover border border-gray-100 shadow-sm">
                        <div class="flex-grow">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-bold text-gray-900 text-base">{{ $review->user->name }}</h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <div class="flex text-yellow-400">
                                            @for($i=1; $i<=5; $i++)
                                                @if($i <= $review->rating)
                                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                @else
                                                    <svg class="w-4 h-4 text-gray-200 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="text-gray-400 text-xs">&bull;</span>
                                        <span class="text-gray-400 text-xs">{{ $review->created_at->locale('id')->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-3 text-gray-700 text-sm leading-relaxed bg-gray-50 p-4 rounded-xl">
                                {{ $review->comment }}
                            </div>

                            @if($review->reply)
                                <div class="ml-4 mt-3 bg-red-50 p-4 rounded-xl border border-red-100 flex gap-3">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-[#EC1C25]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-[#EC1C25] text-sm mb-1">Respon Penjual</p>
                                        <p class="text-gray-700 text-sm">{{ $review->reply }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-white rounded-2xl border border-dashed border-gray-300">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-3">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    </div>
                    <p class="text-gray-900 font-medium">Belum ada ulasan</p>
                    <p class="text-gray-500 text-sm">Jadilah yang pertama memberikan ulasan untuk produk ini!</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Related Products (Same Shop) -->
    @if($shopProducts->count() > 0)
    <div class="mt-16">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                Lainnya dari Toko Ini
            </h2>
            <a href="{{ route('shop.show', $product->seller->username ?? $product->seller->user_id) }}" class="text-sm font-bold text-[#EC1C25] hover:text-[#b0151b] transition-colors flex items-center gap-1 group">
                Lihat Selengkapnya
                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($shopProducts as $related)
            <div class="group bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden flex flex-col h-full transform hover:-translate-y-1">
                <!-- Image -->
                <div class="relative w-full pt-[100%] bg-gray-100 overflow-hidden">
                    <img src="{{ $related->main_image ? asset('storage/'.$related->main_image) : 'https://placehold.co/400x300?text=No+Image' }}" 
                         alt="{{ $related->title }}" 
                         class="absolute top-0 left-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    
                    <!-- Badge Kondisi -->
                    <span class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-gray-900 text-[10px] font-bold px-2.5 py-1 rounded-full shadow-sm border border-gray-100">
                        {{ $related->condition == 'new' ? 'BARU' : 'BEKAS' }}
                    </span>
                    
                    <!-- Overlay -->
                    <a href="{{ route('product.show', $related->slug ?? $related->product_id) }}" class="absolute inset-0 z-10"></a>
                </div>

                <!-- Content -->
                <div class="p-4 flex flex-col flex-grow">
                    <!-- Category Pill -->
                    <a href="{{ route('search.index', ['category' => $related->category_id]) }}" class="inline-block w-fit mb-2 z-20 relative">
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider hover:text-[#EC1C25] transition-colors">
                            {{ $related->category->name ?? 'Umum' }}
                        </span>
                    </a>

                    <!-- Title -->
                    <h5 class="text-sm font-bold text-gray-900 line-clamp-2 leading-relaxed group-hover:text-[#EC1C25] transition-colors mb-2 min-h-[2.5em]">
                        {{ $related->title }}
                    </h5>

                    <!-- Price -->
                    <p class="text-lg font-extrabold text-[#EC1C25] mb-4">
                        Rp{{ number_format($related->price, 0, ',', '.') }}
                    </p>

                    <!-- Footer: Seller & Location -->
                    <div class="mt-auto pt-3 border-t border-gray-50 space-y-2">
                        <!-- Seller -->
                        <div class="flex items-center gap-2 text-xs">
                            <img src="{{ $related->seller->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($related->seller->name).'&background=f3f4f6&color=6b7280' }}" 
                                 class="w-5 h-5 rounded-full object-cover border border-gray-100 flex-shrink-0">
                            <span class="truncate font-medium text-gray-700">{{ $related->seller->name ?? 'Seller' }}</span>
                        </div>
                        
                        <!-- Location Badge -->
                        <div class="flex items-center gap-1.5 text-xs">
                            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span class="text-gray-500 font-medium">{{ $related->seller->addresses->first()->city ?? ($related->location ?? 'Indonesia') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Related Products (Similar/Category) -->
    @if($relatedProducts->count() > 0)
    <div class="mt-16">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                Produk Serupa
            </h2>
            <a href="{{ route('search.index', ['category' => $product->category_id]) }}" class="text-sm font-bold text-[#EC1C25] hover:text-[#b0151b] transition-colors flex items-center gap-1 group">
                Lihat Selengkapnya
                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($relatedProducts as $related)
            <div class="group bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden flex flex-col h-full transform hover:-translate-y-1">
                <!-- Image -->
                <div class="relative w-full pt-[100%] bg-gray-100 overflow-hidden">
                    <img src="{{ $related->main_image ? asset('storage/'.$related->main_image) : 'https://placehold.co/400x300?text=No+Image' }}" 
                         alt="{{ $related->title }}" 
                         class="absolute top-0 left-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    
                    <!-- Badge Kondisi -->
                    <span class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-gray-900 text-[10px] font-bold px-2.5 py-1 rounded-full shadow-sm border border-gray-100">
                        {{ $related->condition == 'new' ? 'BARU' : 'BEKAS' }}
                    </span>
                    
                    <!-- Overlay -->
                    <a href="{{ route('product.show', $related->slug ?? $related->product_id) }}" class="absolute inset-0 z-10"></a>
                </div>

                <!-- Content -->
                <div class="p-4 flex flex-col flex-grow">
                    <!-- Category Pill -->
                    <a href="{{ route('search.index', ['category' => $related->category_id]) }}" class="inline-block w-fit mb-2 z-20 relative">
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider hover:text-[#EC1C25] transition-colors">
                            {{ $related->category->name ?? 'Umum' }}
                        </span>
                    </a>

                    <!-- Title -->
                    <h5 class="text-sm font-bold text-gray-900 line-clamp-2 leading-relaxed group-hover:text-[#EC1C25] transition-colors mb-2 min-h-[2.5em]">
                        {{ $related->title }}
                    </h5>

                    <!-- Price -->
                    <p class="text-lg font-extrabold text-[#EC1C25] mb-4">
                        Rp{{ number_format($related->price, 0, ',', '.') }}
                    </p>

                    <!-- Footer: Seller & Location -->
                    <div class="mt-auto pt-3 border-t border-gray-50 space-y-2">
                        <!-- Seller -->
                        <div class="flex items-center gap-2 text-xs">
                            <img src="{{ $related->seller->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($related->seller->name).'&background=f3f4f6&color=6b7280' }}" 
                                 class="w-5 h-5 rounded-full object-cover border border-gray-100 flex-shrink-0">
                            <span class="truncate font-medium text-gray-700">{{ $related->seller->name ?? 'Seller' }}</span>
                        </div>
                        
                        <!-- Location Badge -->
                        <div class="flex items-center gap-1.5 text-xs">
                            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span class="text-gray-500 font-medium">{{ $related->seller->addresses->first()->city ?? ($related->location ?? 'Indonesia') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

<!-- LIGHTBOX MODAL (Admin Style Port) -->
<div id="lightbox" class="fixed inset-0 z-[100] hidden bg-black/95 backdrop-blur-sm flex flex-col items-center justify-center p-4">
    <button onclick="closeLightbox()" class="absolute top-4 right-4 text-white hover:text-gray-300 focus:outline-none z-50 p-2 rounded-full hover:bg-white/10 transition-colors">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>
    
    <div class="flex items-center justify-center w-full h-[80vh] gap-4 relative">
        <button onclick="prevImage()" class="absolute left-2 md:left-8 text-white hover:text-gray-300 p-3 bg-white/10 hover:bg-white/20 backdrop-blur-md rounded-full transition-all z-40 focus:outline-none -translate-y-1/2 top-1/2 group">
            <svg class="w-6 h-6 md:w-8 md:h-8 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        
        <img id="lightbox-img" src="" class="max-w-full max-h-full object-contain rounded-lg shadow-2xl transition-all duration-300 opacity-0 scale-95 data-[visible=true]:opacity-100 data-[visible=true]:scale-100">
        
        <button onclick="nextImage()" class="absolute right-2 md:right-8 text-white hover:text-gray-300 p-3 bg-white/10 hover:bg-white/20 backdrop-blur-md rounded-full transition-all z-40 focus:outline-none -translate-y-1/2 top-1/2 group">
            <svg class="w-6 h-6 md:w-8 md:h-8 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>
    </div>

    <div class="absolute bottom-6 flex gap-3 overflow-x-auto max-w-full px-4 py-2 custom-scrollbar">
        <button onclick="openLightbox(0)" 
            class="w-16 h-16 md:w-20 md:h-20 rounded-lg overflow-hidden border-2 transition-all opacity-60 hover:opacity-100 focus:outline-none lightbox-thumb flex-shrink-0 bg-gray-900 border-transparent" 
            data-index="0">
            <img src="{{ $product->main_image ? asset('storage/'.$product->main_image) : 'https://placehold.co/800x800' }}" class="w-full h-full object-cover">
        </button>
        @foreach($product->images as $index => $img)
            @if($img->url !== $product->main_image)
            <button onclick="openLightbox({{ $index + 1 }})" 
                class="w-16 h-16 md:w-20 md:h-20 rounded-lg overflow-hidden border-2 transition-all opacity-60 hover:opacity-100 focus:outline-none lightbox-thumb flex-shrink-0 bg-gray-900 border-transparent" 
                data-index="{{ $index + 1 }}">
                <img src="{{ asset('storage/'.$img->url) }}" class="w-full h-full object-cover">
            </button>
            @endif
        @endforeach
    </div>
</div>

<script>
    // --- LIGHTBOX LOGIC ---
    let currentIndex = 0;
    const images = [
        "{{ $product->main_image ? asset('storage/'.$product->main_image) : 'https://placehold.co/800x800' }}",
        @foreach($product->images as $img)
            @if($img->url !== $product->main_image)
            "{{ asset('storage/'.$img->url) }}",
            @endif
        @endforeach
    ];

    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const thumbs = document.querySelectorAll('.lightbox-thumb');

    // 1. Image Gallery (Thumb Click updates Main Image)
    function changeImage(src) {
        const mainImage = document.getElementById('mainImage');
        mainImage.style.opacity = 0.5;
        setTimeout(() => {
            mainImage.src = src;
            mainImage.style.opacity = 1;
        }, 150);
    }

    function openLightbox(index) {
        if (images.length === 0) return;
        
        currentIndex = index;
        updateLightboxImage();
        
        lightbox.classList.remove('hidden');
        lightbox.classList.add('flex');
        document.body.style.overflow = 'hidden';
        
        // Animasi masuk
        setTimeout(() => {
             lightboxImg.setAttribute('data-visible', 'true');
        }, 50);
    }

    function closeLightbox() {
        lightboxImg.setAttribute('data-visible', 'false');
        setTimeout(() => {
            lightbox.classList.add('hidden');
            lightbox.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }, 300);
    }

    function updateLightboxImage() {
        // Animasi swap image
        lightboxImg.setAttribute('data-visible', 'false');
        
        setTimeout(() => {
            lightboxImg.src = images[currentIndex];
            lightboxImg.setAttribute('data-visible', 'true');
        }, 200);

        // Update active thumb
        thumbs.forEach((thumb, i) => {
            if (i === currentIndex) {
                thumb.classList.add('border-white', 'opacity-100', 'scale-110');
                thumb.classList.remove('border-transparent', 'opacity-60');
            } else {
                thumb.classList.remove('border-white', 'opacity-100', 'scale-110');
                thumb.classList.add('border-transparent', 'opacity-60');
            }
        });
    }

    function nextImage() {
        currentIndex = (currentIndex + 1) % images.length;
        updateLightboxImage();
    }

    function prevImage() {
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        updateLightboxImage();
    }

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (lightbox.classList.contains('hidden')) return;
        
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowRight') nextImage();
        if (e.key === 'ArrowLeft') prevImage();
    });

    // Bind click to main image to open lightbox at current main image index
    const mainImage = document.getElementById('mainImage');
    if(mainImage) {
        mainImage.addEventListener('click', () => {
             // Find index of current main image src
             const currentSrc = mainImage.src;
             let idx = images.findIndex(img => img === currentSrc);
             if (idx === -1) idx = 0; // fallback
             openLightbox(idx);
        });
    }

    // --- CHECKOUT LOGIC ---
    
    // Helper to check Auth
    const isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};

    // Shared SweetAlert Config (Industrial Standard)
    const SwalModal = (window.TeluSwal || window.Swal).mixin({
        buttonsStyling: false,
        customClass: {
            popup: 'rounded-3xl shadow-2xl border border-gray-100 p-0 overflow-hidden',
            title: 'text-xl font-extrabold text-gray-900 mt-6 px-4',
            htmlContainer: 'text-gray-500 text-sm mt-2 mb-8 px-8 leading-relaxed',
            actions: 'w-full flex justify-center gap-3 px-6 pb-8',
            confirmButton: 'flex-1 bg-[#EC1C25] hover:bg-[#c4161e] text-white font-bold py-3.5 px-6 rounded-xl shadow-lg shadow-red-500/20 transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2',
            cancelButton:  'flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3.5 px-6 rounded-xl transition-all transform hover:-translate-y-0.5 border border-gray-200',
            icon: 'mb-4 mt-6 border-none'
        },
        padding: 0,
        width: '400px'
    });

    function checkAuth() {
        if (!isLoggedIn) {
            SwalModal.fire({
                title: 'Login Diperlukan',
                html: "Anda harus login terlebih dahulu<br>untuk melakukan transaksi.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Login Sekarang',
                cancelButtonText: 'Batal Nanti',
                // Default order: Confirm (Left), Cancel (Right)
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route("login") }}';
                }
            });
            return false;
        }
        return true;
    }

    // 3. AJAX Add to Cart
    function addToCart() {
        if(!checkAuth()) return;

        const btn = document.getElementById('btn-add-cart');
        const originalText = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin h-5 w-5 text-[#EC1C25]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            credentials: 'same-origin', // include cookies for session authentication
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                product_id: {{ $product->product_id }},
                quantity: 1
            })
        })
        .then(response => {
            return response.text().then(text => {
                try {
                    return {
                        status: response.status,
                        body: JSON.parse(text)
                    };
                } catch (e) {
                    throw new Error('Server returned invalid JSON.');
                }
            });
        })
        .then(res => {
            const data = res.body;
            if (res.status === 200 && data.status === 'success') {
                const SwalInstance = window.TeluSwal || window.Swal;
                const Toast = SwalInstance.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', SwalInstance.stopTimer)
                        toast.addEventListener('mouseleave', SwalInstance.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: 'success',
                    title: 'Berhasil masuk keranjang'
                });
                
                setTimeout(() => location.reload(), 800);
            } else {
                SwalModal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    html: data.message || 'Terjadi kesalahan.',
                    confirmButtonText: 'Tutup',
                    showCancelButton: false,
                    customClass: {
                        popup: 'rounded-3xl shadow-2xl border border-gray-100 p-0 overflow-hidden',
                        title: 'text-xl font-extrabold text-gray-900 mt-6 px-4',
                        htmlContainer: 'text-gray-500 text-sm mt-2 mb-8 px-8 leading-relaxed',
                        actions: 'w-full flex justify-center px-6 pb-8',
                        confirmButton: 'w-full bg-[#EC1C25] hover:bg-[#c4161e] text-white font-bold py-3.5 px-6 rounded-xl shadow-lg shadow-red-500/20 transition-all',
                        icon: 'mb-4 mt-6 border-none'
                    }
                });
            }
        })
        .catch(err => {
            console.error('Add to Cart Error:', err);
            SwalModal.fire({
                icon: 'error',
                title: 'Error Sistem',
                html: err.message || 'Terjadi kesalahan sistem.',
                confirmButtonText: 'Tutup',
                showCancelButton: false,
                customClass: {
                    popup: 'rounded-3xl shadow-2xl border border-gray-100 p-0 overflow-hidden',
                    title: 'text-xl font-extrabold text-gray-900 mt-6 px-4',
                    htmlContainer: 'text-gray-500 text-sm mt-2 mb-8 px-8 leading-relaxed',
                    actions: 'w-full flex justify-center px-6 pb-8',
                    confirmButton: 'w-full bg-[#EC1C25] hover:bg-[#c4161e] text-white font-bold py-3.5 px-6 rounded-xl shadow-lg shadow-red-500/20 transition-all',
                    icon: 'mb-4 mt-6 border-none'
                }
            });
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }

    // Beli Langsung - Add to cart and redirect with pre-selection
    function buyNow() {
        if(!checkAuth()) return;

        const btn = document.getElementById('btn-buy-now');
        const originalText = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            credentials: 'same-origin', // include cookies for session authentication
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                product_id: {{ $product->product_id }},
                quantity: 1
            })
        })
        .then(response => {
            return response.text().then(text => {
                try {
                    return {
                        status: response.status,
                        body: JSON.parse(text)
                    };
                } catch (e) {
                    throw new Error('Server returned invalid JSON.');
                }
            });
        })
        .then(res => {
            const data = res.body;
            if (res.status === 200 && data.status === 'success') {
                window.location.href = '{{ route("cart.index") }}?selected_product={{ $product->product_id }}';
            } else {
                SwalModal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    html: data.message || 'Terjadi kesalahan.',
                    confirmButtonText: 'Tutup',
                    showCancelButton: false,
                    customClass: {
                        popup: 'rounded-3xl shadow-2xl border border-gray-100 p-0 overflow-hidden',
                        title: 'text-xl font-extrabold text-gray-900 mt-6 px-4',
                        htmlContainer: 'text-gray-500 text-sm mt-2 mb-8 px-8 leading-relaxed',
                        actions: 'w-full flex justify-center px-6 pb-8',
                        confirmButton: 'w-full bg-[#EC1C25] hover:bg-[#c4161e] text-white font-bold py-3.5 px-6 rounded-xl shadow-lg shadow-red-500/20 transition-all',
                        icon: 'mb-4 mt-6 border-none'
                    }
                });
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(err => {
            console.error('Buy Now Error:', err);
            SwalModal.fire({
                icon: 'error',
                title: 'Error Sistem',
                html: err.message || 'Terjadi kesalahan sistem.',
                confirmButtonText: 'Tutup',
                showCancelButton: false,
                customClass: {
                    popup: 'rounded-3xl shadow-2xl border border-gray-100 p-0 overflow-hidden',
                    title: 'text-xl font-extrabold text-gray-900 mt-6 px-4',
                    htmlContainer: 'text-gray-500 text-sm mt-2 mb-8 px-8 leading-relaxed',
                    actions: 'w-full flex justify-center px-6 pb-8',
                    confirmButton: 'w-full bg-[#EC1C25] hover:bg-[#c4161e] text-white font-bold py-3.5 px-6 rounded-xl shadow-lg shadow-red-500/20 transition-all',
                    icon: 'mb-4 mt-6 border-none'
                }
            });
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }
</script>
@endsection
