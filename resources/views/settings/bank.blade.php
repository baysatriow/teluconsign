@extends('layouts.settings')

@section('settings_content')

@php
$bankData = [
    'BCA' => [
        'gradient' => 'linear-gradient(135deg, #005CAA 0%, #002E5D 100%)',
        'accent'   => '#7AB4FF'
    ],
    'BRI' => [
        'gradient' => 'linear-gradient(135deg, #0E4C92 0%, #062D5C 100%)',
        'accent'   => '#85A8FF'
    ],
    'MANDIRI' => [
        'gradient' => 'linear-gradient(135deg, #003791 0%, #001E54 100%)',
        'accent'   => '#FFD13B'
    ],
    'BNI' => [
        'gradient' => 'linear-gradient(135deg, #F45A00 0%, #C14700 100%)',
        'accent'   => '#FFB38A'
    ],
    'JAGO' => [
        'gradient' => 'linear-gradient(135deg, #F9B000 0%, #D89000 100%)',
        'accent'   => '#FFF2B3'
    ],
    'SEABANK' => [
        'gradient' => 'linear-gradient(135deg, #FF5B5B 0%, #D94444 100%)',
        'accent'   => '#FFC7C7'
    ],
    'BSI' => [
        'gradient' => 'linear-gradient(135deg, #1A8D55 0%, #0F5D36 100%)',
        'accent'   => '#A8FFD7'
    ],
    'CIMB' => [
        'gradient' => 'linear-gradient(135deg, #A0000F 0%, #6A000A 100%)',
        'accent'   => '#FF9BAA'
    ],
];
@endphp


<div class="mb-8">
    <h1 class="text-2xl font-bold text-[var(--tc-text-main)]">Rekening Bank</h1>
    <p class="text-gray-500">Rekening untuk pencairan dana hasil penjualan Anda.</p>
</div>


