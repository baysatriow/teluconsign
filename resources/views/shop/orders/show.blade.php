@extends('layouts.seller')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('shop.orders') }}" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Pesanan</h1>
            <p class="text-sm text-gray-500">No. Pesanan: <span class="font-mono font-medium text-gray-900">{{ $order->code }}</span></p>
        </div>
        <div class="ml-auto">
            @php
                $statusColors = [
                    'pending'   => 'yellow',
                    'paid'      => 'blue',
                    'processed' => 'indigo',
                    'shipped'   => 'purple',
                    'delivered' => 'teal',
                    'completed' => 'green',
                    'cancelled' => 'red',
                    'refunded'  => 'pink',
                ];
                $color = $statusColors[$order->status] ?? 'gray';
            @endphp
            <span class="inline-flex items-center gap-1.5 bg-{{ $color }}-50 text-{{ $color }}-700 text-sm font-bold px-3 py-1.5 rounded-lg border border-{{ $color }}-200 uppercase tracking-wide">
                <span class="w-2 h-2 rounded-full bg-{{ $color }}-500"></span>
                {{ $order->status }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Tracking & Items -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Tracking Timeline -->
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 relative overflow-hidden">
                <h3 class="font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-telu-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Lacak Pengiriman
                </h3>
                
                <!-- Timeline Steps -->
                <div class="relative">
                    @php
                        $steps = [
                            'pending'   => ['label' => 'Pesanan Dibuat', 'desc' => 'Menunggu Pembayaran'],
                            'paid'      => ['label' => 'Dibayar', 'desc' => 'Pembayaran Diverifikasi'],
                            'processed' => ['label' => 'Diproses', 'desc' => 'Penjual menyiapkan barang'],
                            'shipped'   => ['label' => 'Dikirim', 'desc' => 'Sedang dalam perjalanan'],
                            'delivered' => ['label' => 'Sampai', 'desc' => 'Paket diterima pembeli'],
                            'completed' => ['label' => 'Selesai', 'desc' => 'Transaksi Selesai'],
                        ];
                        
                        $currentFound = false;
                        $statusKeys = array_keys($steps);
                        $currentStatusIndex = array_search($order->status, $statusKeys);
                        if ($currentStatusIndex === false) $currentStatusIndex = -1;
                    @endphp

                    <div class="absolute left-3.5 top-2 bottom-0 w-0.5 bg-gray-100"></div>

                    <div class="space-y-6 relative">
                        @foreach($steps as $key => $step)
                            @php
                                $index = array_search($key, $statusKeys);
                                $isActive = $index <= $currentStatusIndex;
                                $isCurrent = $index === $currentStatusIndex;
                            @endphp
                            <div class="flex gap-4">
                                <div class="relative z-10 flex-none w-8 h-8 rounded-full flex items-center justify-center border-2 {{ $isActive ? 'bg-green-500 border-green-500 text-white' : 'bg-white border-gray-200 text-gray-300' }}">
                                    @if($isActive) 
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    @else
                                        <div class="w-2 h-2 rounded-full bg-gray-200"></div>
                                    @endif
                                </div>
                                <div class="pt-1">
                                    <p class="text-sm font-bold {{ $isActive ? 'text-gray-900' : 'text-gray-400' }}">{{ $step['label'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $step['desc'] }}</p>
                                    @if($isCurrent)
                                        <p class="text-xs text-green-600 font-medium mt-1">Status Saat Ini</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-900">Rincian Produk</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                    <div class="p-4 flex gap-4">
                        <div class="w-16 h-16 rounded-lg bg-gray-100 overflow-hidden flex-none border border-gray-200">
                            @if($item->product->images->isNotEmpty())
                                <img src="{{ asset('storage/' . $item->product->images->first()->url) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">No IMG</div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900 line-clamp-2">{{ $item->product_title_snapshot }}</h4>
                            <div class="flex items-center gap-2 mt-1 text-xs text-gray-500">
                                <span>{{ $item->quantity }} x Rp{{ number_format($item->unit_price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-900">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Right Column: Info & Actions -->
        <div class="space-y-6">
            
            <!-- Actions -->
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                <h3 class="font-bold text-gray-900 mb-4">Aksi Pesanan</h3>
                <div class="flex flex-col gap-3">
                    <!-- Flow Buttons (Simulation) -->
                    @if($order->status === 'pending')
                         <form action="{{ route('shop.orders.update_status', $order) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="paid">
                            <button class="w-full py-2.5 px-4 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 font-medium transition-colors text-sm">
                                Simulasi Bayar (Demo)
                            </button>
                        </form>
                    @elseif($order->status === 'paid')
                        <form action="{{ route('shop.orders.update_status', $order) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="processed">
                            <button class="w-full py-2.5 px-4 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition-colors text-sm">
                                Proses Pesanan
                            </button>
                        </form>
                    @elseif($order->status === 'processed')
                        <form action="{{ route('shop.orders.update_status', $order) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="shipped">
                            <button class="w-full py-2.5 px-4 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium transition-colors text-sm">
                                Kirim Barang (Input Resi Dummy)
                            </button>
                        </form>
                    @elseif($order->status === 'shipped')
                         <form action="{{ route('shop.orders.update_status', $order) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="delivered">
                            <button class="w-full py-2.5 px-4 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-medium transition-colors text-sm">
                                Konfirmasi Sampai (Simulasi)
                            </button>
                        </form>
                    @elseif($order->status === 'delivered')
                         <form action="{{ route('shop.orders.update_status', $order) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="completed">
                            <button class="w-full py-2.5 px-4 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition-colors text-sm">
                                Selesai
                            </button>
                        </form>
                    @endif

                    @if(!in_array($order->status, ['completed', 'cancelled']))
                        <button class="w-full py-2.5 px-4 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors text-sm">
                            Hubungi Pembeli
                        </button>
                    @endif
                </div>
            </div>

            <!-- Shipping Info -->
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                <h3 class="font-bold text-gray-900 mb-3 text-sm uppercase tracking-wide text-gray-500">Alamat Pengiriman</h3>
                <div class="text-sm text-gray-700 leading-relaxed">
                    {!! $order->formatted_address !!}
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-500 font-medium mb-1">Kurir</p>
                    @if($order->shipment && $order->shipment->carrier)
                        <p class="text-sm font-bold text-gray-900">{{ $order->shipment->carrier->name }} - {{ $order->shipment->service_code }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <p class="text-xs text-gray-500">Resi:</p>
                            @if($order->shipment->tracking_number)
                                <p class="text-xs font-mono font-bold text-gray-900 bg-gray-100 px-2 py-0.5 rounded">{{ $order->shipment->tracking_number }}</p>
                            @else
                                <span class="text-xs text-gray-400 italic">Belum ada</span>
                            @endif
                        </div>
                    @else
                        <p class="text-sm font-bold text-gray-900">Kurir tidak tersedia</p>
                        <p class="text-xs text-gray-400">Data pengiriman belum lengkap</p>
                    @endif
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                 <h3 class="font-bold text-gray-900 mb-3 text-sm uppercase tracking-wide text-gray-500">Rincian Pembayaran</h3>
                 <div class="space-y-2 text-sm">
                 <div class="space-y-2 text-sm">
                     <!-- Income -->
                     <div class="flex justify-between text-gray-600">
                         <span>Harga Produk</span>
                         <span>Rp{{ number_format($order->subtotal_amount, 0, ',', '.') }}</span>
                     </div>
                     <div class="flex justify-between text-gray-600">
                         <span>Ongkos Kirim Dibayar Pembeli</span>
                         <span>Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                     </div>
                     
                     <div class="border-t border-dashed border-gray-200 my-2"></div>
                     
                     <div class="flex justify-between font-bold text-gray-900">
                         <span>Total Dibayar Pembeli</span>
                         <span>Rp{{ number_format($order->subtotal_amount + $order->shipping_cost, 0, ',', '.') }}</span>
                     </div>

                     <div class="border-t border-dashed border-gray-200 my-2"></div>

                     <!-- Deductions -->
                     <div class="flex justify-between text-red-500">
                         <span>Potongan Ongkos Kirim</span>
                         <span>-Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                     </div>
                     <div class="flex justify-between text-red-500">
                         <span>Biaya Platform</span>
                         <span>-Rp{{ number_format($order->platform_fee_seller, 0, ',', '.') }}</span>
                     </div>

                     <div class="border-t border-gray-100 pt-2 flex justify-between font-bold text-gray-900 text-base">
                         <span>Penghasilan Bersih</span>
                         <span>Rp{{ number_format($order->seller_earnings, 0, ',', '.') }}</span>
                     </div>
                 </div>
                 </div>
            </div>

        </div>
    </div>
</div>
@endsection
