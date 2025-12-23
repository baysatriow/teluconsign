@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- MAIN CONTENT -->
        <div class="flex-grow space-y-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Checkout Pengiriman</h1>

            <!-- 1. ALAMAT PENGIRIMAN -->
            <div class="bg-white border border-gray-100 rounded-2xl p-8 shadow-sm relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-24 h-24 text-[#EC1C25]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                </div>

                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                    <span class="bg-red-50 text-[#EC1C25] p-2 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </span>
                    Alamat Pengiriman
                </h3>
                
                @if($mainAddress)
                    <div class="relative z-10 pl-2 border-l-4 border-[#EC1C25]">
                        <div class="flex flex-col sm:flex-row sm:items-baseline gap-2 mb-2">
                             <h4 class="text-lg font-bold text-gray-900">{{ Auth::user()->name }}</h4>
                             <span class="text-gray-500 font-medium">({{ $mainAddress->phone }})</span>
                        </div>
                        <p class="text-gray-600 leading-relaxed max-w-2xl">{{ $mainAddress->getFullAddress() }}</p>
                        <p class="text-gray-600 mb-4">{{ $mainAddress->city }}, {{ $mainAddress->province }} {{ $mainAddress->postal_code }}</p>
                        
                        <a href="{{ route('profile.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-[#EC1C25] hover:text-[#c4161e] hover:underline transition-colors">
                            Ganti Alamat
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                    </div>
                @else
                    <div class="text-center py-8 bg-yellow-50 rounded-xl border border-yellow-100">
                        <svg class="w-12 h-12 text-yellow-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <p class="text-yellow-800 font-bold mb-1">Alamat Belum Diatur</p>
                        <p class="text-yellow-600 mb-6 text-sm">Kamu perlu menambahkan alamat pengiriman utama.</p>
                        <a href="{{ route('profile.index') }}" class="inline-flex items-center px-6 py-2.5 bg-[#EC1C25] text-white rounded-xl text-sm font-bold shadow-md hover:bg-[#c4161e] transition-all transform hover:-translate-y-0.5">
                            Tambah Alamat Sekarang
                        </a>
                    </div>
                @endif
            </div>

            <!-- 2. ITEM CHECKOUT -->
            @foreach($groupedItems as $sellerId => $items)
                @php $seller = $items->first()->product->seller; @endphp
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm overflow-hidden store-section transition-all hover:shadow-md" data-seller="{{ $sellerId }}">
                    
                    <!-- Header Toko -->
                    <div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-50">
                        <div class="w-10 h-10 rounded-full bg-gray-100 overflow-hidden border border-gray-200">
                            <img src="{{ $seller->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($seller->name).'&background=random' }}" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 flex items-center gap-2">
                                {{ $seller->name }}
                                <span class="bg-gray-100 text-gray-600 text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wider">{{ $seller->addresses->first()->city ?? 'Kota Toko' }}</span>
                            </h4>
                        </div>
                    </div>

                    <!-- List Item -->
                    <div class="space-y-4 mb-8">
                        @foreach($items as $item)
                            <div class="flex gap-4 p-4 bg-gray-50/50 rounded-xl border border-gray-50 hover:bg-white hover:border-gray-200 transition-all">
                                <div class="w-20 h-20 rounded-lg bg-gray-100 border border-gray-200 overflow-hidden flex-shrink-0">
                                    <img src="{{ $item->product->main_image ? asset('storage/'.$item->product->main_image) : 'https://placehold.co/100' }}" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-grow flex flex-col justify-between">
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900 line-clamp-1 mb-1">{{ $item->product->title }}</h4>
                                        <p class="text-xs text-gray-500 font-medium">{{ $item->quantity }} barang x Rp{{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                    </div>
                                    <p class="text-sm font-extrabold text-[#EC1C25]">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- PENGIRIMAN -->
                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-100">
                        <!-- 1. Pilih Kurir -->
                        <h4 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Pilih Kurir Pengiriman
                        </h4>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                            @foreach($couriers as $courier)
                                <label class="cursor-pointer relative group">
                                    <input type="radio" name="courier-{{ $sellerId }}" value="{{ $courier }}" class="peer sr-only" onchange="checkOngkir('{{ $sellerId }}', this.value)">
                                    <div class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-gray-200 rounded-xl hover:border-red-200 hover:bg-white bg-transparent transition-all peer-checked:border-[#EC1C25] peer-checked:bg-white peer-checked:shadow-sm peer-checked:border-solid">
                                        <!-- Placeholder Logo / Text -->
                                        <span class="font-extrabold text-sm text-gray-400 group-hover:text-gray-600 peer-checked:text-[#EC1C25] transition-colors">{{ strtoupper($courier) }}</span>
                                        
                                        <!-- Check Icon -->
                                        <div class="absolute -top-2 -right-2 bg-[#EC1C25] text-white rounded-full p-0.5 opacity-0 peer-checked:opacity-100 transition-all transform scale-0 peer-checked:scale-100">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <!-- 2. Pilih Layanan (Dynamic by AJAX) -->
                        <div id="service-container-{{ $sellerId }}" class="hidden animate-fade-in-up">
                            <h4 class="text-sm font-bold text-gray-900 mb-3 ml-1">Pilih Layanan</h4>
                            
                            <!-- Loading Indicator -->
                            <div id="loading-{{ $sellerId }}" class="hidden text-center py-6">
                                <svg class="animate-spin h-8 w-8 text-[#EC1C25] mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <p class="text-xs font-bold text-gray-400">Sedang mengecek ongkir...</p>
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
            <div class="bg-white border border-gray-100 rounded-2xl shadow-xl shadow-gray-200/50 p-6 sticky top-24">
                <h3 class="text-lg font-extrabold text-gray-900 mb-6">Ringkasan Pembayaran</h3>

                <div class="space-y-4 mb-6">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 font-medium">Total Harga ({{ $groupedItems->flatten()->sum('quantity') }} barang)</span>
                        <span class="font-bold text-gray-900">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 font-medium">Total Ongkos Kirim</span>
                        <span class="font-bold text-gray-900" id="summary-shipping">Rp0</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 font-medium">Biaya Layanan</span>
                        <span class="font-bold text-gray-900">Rp{{ number_format($platformFee, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="flex justify-between items-center mb-6 pt-6 border-t border-gray-100">
                    <span class="text-base font-bold text-gray-900">Total Tagihan</span>
                    <span class="text-2xl font-extrabold text-[#EC1C25]" id="grand-total">Rp{{ number_format($subtotal + $platformFee, 0, ',', '.') }}</span>
                </div>

                <button type="button" onclick="processPayment()" id="pay-btn" class="w-full text-white bg-gray-200 font-bold rounded-xl text-base px-5 py-4 text-center transition-all shadow-none cursor-not-allowed transform disabled:opacity-100 disabled:hover:translate-y-0 text-gray-400" disabled>
                    Lengkapi Pengiriman
                </button>
                
                <p class="text-[10px] text-center text-gray-400 mt-4 leading-relaxed">
                    Mohon lengkapi semua opsi pengiriman untuk melanjutkan pembayaran.
                </p>
            </div>
        </div>

    </div>
</div>

<!-- SCRIPTS -->
<script>
    window.addEventListener('load', function() {



    const subtotal = {{ $subtotal }};
    const platformFee = {{ $platformFee }};
    let shippingData = {}; // Store ID -> { cost, code, service, etd, description }
    
    // --- 1. CHECK ONGKIR (AJAX) ---
    window.checkOngkir = function(sellerId, courier) {
        const container = document.getElementById(`service-container-${sellerId}`);
        const listDiv = document.getElementById(`service-list-${sellerId}`);
        const loadingDiv = document.getElementById(`loading-${sellerId}`);
        
        // Reset UI
        container.classList.remove('hidden');
        listDiv.innerHTML = ''; 
        loadingDiv.classList.remove('hidden');
        
        // Hapus cost lama
        delete shippingData[sellerId];
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
                            <input type="radio" name="service-${sellerId}" value="${serviceCost}" class="peer sr-only" onchange="selectService('${sellerId}', ${serviceCost}, '${cost.code}', '${serviceName}', '${serviceEtd}', '${serviceDesc}')">
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
    window.selectService = function(sellerId, cost, courierCode, serviceCode, etd, description) {
        if(!isNaN(cost)) {
            shippingData[sellerId] = {
                cost: cost,
                courier: courierCode,
                service: serviceCode,
                etd: etd,
                description: description
            };
        } else {
            delete shippingData[sellerId];
        }
        updateTotal();
    }

    // --- 3. RECALCULATE GLOBAL TOTAL ---
    function updateTotal() {
        let totalShipping = 0;
        let countFilled = 0;
        const totalStores = {{ $groupedItems->count() }};
        
        for (let key in shippingData) {
            totalShipping += shippingData[key].cost;
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
            btn.classList.remove('bg-gray-200', 'cursor-not-allowed', 'text-gray-400');
            btn.classList.add('bg-[#EC1C25]', 'hover:bg-[#c4161e]', 'shadow-lg', 'text-white');
            btn.textContent = 'Bayar Sekarang';
        } else {
             btn.disabled = true;
             btn.classList.add('bg-gray-200', 'cursor-not-allowed', 'text-gray-400', 'font-bold');
             btn.classList.remove('bg-[#EC1C25]', 'hover:bg-[#c4161e]', 'shadow-lg', 'text-white');
             btn.textContent = `Lengkapi Pengiriman (${countFilled}/${totalStores})`;
        }
    }

    // --- 4. PROCESS PAYMENT (REDIRECT TO CUSTOM PAGE) ---
    window.processPayment = function() {
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
                shipping_data: shippingData
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                // ✅ NEW: Direct redirect to custom payment page
                window.location.href = data.redirect_url;
            } else {
                SwalCustom.fire('Gagal', data.message, 'error');
                btn.disabled = false;
                btn.textContent = 'Bayar Sekarang';
            }
        })
        .catch(err => {
            console.error(err);
            SwalCustom.fire('Error', 'Terjadi kesalahan sistem', 'error');
            btn.disabled = false;
            btn.textContent = 'Bayar Sekarang';
        });
    }
    });
</script>
@endsection
