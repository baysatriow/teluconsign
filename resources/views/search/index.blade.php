@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- SIDEBAR FILTERS -->
        <div class="w-full lg:w-64 flex-shrink-0 space-y-8">
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm sticky top-24">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Filter</h3>
                    <a href="{{ route('search.index') }}" class="text-xs text-[#EC1C25] font-medium hover:underline">Reset</a>
                </div>

                <form action="{{ route('search.index') }}" method="GET" id="filterForm">
                    @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                    @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                    <!-- Kategori -->
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-800 mb-3">Kategori</h4>
                        <div class="space-y-2 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="category" value="" class="text-[#EC1C25] focus:ring-[#EC1C25] rounded-full border-gray-300" {{ !request('category') ? 'checked' : '' }} onchange="this.form.submit()">
                                <span class="text-sm text-gray-600 group-hover:text-[#EC1C25]">Semua Kategori</span>
                            </label>
                            @foreach($categories as $cat)
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" name="category" value="{{ $cat->slug }}" class="text-[#EC1C25] focus:ring-[#EC1C25] rounded-full border-gray-300" {{ request('category') == $cat->slug ? 'checked' : '' }} onchange="this.form.submit()">
                                    <span class="text-sm text-gray-600 group-hover:text-[#EC1C25]">{{ $cat->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Subcategory Filter (only show if category selected and has children) -->
                    @if(isset($selectedCategory) && $selectedCategory->children->count() > 0)
                    <div class="mb-6 pt-6 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-3">Sub Kategori</h4>
                        <div class="space-y-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                            @foreach($selectedCategory->children as $child)
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" name="subcategory[]" value="{{ $child->category_id }}" class="rounded text-[#EC1C25] focus:ring-[#EC1C25] border-gray-300" {{ in_array($child->category_id, (array)request('subcategory', [])) ? 'checked' : '' }}>
                                    <span class="text-sm text-gray-600 group-hover:text-[#EC1C25]">{{ $child->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <button type="submit" class="w-full mt-3 bg-gray-100 text-gray-700 font-medium py-2 rounded-lg text-sm hover:bg-gray-200 transition-colors">Terapkan Filter</button>
                    </div>
                    @endif

                    <!-- Kondisi -->
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-800 mb-3">Kondisi</h4>
                        <div class="flex flex-col gap-2">
                             <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="condition" value="new" class="rounded text-[#EC1C25] focus:ring-[#EC1C25] border-gray-300" {{ request('condition') == 'new' ? 'checked' : '' }} onchange="this.form.submit()">
                                <span class="text-sm text-gray-600">Baru</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="condition" value="used" class="rounded text-[#EC1C25] focus:ring-[#EC1C25] border-gray-300" {{ request('condition') == 'used' ? 'checked' : '' }} onchange="this.form.submit()">
                                <span class="text-sm text-gray-600">Bekas</span>
                            </label>
                        </div>
                    </div>

                    <!-- Harga -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-800 mb-3">Harga</h4>
                        <div class="flex items-center gap-2 mb-3">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-full text-sm border-gray-300 rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25]">
                            <span class="text-gray-400">-</span>
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-full text-sm border-gray-300 rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25]">
                        </div>
                        <button type="submit" class="w-full bg-gray-100 text-gray-700 font-medium py-2 rounded-lg text-sm hover:bg-gray-200 transition-colors">Terapkan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="flex-grow">
            <!-- Category Breadcrumb Header (if category selected) -->
            @if(isset($selectedCategory))
            <div class="mb-6">
                <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                    <a href="{{ route('home') }}" class="hover:text-[#EC1C25]">Home</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <span class="text-gray-900 font-medium">{{ $selectedCategory->name }}</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1">{{ $selectedCategory->name }}</h1>
                <p class="text-gray-600 text-sm">{{ $products->total() }} produk ditemukan</p>
            </div>
            @endif

            <!-- Header Result -->
            <div class="flex flex-col sm:flex-row justify-between items-center bg-white p-4 rounded-xl border border-gray-200 shadow-sm mb-6">
                <div>
                    @if(request('q'))
                        <h1 class="text-lg text-gray-600">Hasil pencarian untuk "<span class="font-bold text-gray-900">{{ request('q') }}</span>"</h1>
                    @else
                        <h1 class="text-lg font-bold text-gray-900">Semua Produk</h1>
                    @endif
                    <p class="text-sm text-gray-500 mt-1">Menampilkan {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} produk</p>
                </div>
                
                <div class="mt-4 sm:mt-0 flex items-center gap-3">
                    <span class="text-sm text-gray-500 hidden sm:inline">Urutkan:</span>
                    <select name="sort" form="filterForm" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block p-2 cursor-pointer">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                    </select>
                </div>
            </div>

            <!-- Product Grid -->
            @if($products->isEmpty())
                <div class="flex flex-col items-center justify-center py-20 text-center bg-white rounded-xl border border-gray-200">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4 text-gray-400">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Tidak ditemukan</h3>
                    <p class="text-gray-500 mb-6 max-w-md">Coba kurangi filter atau gunakan kata kunci pencarian yang berbeda.</p>
                    <a href="{{ route('search.index') }}" class="px-6 py-2 bg-[#EC1C25] text-white rounded-full font-medium hover:bg-[#c4161e] transition-shadow shadow-md hover:shadow-lg">
                        Reset Filter
                    </a>
                </div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
                    @foreach($products as $product)
                        <div class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col h-full group overflow-hidden relative">
                            
                            <!-- Link Full Card -->
                            <a href="{{ route('product.show', $product->product_id) }}" class="absolute inset-0 z-10"></a>

                            <!-- Image -->
                            <div class="relative block h-48 overflow-hidden bg-gray-100">
                                <img class="object-cover w-full h-full group-hover:scale-110 transition duration-700"
                                     src="{{ $product->main_image ? asset('storage/'.$product->main_image) : 'https://placehold.co/400x400?text=No+Image' }}"
                                     alt="{{ $product->title }}">
                                
                                @if($product->condition == 'new')
                                    <span class="absolute top-2 right-2 bg-green-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm z-20">BARU</span>
                                @else
                                    <span class="absolute top-2 right-2 bg-gray-600 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm z-20">BEKAS</span>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="p-4 flex flex-col flex-grow relative z-20 pointer-events-none"> 
                                <h5 class="text-sm font-medium text-gray-900 line-clamp-2 leading-snug min-h-[40px] group-hover:text-[#EC1C25] transition-colors mb-2">
                                    {{ $product->title }}
                                </h5>
                                <p class="text-lg font-bold text-gray-900 mb-3">
                                    Rp{{ number_format($product->price, 0, ',', '.') }}
                                </p>

                                <div class="mt-auto pt-3 border-t border-gray-50 flex items-center justify-between text-xs text-gray-500">
                                    <div class="flex items-center gap-1.5 max-w-[70%]">
                                        <div class="w-5 h-5 rounded-full bg-gray-200 overflow-hidden flex-shrink-0">
                                             <img src="{{ $product->seller->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($product->seller->name).'&background=random' }}" class="w-full h-full object-cover">
                                        </div>
                                        <span class="truncate">{{ $product->seller->name }}</span>
                                    </div>
                                    <span>{{ $product->location ?? 'Bandung' }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-10 mb-8 flex justify-center">
                    {{ $products->links('pagination::tailwind') }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
