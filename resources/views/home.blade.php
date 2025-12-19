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

    <!-- 2. Kategori Pilihan (Fixed 5 Categories) -->
    <div class="mb-10">
        <h2 class="text-xl font-bold text-gray-900 mb-5">Kategori Pilihan</h2>
        
        <!-- Logic to map Categories by Name if ID is unknown, or use search param names -->
        @php
            $fixedCats = [
                ['name' => 'Semua', 'param' => '', 'icon' => 'M4 6h16M4 12h16M4 18h16'],
                ['name' => 'Pakaian', 'slug' => 'pakaian', 'icon' => 'M3 13h1l3 8h10l3-8h1M2 10h20M5 21h14'], // Shirt-like
                ['name' => 'Aksesoris', 'slug' => 'aksesoris', 'icon' => 'M12 8c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z'], // Simple user/acc
                ['name' => 'Elektronik', 'slug' => 'elektronik', 'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'], // Monitor
                ['name' => 'Perabotan', 'slug' => 'perabotan', 'icon' => 'M3 13h18M5 13v8h14v-8M5 13l2-10h10l2 10'], // Sofa-ish
                ['name' => 'Otomotif', 'slug' => 'otomotif', 'icon' => 'M5 13l4 4L19 7'], // Checkmark/Simple
            ];
            
            // Try to find IDs for these names (Case insensitive)
            // Use Helper or loop
            $catMap = [];
            foreach($categories as $c) {
                $catMap[strtolower($c->name)] = $c->category_id;
            }
        @endphp

        <div class="flex flex-wrap gap-3">
            @foreach($fixedCats as $fcat)
                @php
                    $isActive = !request('category') && $fcat['name'] === 'Semua';
                    if($fcat['name'] !== 'Semua') {
                        // Find matching ID from DB categories
                        $matchId = $catMap[strtolower($fcat['name'])] ?? null;
                        // Determine active
                        $isActive = request('category') == $matchId;
                        // Url
                        $url = $matchId ? route('search.index', ['category' => $matchId]) : route('search.index', ['q' => $fcat['name']]);
                    } else {
                        $url = route('home');
                    }
                @endphp
                
                <a href="{{ $url }}" class="flex items-center gap-2 px-6 py-3 rounded-full text-base font-medium transition-all shadow-sm border {{ $isActive ? 'bg-[#EC1C25] text-white border-[#EC1C25]' : 'bg-white text-gray-700 border-gray-200 hover:border-[#EC1C25] hover:text-[#EC1C25] hover:shadow-md' }}">
                    {{ $fcat['name'] }}
                </a>
            @endforeach
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
