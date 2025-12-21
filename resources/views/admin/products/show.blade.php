@extends('layouts.admin')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Review Produk</h1>
        <p class="text-sm text-gray-500 mt-1">Detail produk #{{ $product->product_id }} untuk moderasi.</p>
    </div>
    <a href="{{ route('admin.products') }}" class="flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-900 bg-white border border-gray-200 hover:bg-gray-50 px-4 py-2 rounded-xl transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- Kolom Kiri: Gambar & Info Utama -->
    <div class="lg:col-span-2 space-y-8">
        <div class="bg-white rounded-2xl shadow-soft border border-gray-100 p-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">{{ $product->title }}</h3>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                @foreach($product->images as $img)
                    <div class="aspect-square rounded-xl overflow-hidden border border-gray-100 bg-gray-50 group relative cursor-zoom-in">
                        <img src="{{ asset('storage/'.$img->url) }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    </div>
                @endforeach
                @if($product->images->isEmpty())
                     <div class="aspect-square rounded-xl overflow-hidden border border-gray-100 bg-gray-50 flex items-center justify-center text-gray-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                     </div>
                @endif
            </div>

            <div class="prose max-w-none text-gray-600 bg-gray-50 p-6 rounded-xl border border-gray-100">
                <h4 class="font-bold text-gray-900 text-sm uppercase tracking-wider mb-2">Deskripsi Produk</h4>
                <p class="whitespace-pre-line text-sm leading-relaxed">{{ $product->description }}</p>
            </div>
        </div>

        <!-- Tabel Detail -->
        <div class="bg-white rounded-2xl shadow-soft border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h3 class="font-bold text-gray-900">Spesifikasi Produk</h3>
            </div>
            <table class="w-full text-sm text-left text-gray-500">
                <tbody class="divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50 transition-colors">
                        <th class="px-6 py-4 font-medium text-gray-900 w-1/3 bg-gray-50/50">Harga</th>
                        <td class="px-6 py-4 font-bold text-gray-900 text-lg">Rp{{ number_format($product->price, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <th class="px-6 py-4 font-medium text-gray-900 w-1/3 bg-gray-50/50">Stok Tersedia</th>
                        <td class="px-6 py-4">{{ $product->stock }} Unit</td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <th class="px-6 py-4 font-medium text-gray-900 w-1/3 bg-gray-50/50">Kondisi</th>
                        <td class="px-6 py-4 capitalize">
                            <span class="px-2 py-1 rounded-lg text-xs font-bold {{ $product->condition === 'new' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $product->condition }}
                            </span>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <th class="px-6 py-4 font-medium text-gray-900 w-1/3 bg-gray-50/50">Kategori</th>
                        <td class="px-6 py-4">{{ $product->category->name }}</td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <th class="px-6 py-4 font-medium text-gray-900 w-1/3 bg-gray-50/50">Lokasi Pengiriman</th>
                        <td class="px-6 py-4">{{ $product->location }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Kolom Kanan: Info Seller & Aksi Admin -->
    <div class="space-y-8">

        <!-- Status Panel -->
        <div class="bg-white rounded-2xl shadow-soft border border-gray-100 p-6 relative overflow-hidden">
             <!-- Decoration -->
             <div class="absolute top-0 right-0 p-4 opacity-5">
                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>

            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4 relative z-10">Status Moderasi</h3>

            <div class="flex items-center justify-between mb-6 relative z-10">
                <span class="text-sm font-semibold text-gray-700">Status Saat Ini</span>
                @if($product->status === App\Enums\ProductStatus::Active)
                    <span class="bg-green-100 text-green-700 text-sm font-bold px-3 py-1 rounded-full border border-green-200 flex items-center gap-1 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span> Active
                    </span>
                @elseif($product->status === App\Enums\ProductStatus::Suspended)
                    <span class="bg-red-100 text-red-700 text-sm font-bold px-3 py-1 rounded-full border border-red-200 flex items-center gap-1 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span> Suspended
                    </span>
                @else
                    <span class="bg-gray-100 text-gray-700 text-sm font-bold px-3 py-1 rounded-full border border-gray-200 shadow-sm">
                        {{ ucfirst($product->status->value) }}
                    </span>
                @endif
            </div>

            <hr class="border-gray-100 mb-6 relative z-10">

            <h4 class="text-sm font-bold text-gray-900 mb-3 relative z-10">Tindakan Admin</h4>

            <form action="{{ route('admin.products.toggle_status', $product->product_id) }}" method="POST" class="relative z-10">
                @csrf
                @method('PATCH')

                @if($product->status === App\Enums\ProductStatus::Suspended)
                    <div class="p-4 mb-4 text-xs font-medium text-yellow-800 rounded-xl bg-yellow-50 border border-yellow-100" role="alert">
                        Produk ini sedang disuspend. Aktifkan kembali jika pelanggaran sudah diperbaiki.
                        @if($product->suspension_reason)
                            <div class="mt-2 text-yellow-900 font-bold border-t border-yellow-200 pt-2">
                                Alasan: {{ $product->suspension_reason }}
                            </div>
                        @endif
                    </div>
                    <button type="submit" class="w-full text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-100 font-bold rounded-xl text-sm px-5 py-3 shadow-lg shadow-green-500/20 transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                         <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Aktifkan Kembali (Restore)
                    </button>
                @else
                    <div class="mb-4">
                        <label for="reason" class="block mb-2 text-xs font-bold text-gray-500 uppercase">Alasan Suspend</label>
                        <textarea id="reason" name="reason" rows="3" class="block p-3 w-full text-sm text-gray-900 bg-gray-50 rounded-xl border border-gray-200 focus:ring-2 focus:ring-red-100 focus:border-red-500 transition-all" placeholder="Jelaskan alasan pelanggaran..."></textarea>
                    </div>
                    <button type="submit" class="w-full text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-100 font-bold rounded-xl text-sm px-5 py-3 shadow-lg shadow-red-500/20 transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Suspend Produk Ini
                    </button>
                @endif
            </form>
        </div>

        <!-- Info Penjual -->
        <div class="bg-white rounded-2xl shadow-soft border border-gray-100 p-6">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Informasi Penjual</h3>
            <div class="flex items-center gap-4 mb-6">
                <img class="w-14 h-14 rounded-full border-2 border-white shadow-md object-cover" src="{{ $product->seller->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($product->seller->name).'&background=1e293b&color=fff' }}" alt="">
                <div>
                    <div class="font-bold text-gray-900 text-lg">{{ $product->seller->name }}</div>
                    <div class="text-xs text-gray-500 font-medium">{{ $product->seller->email }}</div>
                </div>
            </div>
            <div class="space-y-3 text-sm text-gray-600 bg-gray-50 p-4 rounded-xl border border-gray-100">
                <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                    <span class="text-gray-400 font-medium text-xs uppercase">Bergabung</span>
                    <span class="font-bold text-gray-800">{{ $product->seller->created_at->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                    <span class="text-gray-400 font-medium text-xs uppercase">Total Produk</span>
                    <span class="font-bold text-gray-800">{{ $product->seller->products->count() }} Item</span>
                </div>
                <div class="flex justify-between items-center pt-1">
                    <span class="text-gray-400 font-medium text-xs uppercase">Status Akun</span>
                    <span class="font-bold flex items-center gap-1 {{ $product->seller->status === 'active' ? 'text-green-600' : 'text-red-600' }}">
                         @if($product->seller->status === 'active')
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                         @endif
                         {{ ucfirst($product->seller->status) }}
                    </span>
                </div>
            </div>
            
            <a href="{{ route('admin.users.show', $product->seller->user_id) }}" class="block w-full text-center text-gray-900 bg-white border border-gray-200 hover:bg-gray-50 font-bold rounded-xl text-sm px-4 py-3 mt-4 transition-colors shadow-sm">
                Lihat Detail User
            </a>
        </div>

    </div>
</div>
@endsection
