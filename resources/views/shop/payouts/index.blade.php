@extends('layouts.seller')

@section('content')
<div class="space-y-6">
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;                 /* width of the entire scrollbar */
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f9fafb;        /* color of the tracking area */
            border-radius: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #d1d5db;  /* color of the scroll thumb */
            border-radius: 20px;        /* roundness of the scroll thumb */
            border: 2px solid #f9fafb;  /* creates padding around scroll thumb */
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: #9ca3af;  /* color of the scroll thumb on hover */
        }
        /* Firefox */
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #d1d5db #f9fafb;
        }
    </style>

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
                    <form action="{{ route('shop.payouts.store') }}" method="POST" class="space-y-4" id="withdrawalForm">
                        @csrf
                            @php
                                $withdrawalOptions = $bankAccounts->map(function($bank) {
                                    return [
                                        'value' => $bank->bank_account_id,
                                        'label' => $bank->bank_name . ' - ' . $bank->account_no . ' (' . $bank->account_name . ')'
                                    ];
                                });
                            @endphp
                            <div x-data="{
                                open: false,
                                search: '',
                                selected: '',
                                label: 'Pilih Rekening Tujuan',
                                options: {{ $withdrawalOptions->toJson() }},
                                init() {
                                    if (this.options.length > 0) {
                                        // Optional: Auto-select first if only one? Or just leave empty
                                    }
                                },
                                get filtered() {
                                    if (this.search === '') return this.options;
                                    return this.options.filter(item => item.label.toLowerCase().includes(this.search.toLowerCase()));
                                },
                                select(item) {
                                    this.selected = item.value;
                                    this.label = item.label;
                                    this.open = false;
                                    this.search = '';
                                }
                            }" class="relative">
                                <label class="block mb-2 text-sm font-medium text-gray-900">Pilih Rekening Tujuan</label>
                                <input type="hidden" name="bank_account_id" :value="selected" required>
                                
                                <button type="button" @click="open = !open" class="relative w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-telu-red focus:border-telu-red p-2.5 text-left flex justify-between items-center">
                                    <span x-text="selected ? label : 'Pilih Rekening Tujuan'" :class="{'text-gray-500': !selected, 'text-gray-900': selected}"></span>
                                    <svg class="w-4 h-4 text-gray-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>

                                <div x-show="open" @click.away="open = false" class="absolute z-50 w-full mt-1.5 bg-white border border-gray-100 rounded-xl shadow-2xl shadow-gray-200/50 max-h-60 overflow-hidden ring-1 ring-black/5" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 scale-95 translate-y-[-10px]" x-transition:enter-end="transform opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="transform opacity-100 scale-100 translate-y-0" x-transition:leave-end="transform opacity-0 scale-95 translate-y-[-10px]">
                                    <div class="p-2 border-b border-gray-100 bg-gray-50/50">
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                            </div>
                                            <input x-model="search" type="text" class="w-full pl-9 text-sm border-gray-200 rounded-md focus:border-telu-red focus:ring-telu-red bg-white py-2" placeholder="Cari rekening..." autofocus>
                                        </div>
                                    </div>
                                    <ul class="max-h-48 overflow-y-auto py-1 custom-scrollbar">
                                        <template x-for="item in filtered" :key="item.value">
                                            <li @click="select(item)" class="px-4 py-2 hover:bg-red-50 cursor-pointer text-sm text-gray-700 flex items-center gap-2 transition-colors" :class="{'bg-red-50 text-telu-red font-medium': selected === item.value}">
                                                <svg class="w-4 h-4 text-gray-400" :class="{'text-telu-red': selected === item.value}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                                <span x-text="item.label"></span>
                                            </li>
                                        </template>
                                        <div x-show="filtered.length === 0" class="px-4 py-3 text-sm text-gray-500 italic text-center">
                                            Tidak ditemukan.
                                        </div>
                                    </ul>
                                </div>

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
                                <input type="text" id="amount_input" name="amount" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-telu-red focus:border-telu-red block w-full pl-10 p-2.5" placeholder="0" data-min="10000" required>
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
                        
                        // Withdrawal form validation
                        const withdrawalForm = document.getElementById('withdrawalForm');
                        if (withdrawalForm) {
                            withdrawalForm.addEventListener('submit', function(e) {
                                const amountValue = inputRaw.value.replace(/\./g, '');
                                const amount = parseInt(amountValue || '0');
                                const minAmount = parseInt(inputRaw.getAttribute('data-min'));
                                
                                if (amount < minAmount) {
                                    e.preventDefault();
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Jumlah Tidak Valid',
                                        html: `<div class="text-gray-700">Jumlah penarikan minimal adalah <strong>Rp ${new Intl.NumberFormat('id-ID').format(minAmount)}</strong></div>`,
                                        buttonsStyling: false,
                                        customClass: {
                                            popup: 'rounded-2xl shadow-2xl border-0',
                                            title: 'text-xl font-bold text-gray-900 mb-2',
                                            htmlContainer: 'text-sm',
                                            confirmButton: 'w-full bg-telu-red hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg mt-4'
                                        },
                                        confirmButtonText: 'Mengerti'
                                    });
                                    return false;
                                }
                            });
                        }
                    </script>
                @endif
            </div>

            <!-- Add Bank Account -->
            <div class="card-premium p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Rekening Bank</h3>
                
                <div class="space-y-3 mb-6" x-data="{
                    editModal: false,
                    editForm: {
                        id: '',
                        bank_name: '',
                        account_no: '',
                        account_name: ''
                    },
                    openEdit(bank) {
                        this.editForm = {
                            id: bank.bank_account_id,
                            bank_name: bank.bank_name,
                            account_no: bank.account_no,
                            account_name: bank.account_name
                        };
                        this.editModal = true;
                    }
                }">
                    @forelse($bankAccounts as $bank)
                        <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 flex justify-between items-center group">
                            <div class="flex-1">
                                <p class="font-bold text-gray-900 text-sm">{{ $bank->bank_name }}</p>
                                <p class="text-xs text-gray-500">{{ $bank->account_no }} - {{ $bank->account_name }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="openEdit({{ json_encode($bank) }})" class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all duration-200" title="Edit">
                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.138 2.362a2.121 2.121 0 013 3L11 14.362 8 15.362l1-3 8.138-8.138z"></path></svg>
                                </button>
                                <button onclick="confirmDeleteBank('{{ $bank->bank_account_id }}')" class="flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all duration-200" title="Hapus">
                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                            <form id="delete-bank-{{ $bank->bank_account_id }}" action="{{ route('shop.banks.delete', $bank->bank_account_id) }}" method="POST" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 italic text-center py-2">Belum ada rekening tersimpan.</p>
                    @endforelse

                    <!-- Modal Edit Bank -->
                    <div x-show="editModal" 
                         class="fixed inset-0 z-[60] overflow-y-auto" 
                         style="display: none;"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0">
                        <div class="flex items-center justify-center min-h-screen p-4">
                            <div class="fixed inset-0 bg-black/50 transition-opacity" @click="editModal = false"></div>
                            
                            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0">
                                <div class="flex justify-between items-center mb-6">
                                    <h4 class="text-lg font-bold text-gray-900">Edit Rekening Bank</h4>
                                    <button @click="editModal = false" class="text-gray-400 hover:text-gray-500">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>

                                <form :action="'{{ url('myshop/banks') }}/' + editForm.id" method="POST" class="space-y-4">
                                    @csrf @method('PUT')
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900">Nama Bank</label>
                                        <input type="text" name="bank_name" x-model="editForm.bank_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-telu-red focus:border-telu-red block w-full p-2.5" required>
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900">Nomor Rekening</label>
                                        <input type="text" name="account_no" x-model="editForm.account_no" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-telu-red focus:border-telu-red block w-full p-2.5" required>
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900">Atas Nama</label>
                                        <input type="text" name="account_name" x-model="editForm.account_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-telu-red focus:border-telu-red block w-full p-2.5" required>
                                    </div>
                                    <div class="flex gap-3 pt-2">
                                        <button type="button" @click="editModal = false" class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 font-bold rounded-xl hover:bg-gray-50 transition-all">Batal</button>
                                        <button type="submit" class="flex-1 px-4 py-2.5 bg-[#EC1C25] text-white font-bold rounded-xl hover:bg-[#d0181f] transition-all shadow-lg shadow-red-500/20">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100 mb-4">

                <form action="{{ route('shop.banks.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <div x-data="{
                        open: false,
                        search: '',
                        selected: '',
                        options: [
                            'Bank Central Asia (BCA)', 'Bank Mandiri', 'Bank Negara Indonesia (BNI)', 'Bank Rakyat Indonesia (BRI)',
                            'Bank Tabungan Negara (BTN)', 'Bank CIMB Niaga', 'Bank Danamon', 'Bank Permata', 'Bank Maybank Indonesia',
                            'Bank Panin', 'Bank OCBC NISP', 'Bank UOB Indonesia', 'Bank BTN Syariah', 'Bank Syariah Indonesia (BSI)',
                            'Bank Muamalat', 'Bank Mega', 'Bank Bukopin', 'Bank BCA Syariah', 'Bank BTPN', 'Bank Sinarmas',
                            'Bank Commonwealth', 'Bank DBS Indonesia', 'Bank Jago (Bank Jago Tbk)', 'SeaBank Indonesia',
                            'Bank Neo Commerce (BNC)', 'Bank Allo', 'Jenius (BTPN)', 'Blu by BCA Digital', 'MotionBanking (MNC Bank)',
                            'Digibank (DBS)', 'Lainnya'
                        ],
                        get filtered() {
                            if (this.search === '') return this.options;
                            return this.options.filter(option => option.toLowerCase().includes(this.search.toLowerCase()));
                        },
                        select(option) {
                            this.selected = option;
                            this.open = false;
                            this.search = '';
                        }
                    }" class="relative">
                        <label class="block mb-2 text-sm font-medium text-gray-900">Nama Bank</label>
                        <input type="hidden" name="bank_name" :value="selected" required>
                        
                        <button type="button" @click="open = !open" class="relative w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-telu-red focus:border-telu-red p-2.5 text-left flex justify-between items-center">
                            <span x-text="selected ? selected : 'Pilih Bank'" :class="{'text-gray-500': !selected, 'text-gray-900': selected}"></span>
                            <svg class="w-4 h-4 text-gray-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div x-show="open" @click.away="open = false" class="absolute z-50 w-full mt-1.5 bg-white border border-gray-100 rounded-xl shadow-2xl shadow-gray-200/50 max-h-60 overflow-hidden ring-1 ring-black/5" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 scale-95 translate-y-[-10px]" x-transition:enter-end="transform opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="transform opacity-100 scale-100 translate-y-0" x-transition:leave-end="transform opacity-0 scale-95 translate-y-[-10px]">
                            <div class="p-2 border-b border-gray-100 bg-gray-50/50">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                    <input x-model="search" type="text" class="w-full pl-9 text-sm border-gray-200 rounded-md focus:border-telu-red focus:ring-telu-red bg-white py-2" placeholder="Cari bank..." autofocus>
                                </div>
                            </div>
                            <ul class="max-h-48 overflow-y-auto py-1 custom-scrollbar">
                                <template x-for="option in filtered" :key="option">
                                    <li @click="select(option)" class="px-4 py-2 hover:bg-red-50 cursor-pointer text-sm text-gray-700 flex items-center gap-2 transition-colors" :class="{'bg-red-50 text-telu-red font-medium': selected === option}">
                                        <svg class="w-4 h-4 text-gray-400" :class="{'text-telu-red': selected === option}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        <span x-text="option"></span>
                                    </li>
                                </template>
                                <div x-show="filtered.length === 0" class="px-4 py-3 text-sm text-gray-500 italic text-center">
                                    Tidak ditemukan.
                                </div>
                            </ul>
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Nomor Rekening</label>
                        <input type="text" name="account_no" placeholder="Contoh: 1234567890" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-telu-red focus:border-telu-red block w-full p-2.5" required>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Atas Nama</label>
                        <input type="text" name="account_name" placeholder="Nama Pemilik Rekening" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-telu-red focus:border-telu-red block w-full p-2.5" required>
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
                <div class="px-6 py-5 border-b border-gray-100 bg-white flex flex-col lg:flex-row justify-between lg:items-center gap-6">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-red-50 rounded-xl">
                            <svg class="w-5 h-5 text-[#EC1C25]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg">Riwayat Penarikan</h3>
                    </div>
                    
                    <!-- Premium Search Filter -->
                    <form method="GET" class="flex flex-wrap items-center gap-3">
                         <div class="relative group">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400 group-focus-within:text-[#EC1C25] transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <input type="date" name="date" value="{{ request('date') }}" 
                                class="pl-10 pr-4 py-2.5 w-full md:w-44 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-red-500/10 focus:border-[#EC1C25] focus:bg-white transition-all duration-200 hover:border-gray-300 outline-none text-gray-700">
                         </div>
                         
                         <div class="relative group">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400 group-focus-within:text-[#EC1C25] transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari Bank / Jumlah..." 
                                class="pl-10 pr-4 py-2.5 w-full md:w-64 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-red-500/10 focus:border-[#EC1C25] focus:bg-white transition-all duration-200 hover:border-gray-300 outline-none">
                         </div>

                         <div class="flex items-center gap-2">
                             <button type="submit" class="px-6 py-2.5 bg-[#EC1C25] hover:bg-[#d0181f] text-white text-sm font-bold rounded-xl transition-all duration-200 shadow-lg shadow-red-500/20 active:scale-95">
                                Cari
                             </button>
                             @if(request('date') || request('q'))
                             <a href="{{ route('shop.payouts') }}" class="px-6 py-2.5 bg-white border border-gray-200 text-gray-600 hover:text-[#EC1C25] hover:bg-red-50 hover:border-red-100 text-sm font-bold rounded-xl transition-all duration-200 shadow-sm active:scale-95" title="Reset Filter">
                                Reset
                             </a>
                             @endif
                         </div>
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
                                    <span class="block font-medium text-gray-900">{{ $payout->requested_at ? $payout->requested_at->format('d M Y') : '-' }}</span>
                                    <span class="text-xs text-gray-500">{{ $payout->requested_at ? $payout->requested_at->format('H:i') : '' }} WIB</span>
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
                                        <button onclick="showRejectionReason('{{ addslashes($payout->notes) }}')" class="mx-auto mt-2 flex items-center justify-center gap-1.5 text-xs text-red-600 hover:text-white font-bold border border-red-200 px-3 py-1.5 rounded-lg hover:bg-red-600 hover:border-red-600 transition-all shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
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
    
    function confirmDeleteBank(id) {
        console.log('Confirming deletion for bank ID:', id);
        
        // Show loading state while checking
        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang mengecek status rekening',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Fetch check status
        fetch(`{{ url('myshop/banks') }}/${id}/check`)
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(data => {
                Swal.close();
                const count = data.pending_count;
                const otherBanks = data.other_banks;

                if (count > 0) {
                    if (otherBanks.length === 0) {
                        Swal.fire({
                            title: 'Tidak Bisa Menghapus',
                            html: `<div class="text-sm text-gray-600 text-left bg-red-50 p-3 rounded-lg border border-red-100">Ada <b>${count}</b> penarikan berstatus <b>Menunggu (Pending)</b> di rekening ini. Anda harus memiliki setidaknya satu rekening lain untuk mengalihkan penarikan tersebut sebelum menghapus rekening ini.</div>`,
                            icon: 'error',
                            confirmButtonColor: '#EC1C25',
                            confirmButtonText: 'Tutup'
                        });
                        return;
                    }

                    // Show transfer modal
                    let optionsMarkup = otherBanks.map(b => `<option value="${b.bank_account_id}">${b.bank_name} - ${b.account_no}</option>`).join('');
                    
                    Swal.fire({
                        title: 'Ada Penarikan Pending',
                        html: `
                            <div class="text-sm text-gray-600 mb-4 text-left">
                                Ditemukan <b>${count}</b> penarikan berstatus pending. Silakan pilih rekening lain untuk menampung saldo tersebut:
                            </div>
                            <select id="transfer_to_select" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-telu-red focus:border-telu-red block p-2.5">
                                ${optionsMarkup}
                            </select>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#EC1C25',
                        confirmButtonText: 'Pindahkan & Hapus',
                        cancelButtonText: 'Batal',
                        preConfirm: () => {
                            const selectEl = document.getElementById('transfer_to_select');
                            if (!selectEl) return null;
                            const val = selectEl.value;
                            if (!val) {
                                Swal.showValidationMessage('Silakan pilih rekening tujuan');
                            }
                            return val;
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const formId = 'delete-bank-' + id;
                            const form = document.getElementById(formId);
                            if (form) {
                                // Add transfer_to field to form
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'transfer_to';
                                input.value = result.value;
                                form.appendChild(input);
                                form.submit();
                            } else {
                                console.error('Form not found:', formId);
                                Swal.fire('Error', 'Form tidak ditemukan. ID: ' + formId, 'error');
                            }
                        }
                    });
                } else {
                    // Simple confirm
                    Swal.fire({
                        title: 'Hapus Rekening?',
                        text: "Rekening yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#EC1C25',
                        cancelButtonColor: '#6B7280',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const formId = 'delete-bank-' + id;
                            const form = document.getElementById(formId);
                            if (form) {
                                form.submit();
                            } else {
                                console.error('Form not found:', formId);
                                Swal.fire('Error', 'Form tidak ditemukan. ID: ' + formId, 'error');
                            }
                        }
                    });
                }
            })
            .catch(err => {
                console.error('Fetch error:', err);
                Swal.fire({
                    title: 'Gagal Mengecek',
                    text: 'Terjadi kesalahan saat mengecek status rekening: ' + err.message,
                    icon: 'error',
                    confirmButtonColor: '#EC1C25'
                });
            });
    }
</script>
@endsection
