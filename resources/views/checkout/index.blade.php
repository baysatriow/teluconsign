@extends('layouts.app')

@section('content')
<div class="max-w-screen-xl mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- MAIN CONTENT -->
        <div class="flex-grow space-y-6">
            <h1 class="text-2xl font-bold text-gray-900">Checkout Pengiriman</h1>

            <!-- 1. ALAMAT PENGIRIMAN -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#EC1C25]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Alamat Pengiriman
                </h3>
                
                @if($mainAddress)
                    <div class="text-gray-700">
                        <p class="font-bold">{{ Auth::user()->name }} <span class="font-normal text-gray-500">({{ $mainAddress->phone }})</span></p>
                        <p class="mt-1">{{ $mainAddress->getFullAddress() }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $mainAddress->city }}, {{ $mainAddress->province }} {{ $mainAddress->postal_code }}</p>
                    </div>
                    <button type="button" class="mt-4 text-sm font-medium text-[#EC1C25] hover:text-[#c4161e] hover:underline">
                        Ganti Alamat
                    </button>
                @else
                    <div class="text-center py-4 bg-yellow-50 rounded-lg border border-yellow-100">
                        <p class="text-yellow-700 mb-2">Kamu belum mengatur alamat utama.</p>
                        <a href="{{ route('profile.index') }}" class="inline-block px-4 py-2 bg-[#EC1C25] text-white rounded-lg text-sm font-medium hover:bg-[#c4161e]">
                            Tambah Alamat
                        </a>
                    </div>
                @endif
            </div>

            <!-- 2. ITEM CHECKOUT -->
            @foreach($groupedItems as $sellerId => $items)
                @php $seller = $items->first()->product->seller; @endphp
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm store-section" data-seller="{{ $sellerId }}">
                    
                    <!-- Header Toko -->
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                        <div class="w-8 h-8 rounded-full bg-gray-100 overflow-hidden">
                            <img src="{{ $seller->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($seller->name) }}" class="w-full h-full object-cover">
                        </div>
                        <span class="font-bold text-gray-800">{{ $seller->name }}</span>
                        <span class="text-xs px-2 py-0.5 bg-gray-100 text-gray-600 rounded">{{ $seller->addresses->first()->city ?? 'Kota Toko' }}</span>
                    </div>

                    <!-- List Item -->
                    <div class="space-y-4 mb-6">
                        @foreach($items as $item)
                            <div class="flex gap-4">
                                <div class="w-16 h-16 rounded bg-gray-100 border border-gray-200 overflow-hidden flex-shrink-0">
                                    <img src="{{ $item->product->main_image ? asset('storage/'.$item->product->main_image) : 'https://placehold.co/100' }}" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-grow">
                                    <h4 class="text-sm font-medium text-gray-900 line-clamp-1">{{ $item->product->title }}</h4>
                                    <p class="text-sm text-gray-500">{{ $item->quantity }} x Rp{{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-gray-900">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- PENGIRIMAN -->
                    <div class="bg-gray-50 rounded-lg p-6 mt-4">
                        <!-- 1. Pilih Kurir -->
                        <h4 class="text-sm font-bold text-gray-800 mb-3">Pilih Kurir</h4>
                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 mb-6">
                            @foreach($couriers as $courier)
                                <label class="cursor-pointer relative">
                                    <input type="radio" name="courier-{{ $sellerId }}" value="{{ $courier }}" class="peer sr-only" onchange="checkOngkir('{{ $sellerId }}', this.value)">
                                    <div class="flex flex-col items-center justify-center p-3 border-2 border-gray-200 rounded-xl hover:border-red-200 bg-white transition-all peer-checked:border-[#EC1C25] peer-checked:bg-red-50">
                                        <!-- Placeholder Logo / Text -->
                                        <span class="font-bold text-sm text-gray-700 peer-checked:text-[#EC1C25]">{{ strtoupper($courier) }}</span>
                                        
                                        <!-- Check Icon -->
                                        <div class="absolute top-1 right-1 opacity-0 peer-checked:opacity-100 transition-opacity">
                                            <svg class="w-4 h-4 text-[#EC1C25]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <!-- 2. Pilih Layanan (Dynamic by AJAX) -->
                        <div id="service-container-{{ $sellerId }}" class="hidden">
                            <h4 class="text-sm font-bold text-gray-800 mb-3">Pilih Layanan</h4>
                            
                            <!-- Loading Indicator -->
                            <div id="loading-{{ $sellerId }}" class="hidden text-center py-4">
                                <svg class="animate-spin h-6 w-6 text-[#EC1C25] mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <p class="text-xs text-gray-500 mt-2">Cek Ongkir...</p>
                            </div>

                            <!-- Service List Grid -->
                            <div id="service-list-{{ $sellerId }}" class="grid grid-cols-1 gap-3">
                                <!-- Items injected by JS -->
                            </div>
                        </div>
                    </div>

                </div>
            @endforeach

        </div>

        <!-- SIDEBAR SUMMARY -->
        <div class="lg:w-96 flex-shrink-0">
            <div class="bg-white border border-gray-200 rounded-xl shadow-lg p-6 sticky top-24">
                <h3 class="text-lg font-bold text-gray-900 mb-5">Ringkasan Pembayaran</h3>

                <div class="space-y-3 mb-6">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600">Total Harga ({{ $groupedItems->flatten()->sum('quantity') }} barang)</span>
                        <span class="font-medium text-gray-900">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600">Total Ongkos Kirim</span>
                        <span class="font-medium text-gray-900" id="summary-shipping">Rp0</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600">Biaya Layanan</span>
                        <span class="font-medium text-gray-900">Rp{{ number_format($platformFee, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="flex justify-between items-center mb-6 pt-4 border-t border-dashed border-gray-200">
                    <span class="text-base font-bold text-gray-900">Total Tagihan</span>
                    <span class="text-xl font-extrabold text-[#EC1C25]" id="grand-total">Rp{{ number_format($subtotal + $platformFee, 0, ',', '.') }}</span>
                </div>

                <button type="button" onclick="processPayment()" id="pay-btn" class="w-full text-white bg-gray-300 font-bold rounded-xl text-sm px-5 py-4 text-center transition-all shadow-none cursor-not-allowed" disabled>
                    Bayar Sekarang
                </button>
            </div>
        </div>

    </div>
</div>

<!-- SCRIPTS -->
<script>
    const subtotal = {{ $subtotal }};
    const platformFee = {{ $platformFee }};
    let shippingCosts = {}; // Store ID -> Cost
    
    // --- 1. CHECK ONGKIR (AJAX) ---
    function checkOngkir(sellerId, courier) {
        const container = document.getElementById(`service-container-${sellerId}`);
        const listDiv = document.getElementById(`service-list-${sellerId}`);
        const loadingDiv = document.getElementById(`loading-${sellerId}`);
        
        // Reset UI
        container.classList.remove('hidden');
        listDiv.innerHTML = ''; 
        loadingDiv.classList.remove('hidden');
        
        // Hapus cost lama
        delete shippingCosts[sellerId];
        updateTotal();

        fetch('{{ route("checkout.check_shipping") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ seller_id: sellerId, courier: courier })
        })
        .then(res => res.json())
        .then(data => {
            loadingDiv.classList.add('hidden');
            
            if(data.status === 'success') {
                if(data.costs.length === 0) {
                     listDiv.innerHTML = '<p class="text-sm text-center text-gray-500 py-2">Tidak ada Layanan di Lokasi ini.</p>';
                     return;
                }

                data.costs.forEach((cost, index) => {
                    let serviceName = cost.service;
                    let serviceCost = 0;
                    let serviceEtd = '';
                    let serviceDesc = cost.description || serviceName;

                    if (cost.cost && Array.isArray(cost.cost)) {
                         const detail = cost.cost[0];
                         serviceCost = detail.value;
                         serviceEtd = detail.etd;
                    } else if (typeof cost.cost === 'number') {
                         serviceCost = cost.cost;
                         serviceEtd = cost.etd;
                    } else {
                        return;
                    }

                    // Create Radio Selection Card
                    const wrapper = document.createElement('div');
                    const costFormatted = new Intl.NumberFormat('id-ID').format(serviceCost);
                    
                    wrapper.innerHTML = `
                        <label class="cursor-pointer relative block">
                            <input type="radio" name="service-${sellerId}" value="${serviceCost}" class="peer sr-only" onchange="selectService('${sellerId}', ${serviceCost})">
                            <div class="flex items-center justify-between p-4 border-2 border-gray-100 rounded-xl hover:border-red-100 bg-white transition-all peer-checked:border-[#EC1C25] peer-checked:bg-red-50">
                                <div class="flex items-center gap-3">
                                    <div class="text-[#EC1C25]">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800 text-sm">${serviceName}</p>
                                        <p class="text-xs text-gray-500">Estimasi ${serviceEtd} hari</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-[#EC1C25]">Rp${costFormatted}</p>
                                </div>
                                
                                <!-- Check Icon -->
                                <div class="absolute top-1/2 -translate-y-1/2 left-3 opacity-0 peer-checked:opacity-0 hidden">
                                   <!-- Optional check indicator -->
                                </div>
                            </div>
                        </label>
                    `;
                    listDiv.appendChild(wrapper);
                });
            } else {
                listDiv.innerHTML = `<p class="text-sm text-center text-red-500 py-2">${data.message || 'Tidak ada Layanan di Lokasi ini'}</p>`;
            }
        })
        .catch(err => {
            console.error(err);
            loadingDiv.classList.add('hidden');
            listDiv.innerHTML = '<p class="text-sm text-center text-red-500">Gagal memuat ongkir.</p>';
        });
    }

    // --- 2. SELECT SERVICE ---
    function selectService(sellerId, cost) {
        if(!isNaN(cost)) {
            shippingCosts[sellerId] = cost;
        } else {
            delete shippingCosts[sellerId];
        }
        updateTotal();
    }

    // --- 3. RECALCULATE GLOBAL TOTAL ---
    function updateTotal() {
        let totalShipping = 0;
        let countFilled = 0;
        const totalStores = {{ $groupedItems->count() }};
        
        for (let key in shippingCosts) {
            totalShipping += shippingCosts[key];
            countFilled++;
        }
        
        const grandTotal = subtotal + platformFee + totalShipping;
        
        // Update Summary UI
        document.getElementById('summary-shipping').textContent = 'Rp' + new Intl.NumberFormat('id-ID').format(totalShipping);
        document.getElementById('grand-total').textContent = 'Rp' + new Intl.NumberFormat('id-ID').format(grandTotal);
        
        // Enable/Disable Pay Button
        const btn = document.getElementById('pay-btn');
        if(countFilled === totalStores) {
            btn.disabled = false;
            btn.classList.remove('bg-gray-300', 'cursor-not-allowed');
            btn.classList.add('bg-[#EC1C25]', 'hover:bg-[#c4161e]', 'shadow-lg');
            btn.textContent = 'Bayar Sekarang';
        } else {
             btn.disabled = true;
             btn.classList.add('bg-gray-300', 'cursor-not-allowed');
             btn.classList.remove('bg-[#EC1C25]', 'hover:bg-[#c4161e]', 'shadow-lg');
             btn.textContent = `Lengkapi Pengiriman (${countFilled}/${totalStores})`;
        }
    }

    // --- 4. PROCESS PAYMENT (REDIRECT TO CUSTOM PAGE) ---
    function processPayment() {
        const btn = document.getElementById('pay-btn');
        btn.disabled = true;
        btn.textContent = 'Memproses...';
        
        fetch('{{ route("checkout.process") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                shipping_costs: shippingCosts
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                // ✅ NEW: Direct redirect to custom payment page
                window.location.href = data.redirect_url;
            } else {
                Swal.fire('Gagal', data.message, 'error');
                btn.disabled = false;
                btn.textContent = 'Bayar Sekarang';
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
            btn.disabled = false;
            btn.textContent = 'Bayar Sekarang';
        });
    }
</script>
@endsection
