@extends('layouts.settings')

@section('settings_content')

<div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Produk Saya</h1>
        <p class="text-gray-500 text-sm">Kelola stok dan informasi barang dagangan Anda.</p>
    </div>
    <a href="{{ route('products.create') }}" class="bg-[var(--tc-btn-bg)] text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        Tambah Produk
    </a>
</div>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    });
</script>
@endif

@if($products->isEmpty())
<div class="flex flex-col items-center justify-center py-16 bg-white rounded-xl border-2 border-dashed border-gray-300 text-center">
    <div class="bg-gray-50 p-4 rounded-full mb-4">
        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
    </div>
    <h3 class="text-lg font-medium text-gray-900">Belum ada produk</h3>
    <p class="text-gray-500 text-sm mt-1 max-w-sm">Produk yang Anda tambahkan akan muncul di sini. Mulai jualan sekarang!</p>
    <a href="{{ route('products.create') }}" class="mt-6 text-indigo-600 hover:text-indigo-800 font-semibold text-sm hover:underline">
        + Tambah Produk Pertama
    </a>
</div>
@else
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($products as $product)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300 group flex flex-col h-full">

        <div class="relative h-48 w-full bg-gray-100 overflow-hidden">
            @if($product->main_image)
                <img src="{{ asset('storage/' . $product->main_image) }}" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-500">
            @else
                <div class="h-full w-full flex flex-col items-center justify-center text-gray-400 bg-gray-50">
                    <svg class="w-10 h-10 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span class="text-xs font-medium">No Image</span>
                </div>
            @endif

            <div class="absolute top-3 right-3">
                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide shadow-sm backdrop-blur-sm
                    {{ $product->status->value == 'active' ? 'bg-green-500/90 text-white' :
                       ($product->status->value == 'sold' ? 'bg-yellow-500/90 text-white' : 'bg-gray-500/90 text-white') }}">
                    {{ $product->status->value }}
                </span>
            </div>
        </div>

        <div class="p-5 flex-1 flex flex-col">
            <div class="mb-1">
                <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider bg-indigo-50 px-2 py-0.5 rounded">
                    {{ $product->category->name ?? 'Tanpa Kategori' }}
                </span>
            </div>

            <h3 class="font-bold text-gray-800 text-lg leading-tight mb-2 line-clamp-2 hover:text-indigo-600 transition-colors">
                <a href="{{ route('products.show', $product->product_id) }}">
                    {{ $product->title }}
                </a>
            </h3>

            <p class="text-xl font-bold text-gray-900 mt-auto">
                Rp {{ number_format($product->price, 0, ',', '.') }}
            </p>

            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100 text-xs text-gray-500">
                <div class="flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    Stok: <span class="font-semibold text-gray-700 ml-1">{{ $product->stock }}</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ $product->created_at->diffForHumans() }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-3 border-t border-gray-200 bg-gray-50 divide-x divide-gray-200">

            {{-- VIEW BUTTON --}}
            <a href="{{ route('products.show', $product->product_id) }}"
               class="flex items-center justify-center py-3 text-xs font-bold text-gray-600 hover:bg-white hover:text-blue-600 transition-colors group/btn"
               title="Lihat Detail">
                <svg class="w-4 h-4 mr-1 text-gray-400 group-hover/btn:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                Detail
            </a>

            {{-- EDIT BUTTON --}}
            <a href="{{ route('products.edit', $product->product_id) }}"
               class="flex items-center justify-center py-3 text-xs font-bold text-gray-600 hover:bg-white hover:text-indigo-600 transition-colors group/btn"
               title="Edit Produk">
                <svg class="w-4 h-4 mr-1 text-gray-400 group-hover/btn:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit
            </a>

            {{-- DELETE BUTTON (Dengan SweetAlert) --}}
            <form id="delete-form-{{ $product->product_id }}" action="{{ route('products.destroy', $product->product_id) }}" method="POST" class="contents">
                @csrf
                @method('DELETE')
                <button type="button" onclick="confirmDelete({{ $product->product_id }}, '{{ $product->title }}')"
                        class="flex items-center justify-center py-3 text-xs font-bold text-gray-600 hover:bg-white hover:text-red-600 transition-colors group/btn"
                        title="Hapus Produk">
                    <svg class="w-4 h-4 mr-1 text-gray-400 group-hover/btn:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Hapus
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>

<div class="mt-8">
    {{ $products->links() }}
</div>
@endif

<script>
    // Fungsi Konfirmasi Hapus dengan SweetAlert2
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Produk?',
            text: `Anda yakin ingin menghapus "${name}"? Data yang dihapus tidak bisa dikembalikan.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444', // Warna Merah Tailwind
            cancelButtonColor: '#3d4c67', // Warna Utama Custom
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>

@endsection
