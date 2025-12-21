@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Integrasi API & Sistem</h1>
    <p class="text-gray-600">Kelola koneksi Payment Gateway, Logistik, dan Notifikasi.</p>
</div>

<!-- Tabs -->
<div class="mb-4 border-b border-gray-200 bg-white rounded-t-lg px-4 pt-4">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="integrationTabs" data-tabs-toggle="#integrationTabContent" role="tablist">
        <li class="me-2" role="presentation">
            <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300 group" id="payment-tab" data-tabs-target="#payment" type="button" role="tab" aria-controls="payment" aria-selected="false">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Payment Gateway
                </span>
            </button>
        </li>
        <li class="me-2" role="presentation">
            <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300 group" id="logistics-tab" data-tabs-target="#logistics" type="button" role="tab" aria-controls="logistics" aria-selected="false">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    Logistik
                </span>
            </button>
        </li>
        <li class="me-2" role="presentation">
            <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300 group" id="whatsapp-tab" data-tabs-target="#whatsapp" type="button" role="tab" aria-controls="whatsapp" aria-selected="false">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    WhatsApp
                </span>
            </button>
        </li>
    </ul>
</div>

<div id="integrationTabContent">
    
    <!-- TAB 1: PAYMENT GATEWAY -->
    <div class="hidden p-4 rounded-lg bg-white shadow" id="payment" role="tabpanel" aria-labelledby="payment-tab">
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <!-- Config Form -->
            <div>
                <h3 class="mb-4 text-lg font-bold text-gray-900 flex items-center gap-2">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/e/e6/Midtrans.png" class="h-6" alt="Midtrans">
                    Konfigurasi Midtrans
                    @if($midtrans->is_active)
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Aktif</span>
                    @endif
                </h3>
                
                <form action="{{ route('admin.integrations.payment.update') }}" method="POST">
                    @csrf @method('PATCH')
                    
                    <div class="grid gap-4 mb-4 sm:grid-cols-2">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Environment Mode</label>
                            <select name="mode" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="sandbox" {{ ($midtrans->meta_json['environment'] ?? '') == 'sandbox' ? 'selected' : '' }}>Sandbox (Test)</option>
                                <option value="production" {{ ($midtrans->meta_json['environment'] ?? '') == 'production' ? 'selected' : '' }}>Production (Live)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Merchant ID</label>
                            <input type="text" name="merchant_id" value="{{ $midtrans->meta_json['merchant_id'] ?? '' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="G-12345">
                        </div>
                        <div class="col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Client Key</label>
                            <input type="text" name="client_key" value="{{ $midtrans->public_k ?? '' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="SB-Mid-client-..." required>
                        </div>
                        <div class="col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Server Key (Secret)</label>
                            <div class="relative">
                                <input type="password" name="server_key" value="{{ $midtrans->server_key ?? '' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="SB-Mid-server-..." required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="text-white bg-[#EC1C25] hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Simpan Konfigurasi</button>
                    <!-- Tombol Test Connection (Simple) -->
                    <a href="{{ route('admin.integrations.payment.test') }}" class="ml-2 text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5">Test Koneksi</a>
                </form>
            </div>

            <!-- Side Panel: Methods & Simulator -->
            <div class="space-y-6">
                <!-- Payment Methods Table -->
                <div class="p-4 border border-gray-200 rounded-lg">
                    <h4 class="text-md font-bold text-gray-900 mb-3">Metode Pembayaran Aktif</h4>
                    
                    {{-- Form Tambah Method --}}
                    <form action="{{ route('admin.integrations.payment-method.store') }}" method="POST" class="flex gap-2 mb-4">
                        @csrf
                        <input type="text" name="code" placeholder="Kode (misal: gopay)" class="flex-1 bg-gray-50 border border-gray-300 text-sm rounded-lg p-2" required>
                        <input type="text" name="name" placeholder="Nama (misal: GoPay)" class="flex-1 bg-gray-50 border border-gray-300 text-sm rounded-lg p-2" required>
                        <button type="submit" class="bg-blue-600 text-white text-sm rounded-lg px-3 py-2 hover:bg-blue-700">Tambah</button>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-2 py-2">Kode</th>
                                    <th class="px-2 py-2">Nama</th>
                                    <th class="px-2 py-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paymentMethods as $pm)
                                <tr>
                                    <td class="px-2 py-2 font-medium text-gray-900">{{ $pm->code }}</td>
                                    <td class="px-2 py-2">{{ $pm->name }}</td>
                                    <td class="px-2 py-2">
                                        <form action="{{ route('admin.integrations.payment-method.delete', $pm->payment_method_id) }}" method="POST" onsubmit="return confirm('Hapus metode ini?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="px-2 py-2 text-center text-xs text-gray-400">Belum ada metode. Tambahkan diatas.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Simulator Payment -->
                <div class="p-4 bg-blue-50 border border-blue-100 rounded-lg">
                    <h4 class="text-md font-bold text-blue-800 mb-2">Simulator Pembayaran</h4>
                    <p class="text-xs text-blue-600 mb-3">Klik tombol ini untuk mencoba membuat transaksi dummy dan memunculkan pop-up Snap.</p>
                    <form action="{{ route('admin.integrations.payment.test-simulate') }}" method="POST" target="_blank">
                         @csrf
                         <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Buka Simulator Snap</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: LOGISTIK -->
    <div class="hidden p-4 rounded-lg bg-white shadow" id="logistics" role="tabpanel" aria-labelledby="logistics-tab">
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <!-- Config Form -->
            <div>
                <h3 class="mb-4 text-lg font-bold text-gray-900">Konfigurasi RajaOngkir</h3>
                 <form action="{{ route('admin.integrations.shipping.update') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-900">Tipe Akun</label>
                        <select name="type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                            <option value="starter">Starter</option>
                            <option value="basic">Basic</option>
                            <option value="pro" selected>Pro (Komerce/Official)</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-900">Base URL</label>
                        <input type="url" name="base_url" value="{{ $rajaongkir->meta_json['base_url'] ?? 'https://pro.rajaongkir.com/api' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" required>
                    </div>
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-900">API Key</label>
                        <input type="text" name="api_key" value="{{ $rajaongkir->public_k ?? '' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" required>
                    </div>
                    <button type="submit" class="text-white bg-[#EC1C25] hover:bg-red-800 font-medium rounded-lg text-sm px-5 py-2.5">Simpan Konfigurasi</button>
                </form>

                <!-- Check Ongkir Simulator -->
                <div class="mt-8 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <h4 class="font-bold mb-3">Cek Ongkir Simulator</h4>
                    <form action="{{ route('admin.integrations.shipping.test-cost') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-2 gap-2 mb-2">
                             <input type="number" name="origin" placeholder="ID Asal (501=Jogja)" class="text-sm rounded p-2 border" value="501" required>
                             <input type="number" name="destination" placeholder="ID Tujuan (114=Denpasar)" class="text-sm rounded p-2 border" value="114" required>
                        </div>
                        <div class="grid grid-cols-2 gap-2 mb-2">
                             <input type="number" name="weight" placeholder="Berat (gram)" class="text-sm rounded p-2 border" value="1000" required>
                             <input type="text" name="courier" placeholder="Kurir (jne)" class="text-sm rounded p-2 border" value="jne" required>
                        </div>
                        <button type="submit" class="w-full bg-gray-600 text-white text-sm rounded py-2 hover:bg-gray-700">Cek Harga</button>
                    </form>
                </div>
            </div>

            <!-- Carriers Table -->
            <div>
                <h4 class="text-md font-bold text-gray-900 mb-3">Kurir Aktif</h4>
                <form action="{{ route('admin.integrations.carrier.store') }}" method="POST" class="flex gap-2 mb-4">
                    @csrf
                    <input type="text" name="code" placeholder="Kode (jne)" class="flex-1 bg-gray-50 border border-gray-300 text-sm rounded-lg p-2" required>
                    <input type="text" name="name" placeholder="Nama (JNE Reg)" class="flex-1 bg-gray-50 border border-gray-300 text-sm rounded-lg p-2" required>
                    <button type="submit" class="bg-blue-600 text-white text-sm rounded-lg px-3 py-2 hover:bg-blue-700">Tambah</button>
                </form>

                 <div class="overflow-x-auto border rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-2 py-2">Kode</th>
                                <th class="px-2 py-2">Nama</th>
                                <th class="px-2 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shippingCarriers as $carrier)
                            <tr>
                                <td class="px-2 py-2 font-medium text-gray-900">{{ $carrier->code }}</td>
                                <td class="px-2 py-2">{{ $carrier->name }}</td>
                                <td class="px-2 py-2">
                                    <form action="{{ route('admin.integrations.carrier.delete', $carrier->shipping_carrier_id) }}" method="POST" onsubmit="return confirm('Hapus kurir ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 3: WHATSAPP -->
    <div class="hidden p-4 rounded-lg bg-white shadow" id="whatsapp" role="tabpanel" aria-labelledby="whatsapp-tab">
         <h3 class="mb-4 text-lg font-bold text-gray-900">Konfigurasi WhatsApp (Fonnte)</h3>
         <form action="{{ route('admin.integrations.whatsapp.update') }}" method="POST" class="max-w-md">
            @csrf
            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-gray-900">Fonnte Token</label>
                <input type="text" name="token" value="{{ $fonnte->public_k ?? '' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" required>
            </div>
            <button type="submit" class="text-white bg-[#EC1C25] hover:bg-red-800 font-medium rounded-lg text-sm px-5 py-2.5">Simpan Token</button>
         </form>

         <!-- Test Sender -->
         <div class="mt-8 max-w-md p-4 border border-green-200 bg-green-50 rounded-lg">
             <h4 class="font-bold mb-3 text-green-800">Test Kirim Pesan</h4>
             <form action="{{ route('admin.integrations.whatsapp.test-send') }}" method="POST">
                @csrf
                <div class="mb-2">
                    <input type="text" name="phone" placeholder="Nomor HP (08...)" class="w-full text-sm rounded p-2 border" required>
                </div>
                <div class="mb-2">
                    <input type="text" name="message" placeholder="Pesan Test..." class="w-full text-sm rounded p-2 border" required>
                </div>
                <button type="submit" class="w-full bg-green-600 text-white text-sm rounded py-2 hover:bg-green-700">Kirim Pesan</button>
             </form>
         </div>
    </div>
</div>
@endsection