{{-- ============================================================
     FORM TAMBAH REKENING
============================================================= --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 mb-10">

    <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-width="2" d="M12 6v12M6 12h12"></path>
        </svg>
        Tambah Rekening Baru
    </h3>

    <form action="{{ route('bank.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">

            {{-- BANK NAME --}}
            <div class="md:col-span-3 flex flex-col justify-start">
                <label class="text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">
                    Nama Bank
                </label>
                <select name="bank_name" id="bankSelect" required
                        class="w-full rounded-lg bg-gray-50 border p-2.5 text-sm cursor-pointer">
                    <option value="" disabled selected>Pilih Bank...</option>
                    @foreach($bankData as $bank => $val)
                        <option value="{{ $bank }}">{{ $bank }}</option>
                    @endforeach
                </select>
            </div>

            {{-- NOMOR REKENING --}}
            <div class="md:col-span-4 flex flex-col justify-start">
                <label class="text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">
                    Nomor Rekening
                </label>

                <input type="text" id="accountNo" name="account_no"
                       placeholder="Masukkan nomor rekening"
                       required
                       class="w-full rounded-lg bg-gray-50 border p-2.5 text-sm"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">

                <small id="accountInfo"
                       class="text-xs text-gray-500 mt-1"
                       style="min-height: 18px; display:block;"></small>
            </div>

            {{-- ATAS NAMA --}}
            <div class="md:col-span-3 flex flex-col justify-start">
                <label class="text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">
                    Atas Nama
                </label>
                <input type="text" name="account_name"
                       required
                       oninput="this.value = this.value.replace(/[^a-zA-Z\s\.]/g,'')"
                       class="w-full rounded-lg bg-gray-50 border p-2.5 text-sm">
            </div>

            {{-- BUTTON --}}
            <div class="md:col-span-2 flex items-end">
                <button type="submit"
                        class="w-full bg-[var(--tc-btn-bg)] hover:bg-[var(--tc-btn-bg-hover)] text-white py-2.5 rounded-lg text-sm font-bold shadow-md">
                    Simpan
                </button>
            </div>

        </div>
    </form>
</div>


{{-- ============================================================
     LIST REKENING
============================================================= --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

@forelse($banks as $bank)

    @php $style = $bankData[$bank->bank_name]; @endphp

    <div class="relative h-56 rounded-2xl p-6 shadow-xl overflow-hidden text-white flex flex-col justify-between
                transform transition-all hover:-translate-y-1 hover:shadow-2xl hover:scale-[1.015]"
         style="background: {{ $style['gradient'] }};">

        {{-- TOP --}}
        <div class="flex justify-between items-start">
            <h3 class="text-xl font-extrabold tracking-wide">
                {{ strtoupper($bank->bank_name) }}
            </h3>

            @if($bank->is_default)
                <span class="text-[10px] px-2 py-1 bg-white/30 rounded backdrop-blur-sm">
                    UTAMA
                </span>
            @endif
        </div>

        {{-- ACCOUNT NUMBER --}}
        <div class="mt-4">
            <p class="font-mono text-2xl tracking-[0.15em] drop-shadow-lg">
                {{ implode(' ', str_split($bank->account_no, 4)) }}
            </p>
        </div>

        {{-- FOOTER --}}
        <div class="flex justify-between items-end">

            <div>
                <p class="text-[10px] opacity-80 uppercase">Pemilik Rekening</p>
                <p class="font-semibold tracking-wide">{{ $bank->account_name }}</p>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="flex gap-2">

                {{-- EDIT --}}
                <button onclick="openEditBank(@json($bank))"
                        class="p-2 rounded-lg shadow-md transition-all"
                        style="background: {{ $style['accent'] }}20; color: {{ $style['accent'] }};">
                    ✏️
                </button>

                {{-- SET DEFAULT --}}
                @if(!$bank->is_default)
                <a href="{{ route('bank.setDefault', $bank->bank_account_id) }}"
                   class="p-2 rounded-lg shadow-md transition-all"
                   style="background: {{ $style['accent'] }}20; color: {{ $style['accent'] }};">
                    ⭐
                </a>

                {{-- DELETE --}}
                <form action="{{ route('bank.destroy', $bank->bank_account_id) }}"
                      method="POST" onsubmit="return confirm('Hapus rekening ini?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="p-2 rounded-lg shadow-md transition-all"
                            style="background: {{ $style['accent'] }}20; color: {{ $style['accent'] }};">
                        🗑
                    </button>
                </form>
                @endif

            </div>
        </div>

    </div>

@empty
    <div class="col-span-2 text-center py-16 bg-white rounded-xl border-2 border-dashed text-gray-400">
        Belum ada rekening bank.
    </div>
@endforelse

</div>


{{-- ============================================================
     EDIT MODAL
============================================================= --}}
<div id="editBankModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-white rounded-xl p-6 shadow-xl w-full max-w-md">

            <h3 class="text-lg font-bold mb-4">Edit Rekening</h3>

            <form id="editBankForm" method="POST" class="space-y-4">
                @csrf @method('PUT')

                <div>
                    <label class="text-xs font-bold mb-1 block">Nama Bank</label>
                    <select id="edit_bank_name" name="bank_name"
                            class="w-full bg-gray-50 border p-2.5 rounded-lg text-sm">
                        @foreach($bankData as $bankName => $v)
                            <option value="{{ $bankName }}">{{ $bankName }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-bold mb-1 block">Nomor Rekening</label>
                    <input type="text" id="edit_bank_no" name="account_no"
                           class="w-full bg-gray-50 border p-2.5 rounded-lg text-sm"
                           oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                </div>

                <div>
                    <label class="text-xs font-bold mb-1 block">Atas Nama</label>
                    <input type="text" id="edit_bank_an" name="account_name"
                           class="w-full bg-gray-50 border p-2.5 rounded-lg text-sm"
                           oninput="this.value=this.value.replace(/[^a-zA-Z\s\.]/g,'')">
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeEditBank()"
                            class="px-4 py-2 bg-gray-200 rounded-lg">Batal</button>

                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg">
                        Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>


{{-- ============================================================
     JAVASCRIPT
============================================================= --}}
<script>
const rules = {
    BCA: {min:10,max:16},
    BRI: {min:10,max:15},
    MANDIRI:{min:13,max:16},
    BNI:{min:10,max:10},
    JAGO:{min:15,max:15},
    SEABANK:{min:16,max:16},
    BSI:{min:10,max:16},
    CIMB:{min:10,max:13},
};

document.getElementById("bankSelect").addEventListener("change", function() {
    let bank = this.value;
    let rule = rules[bank];
    document.getElementById("accountInfo").innerText =
        `Harus ${rule.min} - ${rule.max} digit`;
});

function openEditBank(data) {
    document.getElementById("editBankForm").action = `/bank-accounts/${data.bank_account_id}`;
    document.getElementById("edit_bank_name").value = data.bank_name;
    document.getElementById("edit_bank_no").value = data.account_no;
    document.getElementById("edit_bank_an").value = data.account_name;

    document.getElementById("editBankModal").classList.remove("hidden");
}

function closeEditBank() {
    document.getElementById("editBankModal").classList.add("hidden");
}
</script>

@endsection
