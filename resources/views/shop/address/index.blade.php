@extends('layouts.seller')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Alamat Toko</h2>
            <p class="text-sm text-gray-500">Pilih alamat utama untuk operasional toko Anda.</p>
        </div>
        
        <!-- Button Tambah Alamat -->
        <a href="{{ route('shop.address.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 hover:bg-black text-white font-medium rounded-xl transition-all shadow-lg shadow-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Alamat Baru
        </a>
    </div>

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-start gap-3">
        <svg class="w-6 h-6 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        <div>
            <h4 class="font-bold text-red-800">Perhatian Penting</h4>
            <p class="text-sm text-red-600 mt-1">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Container Scrollable: Max height ~3 cards (approx 600px), with padding for shadows -->
    <div class="grid grid-cols-1 gap-6 max-h-[600px] overflow-y-auto p-4 custom-scrollbar rounded-xl">
        @forelse($addresses as $addr)
        <div class="bg-white border {{ $addr->is_shop_default ? 'border-telu-red ring-1 ring-telu-red shadow-soft order-first' : 'border-gray-200' }} rounded-2xl p-6 transition-all hover:border-telu-red/50">
            
            <div class="flex flex-col md:flex-row justify-between gap-6">
                <!-- Info Alamat -->
                <div class="flex items-start gap-4 flex-grow">
                     <div class="p-3 {{ $addr->is_shop_default ? 'bg-red-50 text-telu-red' : 'bg-gray-50 text-gray-500' }} rounded-xl flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                     </div>
                     <div class="flex-grow">
                        <div class="flex items-center flex-wrap gap-2 mb-2">
                            <h4 class="font-bold text-gray-900 pr-2 mr-2 border-r border-gray-300">{{ $addr->label }}</h4>
                            <p class="text-sm font-medium text-gray-600">{{ $addr->recipient }}</p>
                        </div>
                        
                        <p class="text-gray-500 text-sm leading-relaxed mb-2">{{ $addr->phone }}</p>
                        <p class="text-gray-700 text-sm leading-relaxed max-w-xl">
                            {{ $addr->getFullAddress() }}
                        </p>
                        
                        <!-- Badges & Actions Row (Inline) -->
                        <div class="flex items-center gap-3 mt-4 pt-4 border-t border-gray-100">
                             <!-- Status Badges -->
                            @if($addr->is_shop_default)
                                <span class="bg-red-100 text-telu-red text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wide">Aktif</span>
                            @endif
                            @if($addr->is_default)
                                <span class="bg-blue-50 text-blue-600 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wide">Pribadi</span>
                            @endif

                            <div class="flex-grow"></div> <!-- Spacer -->

                            <!-- Action Buttons -->
                            <div class="flex items-center gap-2">
                                <a href="{{ route('shop.address.edit', $addr->address_id) }}" class="text-xs font-semibold text-gray-600 hover:text-telu-red bg-gray-100 hover:bg-red-50 px-3 py-1.5 rounded transition-colors flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    Edit
                                </a>

                                @if(!$addr->is_shop_default)
                                    <form action="{{ route('shop.address.delete', $addr->address_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus alamat ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-gray-600 hover:text-red-600 bg-gray-100 hover:bg-red-50 px-3 py-1.5 rounded transition-colors flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                     </div>
                </div>

                <!-- Set Default Button (Right Side or Bottom on Mobile) -->
                @if(!$addr->is_shop_default)
                    <div class="flex-shrink-0 self-end md:self-center w-full md:w-auto mt-2 md:mt-0">
                        <form action="{{ route('shop.address.setdefault', $addr->address_id) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="w-full md:w-auto text-sm font-bold text-gray-500 hover:text-telu-red bg-gray-50 hover:bg-red-50 px-4 py-2.5 rounded-xl transition-colors border border-gray-100 hover:border-red-100 shadow-sm">
                                Jadikan Utama
                            </button>
                        </form>
                    </div>
                @endif

            </div>
        </div>
        @empty
            <div class="text-center py-16 bg-white border border-dashed border-gray-300 rounded-2xl">
                <div class="mx-auto w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 text-gray-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Belum ada alamat</h3>
                <p class="text-gray-500 text-sm mb-6 max-w-md mx-auto">Anda belum memiliki alamat tersimpan. Tambahkan alamat di profil Anda untuk digunakan sebagai alamat toko.</p>
                <a href="{{ route('shop.address.create') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-telu-red text-white font-bold rounded-xl hover:bg-red-700 transition-all">
                    Tambah Alamat Baru
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
