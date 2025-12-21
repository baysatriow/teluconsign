@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Manajemen Produk</h1>
    <p class="text-sm text-gray-500 mt-1">Review, pantau, dan kelola semua produk yang dijual di platform.</p>
</div>

<!-- Statistik Mini -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
    <div class="p-5 bg-white rounded-2xl shadow-soft border border-gray-100 flex flex-col justify-between group hover:-translate-y-1 transition-transform">
        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Total Produk</div>
        <div class="flex items-end justify-between">
            <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</div>
            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
        </div>
    </div>
    <div class="p-5 bg-white rounded-2xl shadow-soft border border-gray-100 flex flex-col justify-between group hover:-translate-y-1 transition-transform">
        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Aktif Tayang</div>
        <div class="flex items-end justify-between">
            <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['active']) }}</div>
            <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center text-green-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
    </div>
    <div class="p-5 bg-white rounded-2xl shadow-soft border border-gray-100 flex flex-col justify-between group hover:-translate-y-1 transition-transform">
        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Disuspend</div>
        <div class="flex items-end justify-between">
            <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['suspended']) }}</div>
            <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center text-red-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
            </div>
        </div>
    </div>
    <div class="p-5 bg-white rounded-2xl shadow-soft border border-gray-100 flex flex-col justify-between group hover:-translate-y-1 transition-transform">
        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Perlu Review</div>
        <div class="flex items-end justify-between">
            <div class="text-2xl font-bold text-gray-900">0</div>
            <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center text-orange-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-soft border border-gray-100 p-6">
    <!-- Search & Filter Form -->
    <form action="{{ route('admin.products') }}" method="GET" class="flex flex-col md:flex-row items-center gap-4 mb-8">
        <div class="relative w-full md:flex-1">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" name="q" value="{{ request('q') }}" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full pl-10 p-3 transition-all" placeholder="Cari nama produk, ID, atau penjual...">
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <select name="status" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full md:w-40 p-3 transition-all">
                <option value="">Semua Status</option>
                @foreach(App\Enums\ProductStatus::cases() as $status)
                    <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                        {{ $status->label() }}
                    </option>
                @endforeach
            </select>

            <select name="category" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full md:w-48 p-3 transition-all">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->category_id }}" {{ request('category') == $cat->category_id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="text-white bg-gray-900 hover:bg-black font-bold rounded-xl text-sm px-6 py-3 shadow-lg transition-transform hover:-translate-y-0.5">
                Filter
            </button>

            @if(request()->has('q') || request()->has('status') || request()->has('category'))
                <a href="{{ route('admin.products') }}" class="text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 font-bold rounded-xl text-sm px-4 py-3 transition-colors">
                    Reset
                </a>
            @endif
        </div>
    </form>

    <div class="overflow-x-auto rounded-xl border border-gray-100">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50/50">
                <tr>
                    <th scope="col" class="px-6 py-4">Produk</th>
                    <th scope="col" class="px-6 py-4">Kategori</th>
                    <th scope="col" class="px-6 py-4">Penjual</th>
                    <th scope="col" class="px-6 py-4">Harga</th>
                    <th scope="col" class="px-6 py-4">Status</th>
                    <th scope="col" class="px-6 py-4">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($products as $product)
                <tr class="bg-white hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <img class="w-12 h-12 rounded-lg object-cover border border-gray-200 shadow-sm" src="{{ $product->main_image ? asset('storage/'.$product->main_image) : 'https://placehold.co/100' }}" alt="">
                            <div>
                                <div class="font-bold text-gray-900">{{ Str::limit($product->title, 40) }}</div>
                                <div class="text-xs text-gray-400 mt-1">#{{ $product->product_id }} • {{ $product->created_at->format('d M Y') }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-700">
                        {{ $product->category->name ?? '-' }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                             <img class="w-6 h-6 rounded-full border border-gray-200" src="{{ $product->seller->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($product->seller->name).'&background=Random&color=fff' }}" alt="">
                            <span class="text-sm font-medium text-gray-900">{{ $product->seller->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-bold text-gray-900">
                        Rp{{ number_format($product->price, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4">
                        @if($product->status === App\Enums\ProductStatus::Active)
                            <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1 w-fit">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Active
                            </span>
                        @elseif($product->status === App\Enums\ProductStatus::Suspended)
                            <span class="bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1 w-fit">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Suspended
                            </span>
                        @else
                            <span class="bg-gray-100 text-gray-700 text-xs font-bold px-3 py-1 rounded-full w-fit">
                                {{ ucfirst($product->status->value) }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.products.show', $product->product_id) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Review Detail">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>

                            <form id="status-form-{{ $product->product_id }}" action="{{ route('admin.products.toggle_status', $product->product_id) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="reason" id="reason-input-{{ $product->product_id }}">
                                
                                @if($product->status === App\Enums\ProductStatus::Suspended)
                                    <button type="button" onclick="confirmAction('{{ $product->product_id }}', 'Aktifkan produk ini?', 'restore')" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Aktifkan">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </button>
                                @else
                                    <button type="button" onclick="confirmAction('{{ $product->product_id }}', 'Suspend produk ini?', 'suspend')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Suspend">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </button>
                                @endif
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-gray-500 py-12">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4 text-gray-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            </div>
                            <p class="font-medium">Tidak ada produk ditemukan.</p>
                            <p class="text-xs mt-1">Coba ubah filter pencarian anda.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $products->links('pagination::tailwind') }}
    </div>
</div>

<script>
    function confirmAction(productId, message, actionType) {
        let isSuspend = actionType === 'suspend';
        let confirmBtnText = isSuspend ? 'Ya, Suspend' : 'Ya, Aktifkan';
        let confirmBtnColor = isSuspend ? '#EF4444' : '#10B981';

        Swal.fire({
            title: isSuspend ? 'Suspend Produk?' : 'Aktifkan Produk?',
            text: message,
            icon: isSuspend ? 'warning' : 'info',
            showCancelButton: true,
            confirmButtonText: confirmBtnText,
            cancelButtonText: 'Batal',
            confirmButtonColor: confirmBtnColor,
            cancelButtonColor: '#E5E7EB',
            // Input hanya muncul jika Suspend
            input: isSuspend ? 'textarea' : undefined,
            inputPlaceholder: 'Masukan alasan suspend...',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl px-5 py-2.5 font-bold shadow-lg',
                cancelButton: 'rounded-xl px-5 py-2.5 text-gray-800',
                input: 'rounded-xl border-gray-300 focus:ring-red-500 focus:border-red-500 mt-4'
            },
            preConfirm: (value) => {
                if (isSuspend && !value) {
                    Swal.showValidationMessage('Anda harus menuliskan alasan suspend!')
                }
                return value;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika suspend, isi hidden input reason
                if (isSuspend) {
                    document.getElementById('reason-input-' + productId).value = result.value;
                }
                document.getElementById('status-form-' + productId).submit();
            }
        });
    }
</script>
@endsection
