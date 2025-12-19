@extends('layouts.admin')

@section('content')
<!-- Tambahkan CSS DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.tailwindcss.min.css">
<style>
    /* Custom Style untuk DataTables agar serasi dengan Flowbite */
    .dataTables_wrapper .dataTables_length select {
        padding-right: 2rem;
        background-color: #f9fafb;
        border-color: #e5e7eb;
        border-radius: 0.5rem;
        font-size: 0.875rem;
    }
    .dataTables_wrapper .dataTables_filter input {
        background-color: #f9fafb;
        border-color: #e5e7eb;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        margin-left: 0.5rem;
    }
    table.dataTable.no-footer {
        border-bottom: 1px solid #e5e7eb;
    }
    /* Styling Action Buttons Hover Effect */
    .action-btn {
        transition: all 0.2s ease;
    }
    .action-btn:hover {
        transform: scale(1.1);
    }
</style>

<div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
    <div class="w-full mb-1">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 sm:text-2xl dark:text-white">Payout Request</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola dan proses permintaan pencairan dana dari penjual.</p>
        </div>

        <!-- Statistik Mini Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <!-- Total Requests -->
            <div class="p-4 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-xs font-semibold text-blue-600 uppercase tracking-wide">Total Request</div>
                    <div class="p-1.5 bg-blue-50 text-blue-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                </div>
                <div class="text-2xl font-bold text-gray-800">{{ $stats['total_requests'] }}</div>
            </div>

            <!-- Pending -->
            <div class="p-4 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-xs font-semibold text-yellow-600 uppercase tracking-wide">Pending (Baru)</div>
                    <div class="p-1.5 bg-yellow-50 text-yellow-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl font-bold text-gray-800">{{ $stats['pending_requests'] }}</div>
            </div>

            <!-- Approved / Paid -->
            <div class="p-4 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-xs font-semibold text-green-600 uppercase tracking-wide">Selesai (Paid)</div>
                    <div class="p-1.5 bg-green-50 text-green-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                </div>
                <div class="text-2xl font-bold text-gray-800">{{ $stats['approved_requests'] }}</div>
            </div>

            <!-- Rejected -->
            <div class="p-4 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-xs font-semibold text-red-600 uppercase tracking-wide">Ditolak</div>
                    <div class="p-1.5 bg-red-50 text-red-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                </div>
                <div class="text-2xl font-bold text-gray-800">{{ $stats['rejected_requests'] }}</div>
            </div>
        </div>
    </div>
</div>

