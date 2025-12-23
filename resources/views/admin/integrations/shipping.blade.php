@extends('layouts.admin')

@section('content')

<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Logistik & Pengiriman</h1>
    <p class="text-sm text-gray-500 mt-1">Integrasi RajaOngkir untuk kalkulasi ongkos kirim otomatis.</p>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

    <!-- LEFT: CONFIGURATION -->
    <div class="xl:col-span-2 space-y-8">
        <!-- Main Config Card -->
        <div class="bg-white rounded-2xl shadow-soft p-8 border border-gray-100">
            <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-3">
                <div class="p-2 bg-red-50 rounded-lg text-red-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                </div>
                Konfigurasi RajaOngkir (Pro/Komerce)
            </h3>
            
            <form action="{{ route('admin.integrations.shipping.update') }}" method="POST">
                @csrf 
                <input type="hidden" name="type" value="pro">
                
                <div class="mb-5">
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Base URL API</label>
                    <input type="url" name="base_url" value="{{ $rajaongkir->meta_json['base_url'] ?? 'https://rajaongkir.komerce.id/api/v1' }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 block w-full p-3 transition-all" placeholder="https://rajaongkir.komerce.id/api/v1" required>
                    <p class="mt-2 text-xs text-gray-500">Default: <span class="font-mono bg-gray-100 px-2 py-0.5 rounded text-gray-600">https://rajaongkir.komerce.id/api/v1</span></p>
                </div>

                <div class="mb-8">
                    <label class="block mb-2 text-sm font-semibold text-gray-700">API Key</label>
                    <input type="text" name="api_key" value="{{ $rajaongkir->public_k ?? '' }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 block w-full p-3 font-mono transition-all" placeholder="Masukkan API Key Komerce/RajaOngkir" required>
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-100 font-bold rounded-xl text-sm px-6 py-3 text-center flex items-center gap-2 shadow-lg shadow-red-500/30 transition-all transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Konfigurasi
                    </button>
                </div>
            </form>
        </div>

        <!-- Carriers Table -->
        <div class="bg-white rounded-2xl shadow-soft p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-gray-800">Daftar Kurir</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Kelola layanan kurir yang tersedia untuk pembeli.</p>
                </div>
            </div>

            <form action="{{ route('admin.integrations.carrier.store') }}" method="POST" class="flex gap-2 mb-4">
                @csrf
                <input type="text" name="code" placeholder="Kode (ex: jne)" class="bg-white border border-gray-300 text-xs rounded-lg block w-32 px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all" required>
                <input type="text" name="name" placeholder="Nama Tampil (ex: JNE Express)" class="bg-white border border-gray-300 text-xs rounded-lg block flex-1 px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all" required>
                <button type="submit" class="text-white bg-red-600 hover:bg-red-700 font-medium rounded-lg text-xs px-4 py-2 shadow transition-transform hover:-translate-y-0.5 whitespace-nowrap">
                    + Tambah
                </button>
            </form>

            <div class="rounded-lg border border-gray-100 overflow-hidden">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50/50 border-b border-gray-100">
                        <tr>
                            <th scope="col" class="px-4 py-2.5 text-left text-[10px] font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-4 py-2.5 text-left text-[10px] font-bold text-gray-500 uppercase tracking-wider">Kode</th>
                            <th scope="col" class="px-4 py-2.5 text-left text-[10px] font-bold text-gray-500 uppercase tracking-wider">Nama Kurir</th>
                            <th scope="col" class="px-4 py-2.5 text-center text-[10px] font-bold text-gray-500 uppercase tracking-wider w-20">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 bg-white">
                        @forelse($shippingCarriers as $carrier)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-4 py-2.5">
                                <form action="{{ route('admin.integrations.carrier.toggle', $carrier->shipping_carrier_id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" class="sr-only peer" onchange="this.form.submit()" {{ $carrier->is_enabled ? 'checked' : '' }}>
                                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-red-600"></div>
                                    </label>
                                </form>
                            </td>
                            <td class="px-4 py-2.5">
                                <span class="font-mono text-[11px] text-gray-600 bg-gray-50 px-2 py-0.5 rounded border border-gray-100">{{ $carrier->code }}</span>
                            </td>
                            <td class="px-4 py-2.5">
                                <span class="font-medium text-gray-900 text-xs">{{ $carrier->name }}</span>
                            </td>
                            <td class="px-4 py-2.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Edit Button -->
                                    <button onclick="openEditModal('{{ route('admin.integrations.carrier.update', $carrier->shipping_carrier_id) }}', '{{ $carrier->code }}', '{{ $carrier->name }}')" 
                                        class="text-gray-400 hover:text-red-600 transition-colors p-1" 
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    
                                    <!-- Delete Button -->
                                    <form id="delete-carrier-{{ $carrier->shipping_carrier_id }}" action="{{ route('admin.integrations.carrier.delete', $carrier->shipping_carrier_id) }}" method="POST" class="inline-block">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmDelete('delete-carrier-{{ $carrier->shipping_carrier_id }}')" 
                                            class="text-gray-400 hover:text-red-600 transition-colors p-1" 
                                            title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                    </div>
                                    <h3 class="text-gray-900 font-bold text-sm mb-1">Belum ada Kurir</h3>
                                    <p class="text-gray-500 text-xs">Tambahkan kurir pertama menggunakan form di atas.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($shippingCarriers->hasPages())
            <div class="flex items-center justify-between mt-4 text-xs">
                <div class="text-gray-500">
                    Showing {{ $shippingCarriers->firstItem() ?? 0 }} to {{ $shippingCarriers->lastItem() ?? 0 }} of {{ $shippingCarriers->total() }} results
                </div>
                <div>
                    {{ $shippingCarriers->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- RIGHT: SIMULATOR -->
    <div class="xl:col-span-1">
        <div class="bg-white border border-gray-100 rounded-2xl shadow-soft p-6 sticky top-28" x-data="{ 
            courierCode: 'jne', 
            courierName: 'JNE Express',
            dropdownOpen: false,
            couriers: {{ $shippingCarriers->map(fn($c) => ['code' => $c->code, 'name' => $c->name])->toJson() }}
        }">
            <h3 class="text-lg font-bold text-gray-900 mb-2 flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center text-red-600">
                   <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                Test Cek Ongkir
            </h3>
            <p class="text-sm text-gray-500 mb-6">Test koneksi API dengan data real.</p>
            
            <form action="{{ route('admin.integrations.shipping.test-cost') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                     <label class="text-xs font-bold text-gray-500 uppercase block mb-2">Origin ID</label>
                     <input type="number" name="origin" placeholder="501" class="w-full rounded-xl bg-white border border-gray-300 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 p-3 transition-all" value="501" required>
                     <p class="text-[10px] text-gray-400 mt-1">Cth: 501 (Yogyakarta), 151 (Jakarta)</p>
                </div>
                <div>
                     <label class="text-xs font-bold text-gray-500 uppercase block mb-2">Destination ID</label>
                     <input type="number" name="destination" placeholder="114" class="w-full rounded-xl bg-white border border-gray-300 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 p-3 transition-all" value="114" required>
                     <p class="text-[10px] text-gray-400 mt-1">Cth: 114 (Denpasar), 23 (Bandung)</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                         <label class="text-xs font-bold text-gray-500 uppercase block mb-2">Berat (g)</label>
                         <input type="number" name="weight" placeholder="1000" min="1000" class="w-full rounded-xl bg-white border border-gray-300 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 p-3 transition-all" value="1000" required>
                         <p class="text-[10px] text-gray-400 mt-1">Min: 1000g</p>
                    </div>
                    <div>
                         <label class="text-xs font-bold text-gray-500 uppercase block mb-2">Kurir</label>
                         
                         <div class="relative">
                             <input type="hidden" name="courier" x-model="courierCode">
                             <button type="button" @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false"
                                     class="w-full rounded-xl bg-white border border-gray-300 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 p-3 transition-all text-left flex justify-between items-center">
                                 <span x-text="courierName || 'Pilih Kurir'"></span>
                                 <svg class="w-4 h-4 text-gray-500 transition-transform duration-200" :class="{'rotate-180': dropdownOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                             </button>

                             <div x-show="dropdownOpen" x-transition class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden max-h-48 overflow-y-auto" style="display: none;">
                                <template x-for="c in couriers" :key="c.code">
                                    <div @click="courierCode = c.code; courierName = c.name; dropdownOpen = false" class="px-4 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 cursor-pointer flex items-center justify-between">
                                        <span x-text="c.name"></span>
                                        <span x-show="courierCode === c.code" class="text-red-500 font-bold">&check;</span>
                                    </div>
                                </template>
                                <div x-show="couriers.length === 0" class="px-4 py-3 text-xs text-gray-400 text-center">
                                    Belum ada kurir aktif.
                                </div>
                             </div>
                         </div>
                    </div>
                </div>

                <div class="p-3 bg-blue-50 border border-blue-100 rounded-xl text-xs text-blue-800">
                    <span class="font-bold">Info:</span> Pastikan ID Kota/Kecamatan valid (sesuai database RajaOngkir).
                </div>

                <button type="submit" class="w-full text-white bg-red-600 hover:bg-red-700 font-bold rounded-xl text-sm px-5 py-4 shadow-lg shadow-red-500/20 transition-transform hover:-translate-y-0.5 mt-4">
                    CEK HARGA ONGKIR
                </button>
            </form>
        </div>
    </div>

</div>

<div id="editModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeEditModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <form id="editForm" method="POST">
                @csrf @method('PUT')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Edit Kurir</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Kode Kurir</label>
                                    <input type="text" name="code" id="editCode" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-red-500 focus:border-red-500 block p-2.5" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Tampil</label>
                                    <input type="text" name="name" id="editName" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-red-500 focus:border-red-500 block p-2.5" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Simpan Perubahan
                    </button>
                    <button type="button" onclick="closeEditModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    // Edit Modal Functions
    function openEditModal(action, code, name) {
        document.getElementById('editForm').action = action;
        document.getElementById('editCode').value = code;
        document.getElementById('editName').value = name;
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    // Delete Confirmation
    function confirmDelete(formId) {
        Swal.fire({
            title: 'Hapus Kurir?',
            text: "Kurir akan dihapus dari daftar pilihan pembeli.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#E5E7EB',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: '<span class="text-gray-800 font-bold">Batal</span>',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl px-5 py-2.5 font-bold shadow-lg',
                cancelButton: 'rounded-xl px-5 py-2.5'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        })
    }

    // Shipping Cost Popup
    @if(session('cost_results'))
        let resultsHtml = `
        <div class="text-xs text-gray-600 mb-3 text-center border-b pb-2">
            <b>{{ session('origin_name') }}</b> &rarr; <b>{{ session('dest_name') }}</b><br>
            Kurir: <span class="uppercase font-bold">{{ session('courier_name') }}</span> | {{ old('weight') }}g
        </div>
        <div class="space-y-2 max-h-60 overflow-y-auto pr-1 custom-scrollbar text-left">
            @foreach(session('cost_results') as $cost)
                <div class="bg-gray-50 p-3 rounded-xl border border-gray-200 flex justify-between items-center group hover:border-blue-200 hover:bg-blue-50 transition-colors">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-gray-800 text-sm">{{ $cost['service'] }}</span>
                            <span class="text-[10px] text-gray-500 bg-white border border-gray-200 px-1.5 rounded-full">{{ $cost['description'] ?? '' }}</span>
                        </div>
                        <div class="text-xs text-gray-500 mt-0.5">Est: {{ $cost['etd'] ?? '-' }} hari</div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-gray-900">Rp {{ number_format($cost['cost'], 0, ',', '.') }}</div>
                    </div>
                </div>
            @endforeach
        </div>
        `;

        Swal.fire({
            title: 'Hasil Cek Ongkir',
            html: resultsHtml,
            icon: 'success',
            confirmButtonText: 'Tutup',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'bg-gray-900 text-white rounded-xl px-4 py-2'
            }
        });
    @endif
</script>
@endsection
