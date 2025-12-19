@extends('layouts.admin')

@section('content')
<div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700 mb-6">
    <div class="w-full flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 sm:text-2xl dark:text-white">Integrasi Sistem</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola koneksi API pihak ketiga (Payment Gateway, Logistik, dan Notifikasi).</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">

    <!-- Card 1: Midtrans -->
    <div id="payment" class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden h-fit">
        <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white rounded-lg border border-gray-200 flex items-center justify-center p-1.5 shadow-sm">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/e/e6/Midtrans.png" alt="Midtrans" class="h-full object-contain">
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Payment Gateway</h3>
                    <p class="text-xs text-gray-500">Midtrans (VA, QRIS)</p>
                </div>
            </div>
            @if($midtrans->is_enabled)
                <span class="bg-green-100 text-green-800 text-[10px] font-bold px-2 py-0.5 rounded border border-green-200">AKTIF</span>
            @else
                <span class="bg-gray-100 text-gray-600 text-[10px] font-bold px-2 py-0.5 rounded border border-gray-200">OFF</span>
            @endif
        </div>

        <form action="{{ route('admin.integrations.payment.update') }}" method="POST" class="p-5">
            @csrf @method('PATCH')

            <div class="mb-4 flex items-center justify-between">
                <label class="text-sm font-medium text-gray-900">Status Aktif</label>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_enabled" value="1" class="sr-only peer" {{ $midtrans->is_enabled ? 'checked' : '' }}>
                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>

            <div class="space-y-3 mb-4">
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700">Mode</label>
                    <select name="mode" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2">
                        <option value="sandbox" {{ ($midtrans->config_json['mode'] ?? '') === 'sandbox' ? 'selected' : '' }}>Sandbox (Test)</option>
                        <option value="production" {{ ($midtrans->config_json['mode'] ?? '') === 'production' ? 'selected' : '' }}>Production (Live)</option>
                    </select>
                </div>
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700">Server Key</label>
                    <input type="password" name="server_key" value="{{ $midtrans->config_json['server_key'] ?? '' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2" placeholder="SB-Mid-server-...">
                </div>
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700">Client Key</label>
                    <input type="text" name="client_key" value="{{ $midtrans->config_json['client_key'] ?? '' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2" placeholder="SB-Mid-client-...">
                </div>
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700">Merchant ID</label>
                    <input type="text" name="merchant_id" value="{{ $midtrans->config_json['merchant_id'] ?? '' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2" placeholder="M-xxxx">
                </div>
            </div>

            <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-xs px-4 py-2">Simpan Midtrans</button>
        </form>
    </div>

    <!-- Card 2: RajaOngkir -->
    <div id="shipping" class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden h-fit">
        <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white rounded-lg border border-gray-200 flex items-center justify-center p-1.5 shadow-sm">
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Logistik</h3>
                    <p class="text-xs text-gray-500">RajaOngkir (Cek Ongkir)</p>
                </div>
            </div>
             @if(!empty($rajaongkir->public_k))
                <span class="bg-green-100 text-green-800 text-[10px] font-bold px-2 py-0.5 rounded border border-green-200">TERHUBUNG</span>
            @else
                <span class="bg-red-100 text-red-600 text-[10px] font-bold px-2 py-0.5 rounded border border-red-200">BELUM SET</span>
            @endif
        </div>

        <form action="{{ route('admin.integrations.shipping.update') }}" method="POST" class="p-5">
            @csrf

            <div class="space-y-3 mb-4">
                <div class="p-3 bg-orange-50 text-orange-700 text-xs rounded border border-orange-100">
                    Gunakan <b>API Key Starter</b> atau <b>Pro</b> dari RajaOngkir. Endpoint akan disesuaikan otomatis.
                </div>
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700">Tipe Akun / Base URL</label>
                    <select name="base_url" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2">
                        <option value="https://api.rajaongkir.com/starter" {{ ($rajaongkir->meta_json['base_url'] ?? '') == 'https://api.rajaongkir.com/starter' ? 'selected' : '' }}>Starter (Gratis)</option>
                        <option value="https://pro.rajaongkir.com/api" {{ ($rajaongkir->meta_json['base_url'] ?? '') == 'https://pro.rajaongkir.com/api' ? 'selected' : '' }}>PRO (Berbayar)</option>
                        <option value="https://rajaongkir.komerce.id/api/v1" {{ ($rajaongkir->meta_json['base_url'] ?? '') == 'https://rajaongkir.komerce.id/api/v1' ? 'selected' : '' }}>Komerce (V1)</option>
                    </select>
                </div>
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700">API Key</label>
                    <input type="text" name="api_key" value="{{ $rajaongkir->public_k ?? '' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2" placeholder="Masukkan API Key RajaOngkir" required>
                </div>
            </div>

            <button type="submit" class="w-full text-white bg-orange-600 hover:bg-orange-700 focus:ring-4 focus:ring-orange-300 font-medium rounded-lg text-xs px-4 py-2">Simpan RajaOngkir</button>
        </form>
    </div>

    <!-- Card 3: Fonnte (WhatsApp) -->
    <div id="whatsapp" class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden h-fit">
        <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white rounded-lg border border-gray-200 flex items-center justify-center p-1.5 shadow-sm">
                    <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">WhatsApp Gateway</h3>
                    <p class="text-xs text-gray-500">Fonnte (Notifikasi WA)</p>
                </div>
            </div>
            @if(!empty($fonnte->public_k))
                <span class="bg-green-100 text-green-800 text-[10px] font-bold px-2 py-0.5 rounded border border-green-200">TERHUBUNG</span>
            @else
                <span class="bg-red-100 text-red-600 text-[10px] font-bold px-2 py-0.5 rounded border border-red-200">BELUM SET</span>
            @endif
        </div>

        <form action="{{ route('admin.integrations.whatsapp.update') }}" method="POST" class="p-5">
            @csrf

            <div class="space-y-3 mb-4">
                <div class="p-3 bg-green-50 text-green-700 text-xs rounded border border-green-100">
                    Digunakan untuk mengirim notifikasi OTP, Order Baru, dan Update Status ke WhatsApp pengguna.
                </div>
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700">Token Fonnte</label>
                    <input type="text" name="token" value="{{ $fonnte->public_k ?? '' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2" placeholder="Masukkan Token Device Fonnte" required>
                </div>
            </div>

            <button type="submit" class="w-full text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-xs px-4 py-2">Simpan Fonnte</button>
        </form>
    </div>

</div>

<script>
    function toggleVisibility(id) {
        var input = document.getElementById(id);
        if (input.type === "password") {
            input.type = "text";
        } else {
            input.type = "password";
        }
    }
</script>
@endsection