<div class="flex flex-col p-4">
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

        <!-- Header Tabel -->
        <div class="p-5 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Daftar Permintaan Pencairan</h3>
                    <p class="text-xs text-gray-500">Daftar request yang perlu diproses atau sudah selesai.</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto p-4">
            <table id="payoutTable" class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 rounded-lg">
                    <tr>
                        <th class="px-4 py-3 rounded-l-lg">ID & Waktu</th>
                        <th class="px-4 py-3">Penjual</th>
                        <th class="px-4 py-3">Jumlah (IDR)</th>
                        <th class="px-4 py-3">Info Rekening</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-center rounded-r-lg">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payouts as $payout)
                    <tr class="border-b hover:bg-gray-50 transition-colors">
                        <!-- ID & Waktu -->
                        <td class="px-4 py-3">
                            <div class="flex flex-col">
                                <span class="font-mono text-xs font-semibold text-gray-500">#{{ $payout->payout_request_id }}</span>
                                <span class="text-xs text-gray-400 mt-1">{{ $payout->requested_at->format('d M Y, H:i') }}</span>
                            </div>
                        </td>

                        <!-- Penjual -->
                        <td class="px-4 py-3 font-medium text-gray-900">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 h-8 w-8">
                                    <img class="h-8 w-8 rounded-full object-cover border border-gray-200" src="{{ $payout->seller->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($payout->seller->name).'&background=random' }}" alt="">
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-semibold">{{ $payout->seller->name }}</span>
                                    <span class="text-xs text-gray-400">{{ $payout->seller->email }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Jumlah -->
                        <td class="px-4 py-3">
                            <span class="font-bold text-gray-800 text-base">
                                Rp{{ number_format($payout->amount, 0, ',', '.') }}
                            </span>
                        </td>

                        <!-- Info Rekening -->
                        <td class="px-4 py-3">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-gray-600 uppercase">{{ $payout->bankAccount->bank_name ?? '-' }}</span>
                                <span class="text-sm font-mono text-gray-800 tracking-wide">{{ $payout->bankAccount->account_no ?? '-' }}</span>
                                <span class="text-xs text-gray-400 uppercase mt-0.5">a.n {{ $payout->bankAccount->account_name ?? '-' }}</span>
                            </div>
                        </td>

                        <!-- Status -->
                        <td class="px-4 py-3">
                            @if($payout->status === 'requested')
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-yellow-200 inline-flex items-center gap-1">
                                    <span class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></span> Pending
                                </span>
                            @elseif($payout->status === 'approved' || $payout->status === 'paid')
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-green-200 inline-flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    Paid
                                </span>
                            @elseif($payout->status === 'rejected')
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-red-200 inline-flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    Ditolak
                                </span>
                            @endif
                        </td>

                        <!-- Aksi -->
                        <td class="px-4 py-3 text-center">
                            @if($payout->status === 'requested')
                                <div class="flex justify-center gap-2">
                                    <!-- Approve Button -->
                                    <button onclick="processPayout('{{ $payout->payout_request_id }}', 'approve', '{{ $payout->amount }}')" class="action-btn bg-green-100 hover:bg-green-200 text-green-700 p-2 rounded-lg border border-green-200 shadow-sm" title="Setujui & Transfer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </button>
                                    <!-- Reject Button -->
                                    <button onclick="processPayout('{{ $payout->payout_request_id }}', 'reject', '{{ $payout->amount }}')" class="action-btn bg-red-100 hover:bg-red-200 text-red-700 p-2 rounded-lg border border-red-200 shadow-sm" title="Tolak Permintaan">
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
                                    <span class="text-xs text-gray-400 italic">Selesai</span>
                                    @if($payout->processed_at)
                                        <span class="text-[10px] text-gray-400">{{ $payout->processed_at->format('d/m/Y') }}</span>
                                    @endif
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#payoutTable').DataTable({
            "pageLength": 10,
            "lengthMenu": [10, 25, 50, 100],
            "order": [[ 0, "desc" ]], // Sort by ID/Waktu desc (Kolom pertama)
            "dom": '<"flex flex-col sm:flex-row justify-between items-center pb-4 space-y-2 sm:space-y-0"<"flex items-center gap-2"l><"flex items-center gap-2"f>>rt<"flex flex-col sm:flex-row justify-between items-center pt-4"<"text-sm text-gray-500"i><"flex justify-center"p>>',
            "language": {
                "search": "",
                "searchPlaceholder": "Cari Penjual...",
                "lengthMenu": "Tampilkan _MENU_",
                "info": "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                "paginate": { "first": "<<", "last": ">>", "next": ">", "previous": "<" },
                "zeroRecords": "Tidak ada data payout",
                "infoEmpty": "0 data"
            },
            "columnDefs": [
                { "orderable": false, "targets": 5 } // Kolom Aksi disable sort (Index ke-5 atau ke-6)
            ]
        });
    });

    function processPayout(id, action, amount) {
        let titleText = action === 'approve' ? 'Setujui Pencairan Dana?' : 'Tolak Pencairan Dana?';
        let confirmText = action === 'approve' ? 'Ya, Transfer Sekarang' : 'Ya, Tolak Request';
        let btnColor = action === 'approve' ? '#10B981' : '#d33'; // Green / Red

        let htmlText = action === 'approve'
            ? `<div class="text-left text-sm text-gray-600 bg-gray-50 p-3 rounded-lg border border-gray-200">
                <p>Pastikan Anda <b>sudah mentransfer</b> dana sebesar:</p>
                <p class="text-xl font-bold text-gray-800 mt-1 mb-2">Rp${new Intl.NumberFormat('id-ID').format(amount)}</p>
                <p>ke rekening tujuan sebelum menekan tombol setuju.</p>
               </div>`
            : `<div class="text-left text-sm text-gray-600">
                <p>Dana sebesar <b>Rp${new Intl.NumberFormat('id-ID').format(amount)}</b> akan dikembalikan ke saldo dompet penjual.</p>
               </div>`;

        Swal.fire({
            title: titleText,
            html: htmlText,
            icon: action === 'approve' ? 'info' : 'warning',
            showCancelButton: true,
            confirmButtonColor: btnColor,
            cancelButtonColor: '#6B7280',
            confirmButtonText: confirmText,
            cancelButtonText: 'Batal',
            input: action === 'reject' ? 'textarea' : undefined,
            inputPlaceholder: action === 'reject' ? 'Tuliskan alasan penolakan (mis: No. Rekening Salah)...' : '',
            inputAttributes: {
                'aria-label': 'Alasan penolakan'
            },
            preConfirm: (value) => {
                if (action === 'reject' && !value) {
                    Swal.showValidationMessage('Anda harus menuliskan alasan penolakan!')
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Set hidden inputs
                document.getElementById('status-input-' + id).value = action === 'approve' ? 'approved' : 'rejected';
                if(action === 'reject') {
                    document.getElementById('notes-input-' + id).value = result.value;
                }

                // Submit form
                document.getElementById('payout-form-' + id).submit();
            }
        });
    }
</script>
@endsection
