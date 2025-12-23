@extends('layouts.admin')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-red-600 flex items-center justify-center shadow-lg shadow-red-200">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Webhook Logs</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau traffic request dari integrasi pihak ketiga.</p>
        </div>
    </div>
</div>

<!-- FILTER CARD -->
<div class="bg-white rounded-2xl shadow-soft p-5 mb-8 border border-gray-100" x-data="{ 
    startDate: '{{ $startDate }}',
    endDate: '{{ $endDate }}',
    provider: '{{ request('provider') }}',
    providerName: '{{ request('provider') ? strtoupper(request('provider')) : 'Semua Provider' }}',
    dropdownOpen: false,
    providers: {{ $providers->map(fn($p) => ['id' => $p, 'name' => strtoupper($p)])->toJson() }}
}">
    <form action="{{ route('admin.integrations.webhook-logs') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
        
        <!-- Date Range -->
        <div class="md:col-span-4 grid grid-cols-2 gap-2">
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Dari Tanggal</label>
                <input type="date" name="start_date" x-model="startDate" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 p-2.5 transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Sampai Tanggal</label>
                <input type="date" name="end_date" x-model="endDate" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 p-2.5 transition-all">
            </div>
        </div>

        <!-- Provider Filter -->
        <div class="md:col-span-3">
            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Provider</label>
            <div class="relative">
                <input type="hidden" name="provider" x-model="provider">
                <button type="button" @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 block w-full p-2.5 text-left flex justify-between items-center transition-all">
                    <span x-text="providerName"></span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{'rotate-180': dropdownOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div x-show="dropdownOpen" x-transition class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden" style="display: none;">
                    <div @click="provider = ''; providerName = 'Semua Provider'; dropdownOpen = false" class="px-4 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 cursor-pointer flex items-center justify-between group">
                        <span>Semua Provider</span>
                        <span x-show="provider === ''" class="text-red-500 font-bold">&check;</span>
                    </div>
                    <template x-for="p in providers" :key="p.id">
                        <div @click="provider = p.id; providerName = p.name; dropdownOpen = false" class="px-4 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 cursor-pointer flex items-center justify-between group">
                            <span x-text="p.name"></span>
                            <span x-show="provider === p.id" class="text-red-500 font-bold">&check;</span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="md:col-span-3 flex gap-2">
            <button type="submit" class="flex-1 inline-flex justify-center items-center px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-red-500/20 transform hover:-translate-y-0.5">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Filter
            </button>
            <a href="{{ route('admin.integrations.webhook-logs') }}" class="inline-flex justify-center items-center px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-700 text-sm font-bold rounded-xl transition-colors">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- LOGS TABLE -->
<div class="bg-white rounded-2xl shadow-soft border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50/50 border-b border-gray-100">
                <tr>
                    <th scope="col" class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Waktu</th>
                    <th scope="col" class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Provider</th>
                    <th scope="col" class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Event</th>
                    <th scope="col" class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Payload Preview</th>
                    <th scope="col" class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Detail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50/80 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-900">{{ $log->received_at->format('d M Y') }}</span>
                                <span class="text-xs text-gray-400 font-mono">{{ $log->received_at->format('H:i:s') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $colors = [
                                    'midtrans' => 'blue',
                                    'whatsapp' => 'green',
                                    'rajaongkir' => 'orange',
                                    'fonnte' => 'emerald'
                                ];
                                $color = $colors[$log->provider_code] ?? 'gray';
                            @endphp
                            <div class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold bg-{{ $color }}-50 text-{{ $color }}-700 border border-{{ $color }}-100 uppercase tracking-wide">
                                {{ $log->provider_code }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-700">{{ $log->event_type ?? '-' }}</div>
                            @if($log->related_id)
                                <div class="text-[10px] text-gray-400 font-mono mt-0.5 flex items-center gap-1 group/id cursor-pointer hover:text-red-500" onclick="navigator.clipboard.writeText('{{ $log->related_id }}'); alert('ID Copied!')" title="Click to Copy">
                                    <span class="truncate max-w-[120px]">{{ $log->related_id }}</span>
                                    <svg class="w-3 h-3 opacity-0 group-hover/id:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="max-w-xs truncate font-mono text-[10px] text-gray-400 bg-gray-50 px-2 py-1 rounded border border-gray-100">
                                {{ Str::limit(json_encode($log->payload), 60) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button onclick="showPayload('{{ $log->webhook_log_id }}')" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-400 hover:text-red-600 hover:border-red-200 hover:bg-red-50 transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                            
                            <!-- Hidden Data -->
                            <div id="payload-{{ $log->webhook_log_id }}" class="hidden">
                                {{ json_encode($log->payload, JSON_PRETTY_PRINT) }}
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 011.414.293l5.414 5.414a1 1 0 01.293 1.414V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <h3 class="text-gray-900 font-bold mb-1">Belum ada Log</h3>
                                <p class="text-gray-500 text-sm">Aktivitas webhook akan muncul di sini.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="bg-white px-6 py-4 border-t border-gray-100 flex items-center justify-between">
        <div class="text-[10px] sm:text-xs text-gray-400 font-medium">
            Showing <span class="text-gray-700">{{ $logs->firstItem() ?? 0 }}</span> to <span class="text-gray-700">{{ $logs->lastItem() ?? 0 }}</span> of <span class="text-gray-700">{{ $logs->total() }}</span> entries
        </div>
        <div class="webhook-pagination">
            {{ $logs->appends(request()->query())->onEachSide(1)->links() }}
        </div>
    </div>
</div>

<!-- DATA MODAL (Reused from previous step, integrated logic) -->
<div id="payloadModal" class="fixed inset-0 z-[60] overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <!-- Modal Panel -->
        <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-gray-100">
            <div class="bg-white px-6 py-4 border-b border-gray-50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    </div>
                    Payload Detail
                </h3>
                <button type="button" class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 transition-colors" onclick="closeModal()">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <div class="bg-[#1e1e1e] relative group">
                <pre id="modalContent" class="text-blue-300 p-6 text-[11px] font-mono overflow-auto max-h-[500px] leading-relaxed custom-scrollbar"></pre>
                <button onclick="copyPayload()" class="absolute top-4 right-4 bg-white/10 hover:bg-white/20 text-white px-3 py-1.5 rounded-lg transition-all flex items-center gap-2 text-xs backdrop-blur-sm opacity-0 group-hover:opacity-100" title="Copy JSON">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                    Copy JSON
                </button>
            </div>
            
            <div class="bg-gray-50 px-6 py-4 flex justify-end border-t border-gray-100">
                <button type="button" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-red-500/20" onclick="closeModal()">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function showPayload(id) {
        let content = document.getElementById('payload-' + id).innerText;
        try {
            // Pretty Print Re-format
            const jsonObj = JSON.parse(content);
            content = JSON.stringify(jsonObj, null, 2);
        } catch(e) {}
        
        document.getElementById('modalContent').textContent = content;
        document.getElementById('payloadModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('payloadModal').classList.add('hidden');
    }

    function copyPayload() {
        const content = document.getElementById('modalContent').textContent;
        navigator.clipboard.writeText(content);
        // Toast logic could go here
    }
</script>
<style>
    .webhook-pagination nav > div:first-child { display: none; }
    .webhook-pagination nav span[aria-current="page"] > span {
        @apply bg-red-600 border-red-600 text-white shadow-md shadow-red-200;
    }
    .webhook-pagination nav a:hover {
        @apply text-red-600 bg-red-50 border-red-200;
    }
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #2d2d2d;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #4a4a4a;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #5a5a5a;
    }
</style>
@endsection
