@extends('layouts.app')

@section('content')
<div class="max-w-screen-xl mx-auto px-4 sm:px-6 py-12">
    <!-- Page Header -->
    <div class="mb-10 animate-fade-in-down">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Riwayat Pembelian</h1>
        <p class="mt-2 text-gray-500">Cek status pesanan dan riwayat belanja Anda di sini.</p>
    </div>

    <!-- Tabs Navigation -->
    <div class="mb-8 border-b border-gray-100">
        <div class="flex space-x-2 overflow-x-auto pb-2 no-scrollbar" aria-label="Tabs">
            @php
                $tabs = [
                    ['id' => 'all', 'label' => 'Semua', 'count' => $stats['total']],
                    ['id' => 'pending', 'label' => 'Belum Bayar', 'count' => $stats['pending_payment']],
                    ['id' => 'processed', 'label' => 'Dikemas', 'count' => $stats['processed']],
                    ['id' => 'shipped', 'label' => 'Dikirim', 'count' => $stats['shipped']],
                    ['id' => 'completed', 'label' => 'Selesai', 'count' => $stats['completed']],
                    ['id' => 'cancelled', 'label' => 'Dibatalkan', 'count' => 0], // Assuming no specific stat key for cancelled in passed data, or acceptable to be 0/null
                ];
            @endphp

            @foreach($tabs as $tab)
                <a href="{{ route('orders.index', ['status' => $tab['id']]) }}" 
                   class="{{ $status === $tab['id'] 
                        ? 'bg-[#EC1C25] text-white shadow-lg shadow-red-200' 
                        : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }} 
                        group flex items-center px-5 py-2.5 rounded-full text-sm font-bold transition-all whitespace-nowrap min-w-fit">
                    {{ $tab['label'] }}
                    @if(isset($tab['count']) && $tab['count'] > 0)
                        <span class="{{ $status === $tab['id'] ? 'bg-white text-[#EC1C25]' : 'bg-gray-100 text-gray-600' }} ml-2 py-0.5 px-2 rounded-full text-xs transition-colors">
                            {{ $tab['count'] }}
                        </span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    @if($orders->isEmpty())
        <div class="bg-white border-2 border-dashed border-gray-200 rounded-3xl p-16 text-center animate-fade-in-up">
            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900">Belum ada pesanan</h3>
            <p class="text-gray-500 mb-8 mt-2 max-w-sm mx-auto">Tampaknya Anda belum melakukan transaksi apapun. Yuk mulai jelajahi produk kami!</p>
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-8 py-3.5 border border-transparent text-sm font-bold rounded-2xl text-white bg-[#EC1C25] hover:bg-[#c4161e] shadow-lg shadow-red-100 transition-all hover:-translate-y-1">
                Mulai Belanja
            </a>
        </div>
    @else
        <div class="space-y-6 animate-fade-in-up">
            @foreach($orders as $order)
                <div class="bg-white border border-gray-100 rounded-3xl overflow-hidden shadow-sm hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-300 group">
                    <!-- Header Pesanan -->
                    <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-3">
                                @php
                                    $photoUrl = $order->seller->profile?->photo_url ?: $order->seller->photo_url;
                                    $shopImg = $photoUrl 
                                        ? (str_starts_with($photoUrl, 'http') ? $photoUrl : asset('storage/'.$photoUrl))
                                        : 'https://ui-avatars.com/api/?name='.urlencode($order->seller->name).'&background=random';
                                @endphp
                                <div class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-400 overflow-hidden">
                                    <img src="{{ $shopImg }}" alt="{{ $order->seller->name }}" class="w-full h-full object-cover">
                                </div>
                                <a href="{{ route('shop.show', $order->seller->username ?? $order->seller->user_id) }}" class="font-bold text-gray-900 text-sm hover:text-[#EC1C25] transition-colors">{{ $order->seller->name ?? 'Toko' }}</a>
                            </div>
                            <span class="text-gray-300">|</span>
                            <span class="text-xs font-mono text-gray-500 bg-white px-2 py-1 rounded border border-gray-200 select-all">{{ $order->code }}</span>
                            <span class="text-xs text-gray-400 hidden sm:inline">{{ $order->created_at->format('d M Y') }}</span>
                        </div>
                        
                        <div>
                            @php
                                $statusStyles = match($order->payment_status) {
                                    'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                    'paid' => 'bg-green-100 text-green-800 border-green-200',
                                    'failed' => 'bg-red-100 text-red-800 border-red-200',
                                    'settlement' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    default => 'bg-gray-100 text-gray-800 border-gray-200'
                                };
                                $statusLabel = match($order->payment_status) {
                                    'pending' => 'Belum Bayar',
                                    'paid' => 'Dibayar',
                                    'settlement' => 'Lunas',
                                    'failed' => 'Gagal',
                                    default => ucfirst($order->payment_status)
                                };
                                // Check order status for shipping/completed overrides visually
                                if ($order->status == 'shipped') {
                                    $statusStyles = 'bg-purple-100 text-purple-800 border-purple-200';
                                    $statusLabel = 'Dikirim';
                                } elseif ($order->status == 'completed') {
                                    $statusStyles = 'bg-green-100 text-green-800 border-green-200';
                                    $statusLabel = 'Selesai';
                                } elseif ($order->status == 'cancelled') {
                                    $statusStyles = 'bg-red-50 text-red-600 border-red-100';
                                    $statusLabel = 'Dibatalkan';
                                }
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border {{ $statusStyles }}">
                                {{ $statusLabel }}
                            </span>
                        </div>
                    </div>

                    <!-- List Produk & Actions -->
                    <div class="p-6">
                        <div class="flex flex-col lg:flex-row gap-8">
                            <!-- Left: Products -->
                            <div class="flex-grow space-y-5">
                                @foreach($order->items as $item)
                                    <div class="flex gap-4 group/item">
                                        <a href="{{ route('product.show', $item->product->slug) }}" class="w-20 h-20 bg-gray-100 rounded-xl border border-gray-200 overflow-hidden flex-shrink-0 relative">
                                             <img src="{{ $item->product && $item->product->main_image ? asset('storage/'.$item->product->main_image) : 'https://placehold.co/100?text=IMG' }}" class="w-full h-full object-cover group-hover/item:scale-105 transition-transform duration-500">
                                        </a>
                                        <div class="py-1">
                                            <a href="{{ route('product.show', $item->product->slug) }}" class="text-sm font-bold text-gray-900 line-clamp-2 hover:text-[#EC1C25] transition-colors mb-1">{{ $item->product_title }}</a>
                                            <div class="flex items-center text-xs text-gray-500 gap-2">
                                                <span>{{ $item->quantity }} barang</span>
                                                <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                                <span>Rp{{ number_format($item->unit_price, 0, ',', '.') }}</span>
                                            </div>
                                            
                                            <!-- Review Button / Status -->
                                            @if($order->status === 'completed')
                                                <div class="mt-2">
                                                    @if($item->product->currentUserReview)
                                                        <span class="inline-flex items-center gap-1 text-xs font-bold text-yellow-500 bg-yellow-50 px-2 py-1 rounded-lg border border-yellow-100">
                                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                            Ulasan Terkirim
                                                        </span>
                                                    @else
                                                        <button onclick='openReviewModal({{ $item->product_id }}, {{ $order->order_id }}, "{{ e($item->product_title) }}", "Rp{{ number_format($item->unit_price, 0, ',', '.') }}", "{{ $item->product && $item->product->main_image ? asset("storage/".$item->product->main_image) : "https://placehold.co/100?text=IMG" }}")' 
                                                                class="inline-flex items-center gap-1.5 text-xs font-bold text-[#EC1C25] border border-[#EC1C25] px-4 py-2 rounded-xl hover:bg-red-50 transition-all shadow-sm">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                                                            Beri Ulasan
                                                        </button>
                                                    @endif
                                                </div>
                                            @elseif($order->status === 'completed_old')
                                                 <button class="mt-2 text-xs font-bold text-[#EC1C25] hover:underline">Beli Lagi</button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Right: Total & Buttons -->
                            <div class="lg:w-72 lg:border-l border-gray-100 lg:pl-8 flex flex-col justify-center gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 font-medium mb-1">Total Belanja</p>
                                    <p class="text-xl font-extrabold text-[#EC1C25]">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                </div>
                                
                                <div class="flex flex-col gap-2.5">
                                    @if($order->payment_status == 'pending' && $order->status != 'cancelled')
                                        <button onclick="payNow('{{ $order->order_id }}')" class="w-full text-center py-2.5 px-4 bg-[#EC1C25] text-white rounded-xl hover:bg-[#c4161e] font-bold shadow-lg shadow-red-100 transition-all hover:-translate-y-0.5 active:translate-y-0 text-sm">
                                            Bayar Sekarang
                                        </button>
                                    @endif
                                    
                                    <a href="{{ route('orders.show', $order->order_id) }}" class="w-full text-center py-2.5 px-4 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 hover:text-gray-900 font-bold transition-all text-sm">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-12 flex justify-center">
            {{ $orders->links('pagination::tailwind') }}
        </div>
    @endif
</div>

<x-review-modal />

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    function payNow(orderId) {
        Swal.fire({
            title: 'Memproses Pembayaran',
            text: 'Mohon tunggu sebentar...',
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
                if(data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else if(data.snap_token) {
                    window.snap.pay(data.snap_token, {
                        onSuccess: function(result) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Pembayaran Berhasil!',
                                text: 'Terima kasih telah berbelanja.',
                                confirmButtonColor: '#EC1C25'
                            }).then(() => location.reload());
                        },
                        onPending: function(result) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Menunggu Pembayaran',
                                text: 'Silakan selesaikan pembayaran Anda.',
                                confirmButtonColor: '#EC1C25'
                            }).then(() => location.reload());
                        },
                        onError: function(result) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Pembayaran Gagal',
                                text: 'Terjadi kesalahan saat memproses pembayaran.',
                                confirmButtonColor: '#EC1C25'
                            });
                        },
                        onClose: function() {}
                    });
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.message,
                    confirmButtonColor: '#EC1C25'
                });
            }
        })
        .catch(err => {
             Swal.fire({
                icon: 'error',
                title: 'Error Sistem',
                text: 'Terjadi kesalahan koneksi. Silakan coba lagi.',
                confirmButtonColor: '#EC1C25'
            });
             console.error(err);
        });
    }
</script>
@endsection
