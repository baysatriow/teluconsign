@extends('layouts.app')

@section('content')
<div class="max-w-screen-xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Riwayat Pembelian</h1>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <!-- Total Pesanan -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    <p class="text-xs text-gray-600">Total Pesanan</p>
                </div>
            </div>
        </div>

        <!-- Belum Bayar -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-yellow-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_payment'] }}</p>
                    <p class="text-xs text-gray-600">Belum Bayar</p>
                </div>
            </div>
        </div>

        <!-- Lunas -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['paid'] }}</p>
                    <p class="text-xs text-gray-600">Lunas</p>
                </div>
            </div>
        </div>

        <!-- Selesai -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['completed'] }}</p>
                    <p class="text-xs text-gray-600">Selesai</p>
                </div>
            </div>
        </div>
    </div>

    @if($orders->isEmpty())
        <div class="bg-white border border-gray-200 rounded-xl p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            <h3 class="text-lg font-medium text-gray-900">Belum ada pesanan</h3>
            <p class="text-gray-500 mb-6">Kamu belum melakukan transaksi apapun.</p>
            <a href="{{ route('home') }}" class="text-white bg-[#EC1C25] hover:bg-[#c4161e] font-medium rounded-lg text-sm px-6 py-3 transition-colors">
                Mulai Belanja
            </a>
        </div>
    @else
        <div class="space-y-6">
            @foreach($orders as $order)
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                    <!-- Header Pesanan -->
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex items-center gap-4 text-sm text-gray-600">
                            <span class="font-bold text-gray-900">{{ $order->created_at->format('d M Y') }}</span>
                            <span class="px-2 py-1 bg-gray-200 rounded text-xs select-all">{{ $order->code }}</span>
                            <span class="hidden sm:inline">&bull;</span>
                            <span class="font-medium text-gray-900">{{ $order->seller->name ?? 'Toko' }}</span>
                        </div>
                        
                        <div>
                            @php
                                $statusColor = match($order->payment_status) {
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'paid' => 'bg-green-100 text-green-800',
                                    'failed' => 'bg-red-100 text-red-800',
                                    'settlement' => 'bg-blue-100 text-blue-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                                $statusLabel = match($order->payment_status) {
                                    'pending' => 'Belum Bayar',
                                    'paid' => 'Dibayar',
                                    'settlement' => 'Lunas',
                                    'failed' => 'Gagal',
                                    default => $order->payment_status
                                };
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $statusColor }}">
                                {{ $statusLabel }}
                            </span>
                        </div>
                    </div>

                    <!-- List Produk -->
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row gap-6">
                            <div class="flex-grow space-y-4">
                                @foreach($order->items as $item)
                                    <div class="flex gap-4">
                                        <a href="{{ route('product.show', $item->product_id) }}" class="w-16 h-16 bg-gray-100 rounded border border-gray-200 overflow-hidden flex-shrink-0 block">
                                             <img src="{{ $item->product && $item->product->main_image ? asset('storage/'.$item->product->main_image) : 'https://placehold.co/100?text=Produk' }}" class="w-full h-full object-cover">
                                        </a>
                                        <div>
                                            <a href="{{ route('product.show', $item->product_id) }}" class="text-sm font-bold text-gray-900 line-clamp-1 hover:text-[#EC1C25]">{{ $item->product_title }}</a>
                                            <p class="text-xs text-gray-500 mt-1">{{ $item->quantity }} x Rp{{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="sm:text-right flex flex-col justify-center border-t sm:border-t-0 sm:border-l border-gray-100 pt-4 sm:pt-0 sm:pl-6 min-w-[150px]">
                                <p class="text-xs text-gray-500">Total Belanja</p>
                                <p class="text-lg font-bold text-[#EC1C25] mb-2">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                
                                @if($order->payment_status == 'pending')
                                    <button onclick="payNow('{{ $order->order_id }}')" class="text-xs text-white bg-[#EC1C25] hover:bg-[#c4161e] px-4 py-2 rounded transition-colors font-bold shadow-md">
                                        Bayar Sekarang
                                    </button>
                                @endif
                                
                                @if($order->status == 'shipped')
                                    <button class="mt-2 text-xs text-white bg-green-500 hover:bg-green-600 px-4 py-2 rounded transition-colors font-bold shadow-md">
                                        Pesanan Diterima
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8 flex justify-center">
            {{ $orders->links('pagination::tailwind') }}
        </div>
    @endif
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    function payNow(orderId) {
        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang menyiapkan pembayaran',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        fetch(`/orders/${orderId}/pay`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            Swal.close();
            if(data.status === 'success') {
                window.snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        Swal.fire('Berhasil', 'Pembayaran berhasil!', 'success').then(() => location.reload());
                    },
                    onPending: function(result) {
                        Swal.fire('Pending', 'Menunggu pembayaran...', 'info').then(() => location.reload());
                    },
                    onError: function(result) {
                        Swal.fire('Gagal', 'Pembayaran gagal.', 'error');
                    },
                    onClose: function() {
                        // Do nothing
                        console.log('closed');
                    }
                });
            } else {
                Swal.fire('Gagal', data.message, 'error');
            }
        })
        .catch(err => {
             Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
             console.error(err);
        });
    }
</script>
@endsection
