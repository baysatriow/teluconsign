@extends('layouts.app')

@section('content')
<div class="max-w-screen-xl mx-auto px-4 sm:px-6 py-12">
    <!-- Breadcrumb -->
    <nav class="flex mb-8 animate-fade-in-down" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-[#EC1C25] transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                    Beranda
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <a href="{{ route('orders.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-[#EC1C25] md:ml-2 transition-colors">Riwayat Pembelian</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 text-sm font-bold text-gray-900 md:ml-2">Detail Pesanan</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-10 animate-fade-in-down">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Detail Pesanan</h1>
            <p class="text-gray-500 mt-2 flex items-center gap-2">
                No. Pesanan: <span class="font-mono font-bold text-gray-900 bg-gray-100 px-2 py-0.5 rounded">{{ $order->code }}</span>
                <span class="text-gray-300">|</span>
                {{ $order->created_at->format('d M Y, H:i') }} WIB
            </p>
        </div>
        <div>
           @php
                $statusConfig = [
                    'pending'   => ['color' => 'yellow', 'label' => 'Belum Bayar'],
                    'paid'      => ['color' => 'green', 'label' => 'Dibayar'],
                    'processed' => ['color' => 'blue', 'label' => 'Dikemas'],
                    'shipped'   => ['color' => 'purple', 'label' => 'Dikirim'],
                    'delivered' => ['color' => 'teal', 'label' => 'Sampai'],
                    'completed' => ['color' => 'green', 'label' => 'Selesai'],
                    'cancelled' => ['color' => 'red', 'label' => 'Dibatalkan'],
                    'refunded'  => ['color' => 'pink', 'label' => 'Dikembalikan'],
                ];
                $config = $statusConfig[$order->status] ?? ['color' => 'gray', 'label' => $order->status];
                $color = $config['color'];
            @endphp
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-2 bg-{{ $color }}-50 text-{{ $color }}-700 text-sm font-extrabold px-5 py-2.5 rounded-full border border-{{ $color }}-200 shadow-sm">
                    <span class="w-2.5 h-2.5 rounded-full bg-{{ $color }}-500 animate-pulse"></span>
                    {{ $config['label'] }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-fade-in-up">
        
        <!-- Left Column: Timeline & Items -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Tracking Timeline -->
            <div class="bg-white border border-gray-100 rounded-3xl shadow-xl shadow-gray-200/40 p-8">
                <h3 class="font-extrabold text-gray-900 mb-8 flex items-center gap-3 text-lg">
                    <div class="p-2 bg-red-50 rounded-xl text-[#EC1C25]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                    Status Pesanan
                </h3>
                
                <div class="relative pl-4">
                    @php
                        $steps = [
                            'pending'   => ['label' => 'Menunggu Pembayaran', 'desc' => 'Selesaikan pembayaran agar pesanan diproses.'],
                            'paid'      => ['label' => 'Pembayaran Berhasil', 'desc' => 'Pembayaran telah kami terima.'],
                            'processed' => ['label' => 'Pesanan Diproses', 'desc' => 'Penjual sedang menyiapkan pesanan Anda.'],
                            'shipped'   => ['label' => 'Sedang Dikirim', 'desc' => 'Paket sedang dalam perjalanan ke alamat tujuan.'],
                            'delivered' => ['label' => 'Pesanan Sampai', 'desc' => 'Paket telah diterima di alamat tujuan.'],
                            'completed' => ['label' => 'Selesai', 'desc' => 'Transaksi selesai. Terima kasih telah berbelanja!'],
                        ];
                        
                        $statusKeys = array_keys($steps);
                        $currentStatusIndex = array_search($order->status, $statusKeys);
                        if ($currentStatusIndex === false) {
                             if ($order->status == 'cancelled')  $currentStatusIndex = -1; // Handle cancel specifically if needed
                             else $currentStatusIndex = -1;
                        }
                    @endphp

                    <div class="absolute left-[1.65rem] top-4 bottom-10 w-0.5 bg-gray-100"></div>

                    <div class="space-y-8 relative">
                        @foreach($steps as $key => $step)
                            @php
                                $index = array_search($key, $statusKeys);
                                $isActive = $index <= $currentStatusIndex;
                                $isCurrent = $index === $currentStatusIndex;
                            @endphp
                            <div class="flex gap-6 group">
                                <div class="relative z-10 flex-none w-10 h-10 rounded-full flex items-center justify-center border-4 transition-all duration-500 {{ $isActive ? 'bg-[#EC1C25] border-red-100 text-white shadow-lg shadow-red-200 scale-110' : 'bg-white border-gray-100 text-gray-300' }}">
                                    @if($isActive) 
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    @else
                                        <div class="w-2.5 h-2.5 rounded-full bg-gray-200"></div>
                                    @endif
                                </div>
                                <div class="pt-1.5 {{ $isActive ? 'opacity-100' : 'opacity-50' }} transition-opacity duration-300">
                                    <p class="text-base font-bold text-gray-900">{{ $step['label'] }}</p>
                                    <p class="text-sm text-gray-500 mt-1 leading-relaxed">{{ $step['desc'] }}</p>
                                    @if($isCurrent)
                                        <span class="inline-block mt-2 px-3 py-1 bg-red-50 text-[#EC1C25] text-xs font-bold rounded-full uppercase tracking-wide animate-pulse">Status Saat Ini</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="bg-white border border-gray-100 rounded-3xl shadow-xl shadow-gray-200/40 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="font-extrabold text-gray-900 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        {{ $order->seller->name ?? 'Toko' }}
                    </h3>
                    <a href="{{ route('shop.show', $order->seller_id) }}" class="text-xs font-bold text-[#EC1C25] hover:underline flex items-center gap-1">
                        Kunjungi Toko
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                    <div class="p-6 flex gap-5 hover:bg-gray-50 transition-colors group">
                        <a href="{{ route('product.show', $item->product_id) }}" class="w-24 h-24 rounded-xl bg-gray-100 overflow-hidden flex-none border border-gray-200 relative">
                            <img src="{{ $item->product && $item->product->main_image ? asset('storage/'.$item->product->main_image) : 'https://placehold.co/100?text=IMG' }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </a>
                        <div class="flex-1">
                            <a href="{{ route('product.show', $item->product_id) }}" class="text-base font-bold text-gray-900 line-clamp-2 hover:text-[#EC1C25] transition-colors mb-2">{{ $item->product_title_snapshot }}</a>
                            <p class="text-sm text-gray-500 mb-3">{{ $item->quantity }} barang x Rp{{ number_format($item->unit_price, 0, ',', '.') }}</p>
                            
                            @if($order->status === 'completed')
                                <a href="{{ route('product.show', $item->product_id) }}" class="inline-flex items-center gap-2 text-xs font-bold text-gray-600 bg-gray-100 hover:bg-[#EC1C25] hover:text-white px-4 py-2 rounded-lg transition-all">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                                    Beri Ulasan
                                </a>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-base font-extrabold text-[#EC1C25]">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Right Column: Info & Actions -->
        <div class="space-y-6">
            
            <!-- Actions -->
            @if(in_array($order->status, ['pending', 'shipped']))
            <div class="bg-white border border-gray-100 rounded-3xl shadow-xl shadow-gray-200/40 p-6">
                <h3 class="font-bold text-gray-900 mb-5 text-lg">Aksi Pesanan</h3>
                <div class="flex flex-col gap-3">
                    @if($order->payment_status === 'pending' && $order->status !== 'cancelled')
                         <button onclick="payNow('{{ $order->order_id }}')" class="w-full py-3.5 px-4 bg-[#EC1C25] text-white rounded-xl hover:bg-[#c4161e] font-bold shadow-lg shadow-red-200 transition-all transform hover:-translate-y-1 active:translate-y-0 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Bayar Sekarang
                        </button>
                    @endif

                    @if($order->status === 'shipped')
                         <button onclick="receiveOrder()" class="w-full py-3.5 px-4 bg-green-600 text-white rounded-xl hover:bg-green-700 font-bold shadow-lg shadow-green-200 transition-all transform hover:-translate-y-1 active:translate-y-0 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Pesanan Diterima
                        </button>
                    @endif
                </div>
            </div>
            @endif

            <!-- Shipping Info -->
            <div class="bg-white border border-gray-100 rounded-3xl shadow-xl shadow-gray-200/40 p-6">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <div class="p-1.5 bg-blue-50 text-blue-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    Alamat Pengiriman
                </h3>
                <div class="text-sm text-gray-600 leading-relaxed bg-gray-50 p-4 rounded-xl border border-gray-100 italic">
                    {!! $order->formatted_address !!}
                </div>
                
                <div class="mt-6">
                    <h4 class="text-gray-900 font-bold mb-3 flex items-center gap-2">
                        <div class="p-1.5 bg-yellow-50 text-yellow-600 rounded-lg">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 02-1-1h-2.586a1 1 0 01-.707-.293l-2.414-2.414a1 1 0 00-.707-.293H3.828a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293H0"></path></svg>
                        </div>
                        Jasa Pengiriman
                    </h4>
                    <div class="flex items-center gap-3">
                         <!-- Placeholder Courier Logo Logic -->
                         @php $courier = strtoupper(explode(' ', $order->shipping_courier)[0]); @endphp
                        <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center text-[10px] font-black text-gray-500 border border-gray-200">
                           {{ $courier }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ $order->shipping_courier }}</p>
                            <p class="text-xs text-gray-500">Regular Service</p>
                        </div>
                    </div>
                     @if($order->tracking_number)
                    <div class="mt-4 bg-blue-50 text-blue-800 text-xs px-4 py-3 rounded-xl border border-blue-100 flex justify-between items-center group cursor-pointer hover:bg-blue-100 transition-colors" onclick="navigator.clipboard.writeText('{{ $order->tracking_number }}'); Swal.fire({toast:true, position:'top-end', icon:'success', title:'Resi disalin', showConfirmButton:false, timer:1500})">
                        <div class="flex flex-col">
                            <span class="text-blue-500 text-[10px] font-bold uppercase">No. Resi</span>
                            <span class="font-mono font-bold text-sm">{{ $order->tracking_number }}</span>
                        </div>
                         <svg class="w-5 h-5 text-blue-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </div>
                    @else
                    <div class="mt-4 p-3 bg-gray-50 rounded-xl text-xs text-gray-400 text-center italic border border-gray-100">
                        Nomor resi belum tersedia
                    </div>
                    @endif
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="bg-white border border-gray-100 rounded-3xl shadow-xl shadow-gray-200/40 p-6">
                 <h3 class="font-bold text-gray-900 mb-5 text-lg flex items-center gap-2">
                    <div class="p-1.5 bg-green-50 text-green-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    Rincian Pembayaran
                </h3>
                 <div class="space-y-3.5 text-sm">
                     <div class="flex justify-between text-gray-600">
                         <span>Total Harga ({{ $order->items->sum('quantity') }} barang)</span>
                         <span class="font-medium text-gray-900">Rp{{ number_format($order->subtotal_amount, 0, ',', '.') }}</span>
                     </div>
                     <div class="flex justify-between text-gray-600">
                         <span>Total Ongkos Kirim</span>
                         <span class="font-medium text-gray-900">Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                     </div>
                     <div class="flex justify-between text-gray-600">
                         <span>Biaya Layanan</span>
                         <span class="font-medium text-gray-900">Rp{{ number_format($order->platform_fee, 0, ',', '.') }}</span>
                     </div>
                     
                     <div class="border-t border-dashed border-gray-200 my-2"></div>
                     
                     <div class="flex justify-between items-center">
                         <span class="font-bold text-lg text-gray-900">Total Belanja</span>
                         <span class="text-xl font-extrabold text-[#EC1C25]">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                     </div>
                 </div>
            </div>

            <!-- Help -->
            <div class="text-center">
                <a href="#" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-900 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Butuh bantuan tentang pesanan ini?
                </a>
            </div>

        </div>
    </div>
</div>

<!-- SCRIPTS -->
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
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#EC1C25'
                            }).then(() => location.reload());
                        },
                        onPending: function(result) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Menunggu Pembayaran',
                                text: 'Silakan selesaikan pembayaran Anda.',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#EC1C25'
                            }).then(() => location.reload());
                        },
                        onError: function(result) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Pembayaran Gagal',
                                text: 'Terjadi kesalahan sistem.',
                                confirmButtonText: 'Tutup',
                                confirmButtonColor: '#333'
                            });
                        },
                        onClose: function() {}
                    });
                }
            } else {
                Swal.fire('Gagal', data.message, 'error');
            }
        })
        .catch(err => {
             Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
             console.error(err);
        });
    }

    function receiveOrder() {
        Swal.fire({
            title: 'Pesanan Diterima?',
            text: "Pastikan barang sudah Anda terima dengan baik dan sesuai pesanan.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10B981', // green-500
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Terima Barang',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                 Swal.fire({
                     icon: 'info',
                     title: 'Fitur Belum Tersedia',
                     text: 'Logika untuk konfirmasi penerimaan oleh pembeli belum diimplementasikan di backend.',
                     confirmButtonColor: '#333'
                 });
            }
        })
    }
</script>
@endsection
