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
                        <p class="text-sm text-gray-500 mt-1">{{ $mainAddress->city_name }}, {{ $mainAddress->province_name }} {{ $mainAddress->postal_code }}</p>
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
                        <span class="text-xs px-2 py-0.5 bg-gray-100 text-gray-600 rounded">{{ $seller->city_name ?? 'Kota Toko' }}</span>
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
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-sm font-bold text-gray-800 mb-3">Pilih Pengiriman</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            
                            <!-- 1. Pilih Kurir -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Kurir</label>
                                <select class="courier-select w-full border-gray-300 rounded-lg text-sm focus:ring-[#EC1C25] focus:border-[#EC1C25]" onchange="checkOngkir('{{ $sellerId }}', this.value)">
                                    <option value="" disabled selected>Pilih Kurir</option>
                                    @foreach($couriers as $courier)
                                        <option value="{{ $courier }}">{{ strtoupper($courier) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- 2. Pilih Layanan (Dynamic by AJAX) -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Layanan</label>
                                <select class="service-select w-full border-gray-300 rounded-lg text-sm focus:ring-[#EC1C25] focus:border-[#EC1C25] disabled:bg-gray-100 disabled:text-gray-400" 
                                    id="service-{{ $sellerId }}" disabled onchange="selectService('{{ $sellerId }}')">
                                    <option value="">Pilih kurir dulu</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Info Estimasi & Harga -->
                        <div id="shipping-info-{{ $sellerId }}" class="mt-3 hidden justify-between items-center text-sm">
                            <span class="text-gray-600" id="estimasi-{{ $sellerId }}">Estimasi: -</span>
                            <span class="font-bold text-[#EC1C25]" id="cost-{{ $sellerId }}">Rp0</span>
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
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    const subtotal = {{ $subtotal }};
    const platformFee = {{ $platformFee }};
    let shippingCosts = {}; // Store ID -> Cost
    
    // --- 1. CHECK ONGKIR (AJAX) ---
    function checkOngkir(sellerId, courier) {
        const serviceSelect = document.getElementById(`service-${sellerId}`);
        const infoDiv = document.getElementById(`shipping-info-${sellerId}`);
        
        // Reset UI
        serviceSelect.innerHTML = '<option>Loading...</option>';
        serviceSelect.disabled = true;
        infoDiv.classList.add('hidden');
        
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
            serviceSelect.innerHTML = '<option value="">Pilih Layanan</option>';
            
            if(data.status === 'success') {
                data.costs.forEach(cost => {
                    // Normalize Data Structure
                    // Helper to handle Komerce (Direct Value) vs RajaOngkir (Array of values)
                    let serviceName = cost.service;
                    let serviceCost = 0;
                    let serviceEtd = '';
                    let serviceDesc = cost.description || serviceName;

                    if (cost.cost && Array.isArray(cost.cost)) {
                         // Standard RajaOngkir Structure: { service: "REG", cost: [{value:10000, etd:"1-2"}] }
                         const detail = cost.cost[0];
                         serviceCost = detail.value;
                         serviceEtd = detail.etd;
                    } else if (typeof cost.cost === 'number') {
                         // Komerce Flattened Structure (User Snippet): { service: "REG", cost: 10000, etd: "1-2" }
                         serviceCost = cost.cost;
                         serviceEtd = cost.etd;
                    } else {
                        return; // Skip invalid format
                    }

                    const option = document.createElement('option');
                    option.value = serviceCost;
                    option.dataset.etd = serviceEtd;
                    option.textContent = `${serviceName} - Rp${new Intl.NumberFormat('id-ID').format(serviceCost)} (${serviceEtd} hari)`;
                    serviceSelect.appendChild(option);
                });
                serviceSelect.disabled = false;
            } else {
                serviceSelect.innerHTML = '<option value="">Tidak tersedia</option>';
                Swal.fire('Info', data.message || 'Ongkir tidak ditemukan', 'warning');
            }
        })
        .catch(err => {
            console.error(err);
            serviceSelect.innerHTML = '<option value="">Error</option>';
        });
    }

    // --- 2. SELECT SERVICE ---
    function selectService(sellerId) {
        const select = document.getElementById(`service-${sellerId}`);
        const infoDiv = document.getElementById(`shipping-info-${sellerId}`);
        const etdSpan = document.getElementById(`estimasi-${sellerId}`);
        const costSpan = document.getElementById(`cost-${sellerId}`);
        
        const cost = parseInt(select.value);
        if(!isNaN(cost)) {
            const etd = select.options[select.selectedIndex].dataset.etd;
            
            // Update UI Toko
            infoDiv.classList.remove('hidden');
            infoDiv.classList.add('flex');
            etdSpan.textContent = `Estimasi: ${etd} hari`;
            costSpan.textContent = `Rp${new Intl.NumberFormat('id-ID').format(cost)}`;
            
            // Update Global Logic
            shippingCosts[sellerId] = cost;
        } else {
            infoDiv.classList.add('hidden');
            delete shippingCosts[sellerId];
        }
        
        updateTotal();
    }

    // --- 3. RECALCULATE GLOBAL TOTAL ---
    function updateTotal() {
        let totalShipping = 0;
        let countFilled = 0;
        const totalStores = {{ $groupedItems->count() }}; // PHP Value
        
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

    // --- 4. PROCESS PAYMENT ---
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
                // Show Snap Popup
                snap.pay(data.snap_token, {
                    onSuccess: function(result){
                        window.location.href = data.redirect_url;
                    },
                    onPending: function(result){
                        window.location.href = data.redirect_url;
                    },
                    onError: function(result){
                        Swal.fire('Error', 'Pembayaran gagal.', 'error');
                        btn.disabled = false;
                        btn.textContent = 'Bayar Sekarang';
                    },
                    onClose: function(){
                        btn.disabled = false;
                        btn.textContent = 'Bayar Sekarang';
                    }
                });
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
