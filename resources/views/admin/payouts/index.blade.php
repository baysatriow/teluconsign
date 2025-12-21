@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Payout Request</h1>
    <p class="text-sm text-gray-500 mt-1">Kelola dan proses permintaan pencairan dana dari penjual.</p>
</div>

<!-- Statistik Mini Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
    <!-- Total Requests -->
    <div class="p-5 bg-white rounded-2xl shadow-soft border border-gray-100 flex flex-col justify-between group hover:-translate-y-1 transition-transform">
        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Total Request</div>
        <div class="flex items-end justify-between">
            <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_requests']) }}</div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            </div>
        </div>
    </div>

    <!-- Pending -->
    <div class="p-5 bg-white rounded-2xl shadow-soft border border-gray-100 flex flex-col justify-between group hover:-translate-y-1 transition-transform relative overflow-hidden">
        @if($stats['pending_requests'] > 0)
            <div class="absolute top-0 right-0 w-1.5 h-full bg-yellow-400"></div>
        @endif
        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Pending (Baru)</div>
        <div class="flex items-end justify-between">
            <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['pending_requests']) }}</div>
            <div class="w-10 h-10 rounded-xl bg-yellow-50 flex items-center justify-center text-yellow-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Approved / Paid -->
    <div class="p-5 bg-white rounded-2xl shadow-soft border border-gray-100 flex flex-col justify-between group hover:-translate-y-1 transition-transform">
        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Selesai (Paid)</div>
        <div class="flex items-end justify-between">
            <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['approved_requests']) }}</div>
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center text-green-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
        </div>
    </div>

    <!-- Rejected -->
    <div class="p-5 bg-white rounded-2xl shadow-soft border border-gray-100 flex flex-col justify-between group hover:-translate-y-1 transition-transform">
        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Ditolak</div>
        <div class="flex items-end justify-between">
            <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['rejected_requests']) }}</div>
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-red-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-soft border border-gray-100 overflow-hidden">

    <!-- Header & Filter -->
    <div class="p-6 border-b border-gray-100">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Daftar Permintaan Pencairan</h3>
                    <p class="text-xs text-gray-500">Antrian request pencairan dana seller.</p>
                </div>
            </div>

            <!-- Filter Form -->
             <form action="{{ route('admin.payouts') }}" method="GET" class="flex flex-col md:flex-row items-center gap-3 w-full md:w-auto">
                <div class="relative w-full md:w-64">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="q" value="{{ request('q') }}" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full pl-10 p-2.5 transition-all" placeholder="Cari Penjual / No. Rek...">
                </div>

                <select name="status" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full md:w-40 p-2.5 transition-all">
                    <option value="">Semua Status</option>
                    <option value="requested" {{ request('status') == 'requested' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Paid</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>

                <button type="submit" class="text-white bg-gray-900 hover:bg-black font-bold rounded-xl text-sm px-5 py-2.5 shadow-lg transition-transform hover:-translate-y-0.5">
                    Filter
                </button>
                
                @if(request()->has('q') || request()->has('status'))
                    <a href="{{ route('admin.payouts') }}" class="text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 font-bold rounded-xl text-sm px-4 py-2.5 transition-colors">
                        Reset
                    </a>
                @endif
            </form>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 rounded-lg">
                <tr>
                    <th class="px-6 py-4 rounded-l-xl">ID & Waktu</th>
                    <th class="px-6 py-4">Penjual</th>
                    <th class="px-6 py-4">Jumlah (IDR)</th>
                    <th class="px-6 py-4">Info Rekening</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-center rounded-r-xl">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($payouts as $payout)
                <tr class="hover:bg-gray-50 transition-colors">
                    <!-- ID & Waktu -->
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-mono text-xs font-bold text-gray-900 bg-gray-100 px-2 py-1 rounded w-fit mb-1">#{{ $payout->payout_request_id }}</span>
                            <span class="text-xs text-gray-400">{{ $payout->requested_at->format('d M Y, H:i') }}</span>
                        </div>
                    </td>

                    <!-- Penjual -->
                    <td class="px-6 py-4 font-medium text-gray-900">
                        <div class="flex items-center gap-3">
                            <img class="h-9 w-9 rounded-full object-cover border border-gray-200" src="{{ $payout->seller->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($payout->seller->name).'&background=random' }}" alt="">
                            <div class="flex flex-col">
                                <span class="font-bold">{{ $payout->seller->name }}</span>
                                <span class="text-xs text-gray-400 font-normal">{{ $payout->seller->email }}</span>
                            </div>
                        </div>
                    </td>

                    <!-- Jumlah -->
                    <td class="px-6 py-4">
                        <span class="font-bold text-gray-900 text-base">
                            Rp{{ number_format($payout->amount, 0, ',', '.') }}
                        </span>
                    </td>

                    <!-- Info Rekening -->
                    <td class="px-6 py-4">
                        <div class="flex flex-col bg-gray-50 p-2 rounded-lg border border-gray-100 max-w-xs">
                            <span class="text-xs font-bold text-gray-600 uppercase">{{ $payout->bankAccount->bank_name ?? '-' }}</span>
                            <span class="text-sm font-mono text-gray-900 tracking-wide font-bold">{{ $payout->bankAccount->account_no ?? '-' }}</span>
                            <span class="text-[10px] text-gray-500 uppercase mt-0.5 truncate">a.n {{ $payout->bankAccount->account_name ?? '-' }}</span>
                        </div>
                    </td>

                    <!-- Status -->
                    <td class="px-6 py-4">
                        @if($payout->status === 'requested')
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-3 py-1 rounded-full border border-yellow-200 inline-flex items-center gap-1 shadow-sm">
                                <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full animate-pulse"></span> Pending
                            </span>
                        @elseif($payout->status === 'approved' || $payout->status === 'paid')
                            <span class="bg-green-100 text-green-800 text-xs font-bold px-3 py-1 rounded-full border border-green-200 inline-flex items-center gap-1 shadow-sm">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                Paid
                            </span>
                        @elseif($payout->status === 'rejected')
                            <span class="bg-red-100 text-red-800 text-xs font-bold px-3 py-1 rounded-full border border-red-200 inline-flex items-center gap-1 shadow-sm">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Ditolak
                            </span>
                        @endif
                    </td>

                    <!-- Aksi -->
                    <td class="px-6 py-4 text-center">
                        @if($payout->status === 'requested')
                            <div class="flex justify-center gap-2">
                                <!-- Approve Button -->
                                <button onclick="processPayout('{{ $payout->payout_request_id }}', 'approve', '{{ $payout->amount }}')" class="bg-green-100 hover:bg-green-200 text-green-700 p-2 rounded-xl border border-green-200 shadow-sm transition-all hover:scale-105" title="Setujui & Transfer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </button>
                                <!-- Reject Button -->
                                <button onclick="processPayout('{{ $payout->payout_request_id }}', 'reject', '{{ $payout->amount }}')" class="bg-red-100 hover:bg-red-200 text-red-700 p-2 rounded-xl border border-red-200 shadow-sm transition-all hover:scale-105" title="Tolak Permintaan">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>

                            <!-- Hidden Form -->
                            <form id="payout-form-{{ $payout->payout_request_id }}" action="{{ route('admin.payouts.update', $payout->payout_request_id) }}" method="POST" class="hidden">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" id="status-input-{{ $payout->payout_request_id }}">
                                <input type="hidden" name="notes" id="notes-input-{{ $payout->payout_request_id }}">
                            </form>
                        @else
                            <div class="flex flex-col items-center">
                                <span class="text-xs font-bold text-gray-400">Selesai</span>
                                @if($payout->processed_at)
                                    <span class="text-[10px] text-gray-400">{{ $payout->processed_at->format('d/m/Y') }}</span>
                                @endif
                            </div>
                        @endif
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-500 py-12">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4 text-gray-400">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <p class="font-medium">Tidak ada data payout.</p>
                                <p class="text-xs mt-1">Belum ada request pencairan dana yang masuk.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6 px-6 pb-6">
        {{ $payouts->links('pagination::tailwind') }}
    </div>
</div>

<!-- Scripts -->
<script>
    function processPayout(id, action, amount) {
        let titleText = action === 'approve' ? 'Setujui Pencairan Dana?' : 'Tolak Pencairan Dana?';
        let confirmText = action === 'approve' ? 'Ya, Transfer Sekarang' : 'Ya, Tolak Request';
        let btnColor = action === 'approve' ? '#10B981' : '#EF4444'; // Green / Red

        let formattedAmount = new Intl.NumberFormat('id-ID').format(amount);
        
        let htmlText = action === 'approve'
            ? `<div class="text-left text-sm text-gray-600 bg-gray-50 p-4 rounded-xl border border-gray-200">
                <p class="mb-2">Pastikan Anda <b>sudah mentransfer</b> dana sebesar:</p>
                <p class="text-2xl font-bold text-gray-900 mb-2">Rp${formattedAmount}</p>
                <p class="text-xs text-gray-500">ke rekening tujuan pengguna sebelum menekan tombol setuju.</p>
               </div>`
            : `<div class="text-left text-sm text-gray-600 bg-red-50 p-4 rounded-xl border border-red-100">
                <p>Dana sebesar <b>Rp${formattedAmount}</b> akan dikembalikan ke saldo dompet penjual.</p>
               </div>`;

        Swal.fire({
            title: titleText,
            html: htmlText,
            icon: action === 'approve' ? 'info' : 'warning',
            showCancelButton: true,
            confirmButtonColor: btnColor,
            cancelButtonColor: '#E5E7EB',
            confirmButtonText: confirmText,
            cancelButtonText: '<span class="text-gray-800 font-bold">Batal</span>',
            input: action === 'reject' ? 'textarea' : undefined,
            inputPlaceholder: action === 'reject' ? 'Tuliskan alasan penolakan (mis: No. Rekening Salah)...' : '',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl px-5 py-2.5 font-bold shadow-lg',
                cancelButton: 'rounded-xl px-5 py-2.5',
                input: 'rounded-xl border-gray-300 focus:ring-red-500 focus:border-red-500'
            },
            preConfirm: (value) => {
                if (action === 'reject' && !value) {
                    Swal.showValidationMessage('Anda harus menuliskan alasan penolakan!')
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('status-input-' + id).value = action === 'approve' ? 'approved' : 'rejected';
                if(action === 'reject') {
                    document.getElementById('notes-input-' + id).value = result.value;
                }
                document.getElementById('payout-form-' + id).submit();
            }
        });
    }
</script>
@endsection
