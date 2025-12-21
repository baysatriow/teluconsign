@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">WhatsApp Notification</h1>
    <p class="text-sm text-gray-500 mt-1">Integrasi Fonnte untuk notifikasi order otomatis ke WhatsApp.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8">

    <!-- LEFT: CONFIG -->
    <div class="bg-white rounded-2xl shadow-soft p-8 border border-gray-100 relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-5">
            <svg class="w-32 h-32 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
        </div>

        <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-3 relative z-10">
            <div class="p-2 bg-green-50 rounded-lg text-green-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
            </div>
            Konfigurasi Token Fonnte
        </h3>
        
        <form action="{{ route('admin.integrations.whatsapp.update') }}" method="POST" class="relative z-10">
            @csrf
            
            <div class="mb-8">
                <label class="block mb-2 text-sm font-semibold text-gray-700">Fonnte TokenAPI</label>
                <div class="relative">
                    <input type="text" name="token" value="{{ $fonnte->public_k ?? '' }}" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-green-500 block w-full p-3 pl-10 transition-all font-mono" placeholder="Paste Token disini..." required>
                     <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 14l-1 1-1 1H6v3.586a1 1 0 01-1.414 1.414l-7-7a1 1 0 001.414-1.414l6 6a1 1 0 001.414 1.414L11 14l5-5a6 6 0 010-8.486z"/></svg>
                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-500">
                    Dapatkan token di <a href="https://fonnte.com" target="_blank" class="text-green-600 hover:text-green-800 font-bold underline transition-colors">fonnte.com</a>. Pastikan akun aktif.
                </p>
            </div>

            <button type="submit" class="w-full text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-100 font-bold rounded-xl text-sm px-5 py-3 text-center shadow-lg shadow-green-500/30 transform transition hover:-translate-y-0.5">
                Simpan Token
            </button>
        </form>
    </div>

    <!-- RIGHT: TEST SENDER -->
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 text-white rounded-2xl shadow-soft p-8 relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
        <div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
        
        <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-3 relative z-10">
            <div class="p-2 bg-white/10 rounded-lg backdrop-blur-sm">
                 <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            </div>
            Test Kirim Pesan
        </h3>
        
        <form action="{{ route('admin.integrations.whatsapp.test-send') }}" method="POST" class="relative z-10 space-y-4">
            @csrf
            <div>
                <label class="block mb-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Nomor Tujuan</label>
                <input type="text" name="phone" placeholder="0812xxxx" class="w-full bg-white/10 border border-white/10 text-white rounded-xl text-sm focus:ring-2 focus:ring-white/20 focus:border-white/50 p-3 placeholder-gray-500 transition-all" required>
            </div>
            <div>
                <label class="block mb-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Pesan</label>
                <textarea name="message" rows="3" class="w-full bg-white/10 border border-white/10 text-white rounded-xl text-sm focus:ring-2 focus:ring-white/20 focus:border-white/50 p-3 placeholder-gray-500 transition-all" placeholder="Halo, ini adalah pesan test dari sistem..."></textarea>
            </div>
            <button type="submit" class="w-full bg-white text-gray-900 font-bold rounded-xl text-sm px-5 py-3 hover:bg-gray-100 shadow-lg transition-transform hover:-translate-y-0.5 mt-2">
                <span class="flex items-center justify-center gap-2">
                    Kirim Pesan
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </span>
            </button>
        </form>
    </div>

</div>
@endsection
