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
                        <span class="font-medium text-gray-900">Terjual {{ rand(0, 50) }}</span>
                        <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            4.{{ rand(5,9) }} ({{ rand(10, 50) }} ulasan)
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
                             <span class="bg-blue-100 text-blue-800 text-[10px] px-1.5 py-0.5 rounded font-bold">PRO</span>
                        </div>
                        <p class="text-xs text-gray-500">{{ $product->location ?? 'Bandung' }} &bull; Online 5 menit lalu</p>
                    </div>
                    <button class="text-xs font-bold text-[#EC1C25] border border-[#EC1C25] rounded-lg px-4 py-2 hover:bg-red-50 transition-colors">
                        Kunjungi Toko
                    </button>
                </div>

                <!-- ACTION BUTTONS STICKY -->
                <div class="mt-auto bg-white border border-gray-200 p-4 rounded-xl shadow-lg sticky bottom-4 z-30">
                     @if(Auth::id() == $product->seller_id)
                        <div class="flex gap-2">
                            <a href="#" class="flex-1 text-center bg-gray-100 text-gray-700 font-bold py-3 rounded-lg hover:bg-gray-200 text-sm">Edit Produk</a>
                        </div>
                     @else
                        @if($product->stock > 0)
                            <div class="flex flex-col gap-3">
                                <div class="flex gap-3">
                                    <button onclick="addToCart(false)" id="btn-add-cart" class="flex-1 flex items-center justify-center gap-2 text-[#EC1C25] bg-white border border-[#EC1C25] hover:bg-red-50 font-bold rounded-xl text-sm px-5 py-3 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        + Keranjang
                                    </button>
                                    
                                    <form action="{{ route('product.buy', $product->product_id) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full text-white bg-[#EC1C25] hover:bg-[#c4161e] font-bold rounded-xl text-sm px-5 py-3 shadow-md hover:shadow-lg transition-all">
                                            Beli Langsung
                                        </button>
                                    </form>
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

    <!-- Related Products (Same as before but styled better) -->
    @if($relatedProducts->count() > 0)
    <div class="mt-16">
        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
            Lainnya dari Toko Ini
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($relatedProducts as $related)
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-lg transition-all group overflow-hidden">
                <a href="{{ route('product.show', $related->product_id) }}" class="relative block h-48 overflow-hidden bg-gray-100">
                    <img class="object-cover w-full h-full group-hover:scale-105 transition duration-500"
                         src="{{ $related->main_image ? asset('storage/'.$related->main_image) : 'https://placehold.co/400x400' }}"
                         alt="{{ $related->title }}">
                </a>
                <div class="p-4">
                    <h5 class="text-sm font-medium text-gray-900 line-clamp-2 mb-2 group-hover:text-[#EC1C25] transition-colors">{{ $related->title }}</h5>
                    <p class="text-base font-bold text-gray-900">Rp{{ number_format($related->price, 0, ',', '.') }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

<script>
    // 1. Image Gallery
    function changeImage(src) {
        const mainImage = document.getElementById('mainImage');
        mainImage.style.opacity = 0.5;
        setTimeout(() => {
            mainImage.src = src;
            mainImage.style.opacity = 1;
        }, 150);
    }

    // 2. AJAX Add to Cart
    function addToCart() {
        const btn = document.getElementById('btn-add-cart');
        const originalText = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin h-5 w-5 text-[#EC1C25]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                product_id: {{ $product->product_id }},
                quantity: 1
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                // Show Success Toast
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

                Toast.fire({
                    icon: 'success',
                    title: 'Berhasil masuk keranjang'
                });
                
                // Optional: Update Badge Logic via JS or reload
                // For now, user didn't ask for live badge update, but it's nice.
                location.reload(); // Simple reload to update badge
            } else {
                Swal.fire('Gagal', data.message, 'error');
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }
</script>
@endsection
