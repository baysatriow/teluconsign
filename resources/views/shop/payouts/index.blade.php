@extends('layouts.seller')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Saldo & Penarikan</h2>
            <p class="text-sm text-gray-500">Kelola rekening bank dan tarik penghasilan anda.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sidebar Column: Balance & Bank -->
        <div class="space-y-6">
            
            <!-- Balance Card -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#EC1C25] to-[#a50f15] p-6 text-white shadow-lg shadow-red-500/30">
                <div class="absolute right-0 top-0 -mt-4 -mr-4 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
                <div class="absolute bottom-0 left-0 -mb-4 -ml-4 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
                
                <div class="relative">
                    <p class="text-red-100 font-medium text-sm">Saldo Dapat Ditarik</p>
                    <h3 class="mt-2 text-3xl font-bold">Rp{{ number_format($currentBalance, 0, ',', '.') }}</h3>
                    <p class="mt-4 text-xs text-red-100 opacity-80">*Saldo otomatis bertambah setelah pesanan selesai.</p>
                </div>
            </div>

            <!-- Create Payout Card -->
            <div class="card-premium p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Request Penarikan</h3>
                
                @if($currentBalance < 10000)
                    <div class="bg-yellow-50 text-yellow-800 p-4 rounded-xl text-sm mb-4 border border-yellow-100">
                        <span class="font-bold">Info:</span> Minimal saldo Rp10.000 untuk penarikan.
                    </div>
                @else
                    <form action="{{ route('shop.payouts.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Pilih Rekening Tujuan</label>
                            <select name="bank_account_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-telu-red focus:border-telu-red block w-full p-2.5" required>
                                @forelse($bankAccounts as $bank)
                                    <option value="{{ $bank->bank_account_id }}">{{ $bank->bank_name }} - {{ $bank->account_no }} ({{ $bank->account_name }})</option>
                                @empty
                                    <option value="" disabled selected>Belum ada rekening</option>
                                @endforelse
                            </select>
                            @if($bankAccounts->count() == 0)
                                <p class="text-xs text-red-600 mt-1">Harap tambahkan rekening bank terlebih dahulu.</p>
                            @endif
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Nominal Penarikan</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <span class="text-gray-500 font-bold">Rp</span>
                                </div>
                                <input type="text" id="amount_input" name="amount" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-telu-red focus:border-telu-red block w-full pl-10 p-2.5" placeholder="0" required>
                            </div>
                            <p class="mt-1 text-xs text-gray-500 flex justify-between">
                                <span>Min: Rp10.000</span>
                                <span>Maks: Rp{{ number_format($currentBalance, 0, ',', '.') }}</span>
                            </p>
                        </div>
                        
                        <div class="text-xs text-gray-500 bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <ul class="list-disc pl-4 space-y-1">
                                <li>Proses penarikan 1-3 hari kerja.</li>
                                <li>Biaya admin Rp0 disubsidi platform.</li>
                            </ul>
                        </div>

                        <button type="submit" class="w-full text-white bg-telu-red hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-bold rounded-xl text-sm px-5 py-3 text-center shadow-lg shadow-red-500/30 transition-all hover:scale-[1.02]" {{ $bankAccounts->count() == 0 ? 'disabled' : '' }}>
                            Ajukan Penarikan
                        </button>
                    </form>
                    
                    <script>
                        const maxBalance = {{ $currentBalance }};
                        const inputRaw = document.getElementById('amount_input');
                        
                        if(inputRaw) {
                            inputRaw.addEventListener('input', function(e) {
                                // Remove non-digit
                                let valStr = this.value.replace(/\D/g, '');
                                let val = parseInt(valStr || '0');
                                
                                // Cap at Max
                                if(val > maxBalance) val = maxBalance;
                                
                                // Format with dots
                                this.value = new Intl.NumberFormat('id-ID').format(val);
                            });
                        }
                    </script>
                @endif
            </div>

            <!-- Add Bank Account -->
            <div class="card-premium p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Rekening Bank</h3>
                
                <div class="space-y-3 mb-6">
                    @forelse($bankAccounts as $bank)
                        <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 flex justify-between items-center group">
                            <div>
                                <p class="font-bold text-gray-900 text-sm">{{ $bank->bank_name }}</p>
                                <p class="text-xs text-gray-500">{{ $bank->account_no }} - {{ $bank->account_name }}</p>
                            </div>
                            <button onclick="confirmDelete('delete-bank-{{ $bank->bank_account_id }}')" class="text-gray-400 hover:text-red-600 transition-colors p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                            <form id="delete-bank-{{ $bank->bank_account_id }}" action="{{ route('shop.banks.delete', $bank->bank_account_id) }}" method="POST" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 italic text-center py-2">Belum ada rekening tersimpan.</p>
                    @endforelse
                </div>

                <hr class="border-gray-100 mb-4">

                <form action="{{ route('shop.banks.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <input type="text" name="bank_name" placeholder="Nama Bank (BCA, Mandiri, dll)" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-telu-red focus:border-telu-red block w-full p-2.5" required>
                    </div>
                    <div>
                        <input type="text" name="account_no" placeholder="Nomor Rekening" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-telu-red focus:border-telu-red block w-full p-2.5" required>
                    </div>
                    <div>
                        <input type="text" name="account_name" placeholder="Atas Nama" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-telu-red focus:border-telu-red block w-full p-2.5" required>
                    </div>
                    <button type="submit" class="w-full text-gray-900 bg-white border border-gray-300 hover:bg-gray-50 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors">
                        + Tambah Rekening
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Column: History -->
        <div class="lg:col-span-2 space-y-6">
            <div class="card-premium overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col md:flex-row justify-between items-center gap-4">
                    <h3 class="font-bold text-gray-800">Riwayat Penarikan</h3>
                    
                    <!-- Search Filter -->
                    <form method="GET" class="flex items-center gap-2">
                         <div class="relative">
                            <input type="date" name="date" value="{{ request('date') }}" class="w-32 text-xs border-gray-200 rounded-lg focus:ring-telu-red focus:border-telu-red p-2">
                         </div>
                         <div class="relative">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari Bank / Jumlah..." class="w-40 text-xs border-gray-200 rounded-lg focus:ring-telu-red focus:border-telu-red p-2">
                         </div>
                         <button type="submit" class="px-3 py-2 bg-telu-red text-white text-xs font-bold rounded-lg hover:bg-red-700 transition-colors">
                            Cari
                         </button>
                         @if(request('date') || request('q'))
                         <a href="{{ route('shop.payouts') }}" class="px-3 py-2 bg-gray-100 text-gray-600 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors">
                            Reset
                         </a>
                         @endif
                    </form>
                </div>
                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-semibold">Tanggal</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Jumlah</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Bank Tujuan</th>
                                <th scope="col" class="px-6 py-4 font-semibold text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($payouts as $payout)
                            <tr class="bg-white hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="block font-medium text-gray-900">{{ $payout->created_at->format('d M Y') }}</span>
                                    <span class="text-xs text-gray-500">{{ $payout->created_at->format('H:i') }} WIB</span>
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-900">
                                    Rp{{ number_format($payout->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($payout->bankAccount)
                                        <div class="text-sm">
                                            <span class="font-semibold">{{ $payout->bankAccount->bank_name }}</span>
                                            <span class="text-gray-500 block text-xs">{{ $payout->bankAccount->account_no }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">Terhapus</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $statusClass = match($payout->status) {
                                            'requested' => 'bg-yellow-100 text-yellow-800',
                                            'approved' => 'bg-blue-100 text-blue-800',
                                            'paid' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                        $statusLabel = match($payout->status) {
                                            'requested' => 'Menunggu',
                                            'approved' => 'Disetujui',
                                            'paid' => 'Dibayar',
                                            'rejected' => 'Ditolak',
                                            default => $payout->status,
                                        };
                                    @endphp
                                    <span class="{{ $statusClass }} text-xs font-bold px-2.5 py-0.5 rounded uppercase">
                                        {{ $statusLabel }}
                                    </span>
                                    @if($payout->status == 'rejected' && $payout->notes)
                                        <button onclick="showRejectionReason('{{ addslashes($payout->notes) }}')" class="mt-1 text-xs text-red-600 hover:text-red-800 font-medium underline">
                                            Lihat Detail
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    Belum ada riwayat penarikan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-100 flex justify-center">
                    {{ $payouts->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    function showRejectionReason(reason) {
        Swal.fire({
            icon: 'error',
            title: 'Penarikan Ditolak',
            html: '<div class="text-left"><strong>Alasan:</strong><br>' + reason + '</div>',
            confirmButtonColor: '#EC1C25',
            confirmButtonText: 'OK'
        });
    }
    
    function confirmDelete(formId) {
        Swal.fire({
            title: 'Hapus Rekening?',
            text: "Rekening yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }
</script>
@endsection
