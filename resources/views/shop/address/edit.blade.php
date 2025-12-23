@extends('layouts.seller')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Edit Alamat</h1>
            <p class="text-sm text-gray-500 mt-2">Perbarui detail alamat toko atau lokasi pengiriman Anda.</p>
        </div>
        <a href="{{ route('shop.address.index') }}" class="group flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-telu-red transition-colors">
            <span class="p-1 rounded-full group-hover:bg-red-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </span>
            Kembali
        </a>
    </div>

    <!-- Card Form -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <form action="{{ route('shop.address.update', $address->address_id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="p-8 space-y-8">
                
                <!-- Section 1: Informasi Dasar -->
                <div>
                    <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3 mb-6 flex items-center gap-2">
                        <span class="w-1 h-6 bg-telu-red rounded-full"></span>
                        Informasi Dasar
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Label -->
                        <div>
                            <label class="block mb-2 text-sm font-semibold text-gray-900">
                                Label Alamat <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="label" value="{{ old('label', $address->label) }}" 
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-3 transition-shadow placeholder-gray-400" 
                                placeholder="Contoh: Kantor Utama" required>
                        </div>

                        <!-- Penerima -->
                        <div>
                            <label class="block mb-2 text-sm font-semibold text-gray-900">
                                Nama Penerima / Toko <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="recipient" value="{{ old('recipient', $address->recipient) }}" 
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-3 transition-shadow placeholder-gray-400" 
                                required>
                        </div>

                        <!-- Telepon -->
                         <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-semibold text-gray-900">
                                Nomor Telepon <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </div>
                                <input type="number" name="phone" value="{{ old('phone', $address->phone) }}" 
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full pl-10 p-3 transition-shadow placeholder-gray-400" 
                                    required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Lokasi & Wilayah -->
                <div>
                    <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3 mb-6 flex items-center gap-2">
                        <span class="w-1 h-6 bg-telu-red rounded-full"></span>
                        Lokasi & Wilayah
                    </h3>
                    
                    <div class="space-y-6">
                        <!-- Smart Search -->
                        <div class="bg-red-50/50 p-6 rounded-xl border border-red-100 relative">
                            <label class="block mb-2 text-sm font-semibold text-gray-900 flex items-center gap-2">
                                <svg class="w-4 h-4 text-telu-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                Cari Kecamatan / Kota <span class="text-red-500">*</span>
                            </label>
                            
                            <div class="relative">
                                <!-- Pre-fill with readable location string -->
                                <input type="text" id="location-search" value="{{ $address->village }}, {{ $address->district }}, {{ $address->city }}, {{ $address->province }}"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-3 pr-10 shadow-sm transition-shadow placeholder-gray-400" 
                                    placeholder="Ketik minimal 3 huruf (contoh: Coblong, Bandung)..." autocomplete="off">
                                
                                <div id="loading-spinner" class="absolute right-3 top-3.5 hidden">
                                    <svg class="animate-spin h-5 w-5 text-telu-red" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </div>

                                <ul id="location-results" class="absolute z-20 w-full bg-white border border-gray-200 rounded-xl shadow-xl mt-1 max-h-60 overflow-y-auto hidden"></ul>
                            </div>
                            <p class="text-xs text-gray-500 mt-2 ml-1">Pilih lokasi dari daftar untuk mengisi provinsi, kota, dan kecamatan secara otomatis.</p>

                            <!-- Hidden Inputs -->
                            <input type="hidden" name="location_id" id="input-location-id" value="{{ old('location_id', $address->location_id) }}" required>
                            <input type="hidden" name="province" id="input-province" value="{{ old('province', $address->province) }}" required>
                            <input type="hidden" name="city" id="input-city" value="{{ old('city', $address->city) }}" required>
                            <input type="hidden" name="district" id="input-district" value="{{ old('district', $address->district) }}" required>
                            <input type="hidden" name="village" id="input-village" value="{{ old('village', $address->village) }}" required>
                        </div>

                        <!-- Manual Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block mb-2 text-sm font-semibold text-gray-900">Kode Pos <span class="text-red-500">*</span></label>
                                <input type="number" name="postal_code" id="addr_postal" value="{{ old('postal_code', $address->postal_code) }}" 
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-3 font-mono transition-shadow placeholder-gray-400" 
                                    required>
                            </div>
                            <!-- Detail Alamat -->
                            <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-semibold text-gray-900">Detail Alamat Lengkap <span class="text-red-500">*</span></label>
                                <textarea name="detail_address" rows="3" 
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-3 transition-shadow placeholder-gray-400" 
                                    placeholder="Nama Jalan, Gedung, No. Rumah, RT/RW, Patokan..." required>{{ old('detail_address', $address->detail_address) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Toggle Jadikan Alamat Utama -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <div>
                        <span class="block text-sm font-bold text-gray-900">Jadikan Alamat Utama Toko</span>
                        <span class="block text-xs text-gray-500 mt-1">Alamat ini akan digunakan sebagai alamat pengiriman toko Anda.</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_shop_default" value="1" {{ $address->is_shop_default ? 'checked disabled' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#EC1C25]"></div>
                    </label>
                </div>
                
                @if($address->is_shop_default)
                    <p class="text-xs text-orange-500 flex items-center gap-1 mt-[-20px]">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Alamat ini sudah menjadi alamat utama.
                    </p>
                @endif

            </div>

            <!-- Footer Actions -->
            <div class="px-8 py-5 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3">
                 <a href="{{ route('shop.address.index') }}" class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-gray-200 transition-all">
                    Batal
                </a>
                <button type="submit" class="text-white bg-[#EC1C25] hover:bg-[#c4161e] focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-8 py-2.5 text-center shadow-lg shadow-red-500/30 transition-all transform hover:-translate-y-0.5">
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('location-search');
        const resultsList = document.getElementById('location-results');
        const spinner = document.getElementById('loading-spinner');
        
        const inputLocId = document.getElementById('input-location-id');
        const inputProvince = document.getElementById('input-province');
        const inputCity = document.getElementById('input-city');
        const inputDistrict = document.getElementById('input-district');
        const inputVillage = document.getElementById('input-village');
        const inputZip = document.getElementById('addr_postal');

        let debounceTimer;

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value;

            if (query.length < 3) {
                resultsList.classList.add('hidden');
                return;
            }

            spinner.classList.remove('hidden');

            debounceTimer = setTimeout(() => {
                fetch(`/location/search?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        resultsList.innerHTML = '';
                        spinner.classList.add('hidden');

                        if (data.length === 0) {
                            resultsList.innerHTML = '<li class="px-4 py-3 text-sm text-gray-500 text-center">Lokasi tidak ditemukan</li>';
                            resultsList.classList.remove('hidden');
                            return;
                        }

                        data.forEach(item => {
                            const li = document.createElement('li');
                            li.className = 'px-4 py-3 text-sm text-gray-700 hover:bg-red-50 cursor-pointer border-b border-gray-100 last:border-0 transition-colors flex flex-col';
                            li.innerHTML = `<span class="font-bold text-gray-900">${item.label.split(',')[0]}</span><span class="text-xs text-gray-500">${item.label}</span>`;
                            
                            li.addEventListener('click', () => {
                                searchInput.value = item.label;
                                inputLocId.value = item.id;
                                inputProvince.value = item.province_name;
                                inputCity.value = item.city_name;
                                inputDistrict.value = item.district_name;
                                inputVillage.value = item.subdistrict_name;
                                inputZip.value = item.zip_code;

                                resultsList.classList.add('hidden');
                            });

                            resultsList.appendChild(li);
                        });

                        resultsList.classList.remove('hidden');
                    })
                    .catch(err => {
                        console.error(err);
                        spinner.classList.add('hidden');
                    });
            }, 500);
        });

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !resultsList.contains(e.target)) {
                resultsList.classList.add('hidden');
            }
        });
    });
</script>
@endsection
