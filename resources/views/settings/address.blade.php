@extends('layouts.settings')

@section('settings_content')

<div class="mb-8">
    <h1 class="text-2xl font-bold text-[var(--tc-text-main)]">Buku Alamat</h1>
    <p class="text-gray-500">Kelola alamat pengiriman untuk mempercepat proses checkout.</p>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

    {{-- ============================================================
        FORM TAMBAH ALAMAT
    ============================================================= --}}
    <div class="xl:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
            <h3 class="text-lg font-bold text-gray-900 mb-5 border-b pb-3">Tambah Alamat Baru</h3>

            <form action="{{ route('address.store') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Label --}}
                <div>
                    <label class="text-xs font-bold mb-1 uppercase block">Label Alamat</label>
                    <input type="text" name="label" required placeholder="Rumah, Kantor, Kost"
                           class="w-full rounded-lg bg-gray-50 border border-gray-300 p-2.5 text-sm">
                </div>

                {{-- Penerima --}}
                <div>
                    <label class="text-xs font-bold mb-1 uppercase block">Nama Penerima</label>
                    <input type="text" name="recipient" required
                           oninput="this.value=this.value.replace(/[^a-zA-Z\s\.]/g,'')"
                           class="w-full rounded-lg bg-gray-50 border border-gray-300 p-2.5 text-sm">
                </div>

                {{-- Nomor HP --}}
                <div>
                    <label class="text-xs font-bold mb-1 uppercase block">No. Handphone</label>
                    <div class="flex rounded-lg overflow-hidden border bg-gray-50 border-gray-300">
                        <span class="px-3 flex items-center bg-gray-100 text-gray-600 font-bold border-r text-sm">+62</span>
                        <input type="text" name="phone" id="phone" required
                               maxlength="13"
                               placeholder="81234567890"
                               oninput="formatPhone(this)"
                               class="flex-1 p-2.5 text-sm bg-gray-50 focus:outline-none">
                    </div>
                </div>

                {{-- Alamat --}}
                <div>
                    <label class="text-xs font-bold mb-1 uppercase block">Alamat Lengkap</label>
                    <textarea name="line1" rows="3" required
                              class="w-full rounded-lg bg-gray-50 border border-gray-300 p-2.5 text-sm"></textarea>
                </div>

                {{-- Provinsi & Kota --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-bold mb-1 uppercase block">Provinsi</label>
                        <select id="provinceSelect" name="province" required
                                class="w-full bg-gray-50 border border-gray-300 p-2.5 rounded-lg text-sm">
                            <option value="">Memuat...</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-bold mb-1 uppercase block">Kota / Kabupaten</label>
                        <select id="citySelect" name="city" required disabled
                                class="w-full bg-gray-50 border border-gray-300 p-2.5 rounded-lg text-sm">
                            <option value="">Pilih Provinsi...</option>
                        </select>
                    </div>
                </div>

                {{-- Kode Pos --}}
                <div>
                    <label class="text-xs font-bold mb-1 uppercase block">Kode Pos</label>
                    <input type="text" name="postal_code" maxlength="5" required
                           oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                           class="w-full rounded-lg bg-gray-50 border border-gray-300 p-2.5 text-sm">
                </div>

                <button type="submit"
                        class="w-full bg-[var(--tc-btn-bg)] hover:bg-[var(--tc-btn-bg-hover)] text-white py-3 rounded-lg text-sm font-bold shadow-md">
                    + Simpan Alamat
                </button>
            </form>
        </div>
    </div>

    {{-- ============================================================
        LIST ALAMAT
    ============================================================= --}}
    <div class="xl:col-span-2 space-y-4">

        <h3 class="text-lg font-bold text-gray-900 mb-2 flex items-center">
            Daftar Alamat Tersimpan
            <span class="ml-2 bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded-full">{{ $addresses->count() }}</span>
        </h3>

        @forelse($addresses as $addr)
        <div class="bg-white rounded-xl border p-6 relative shadow-sm
            {{ $addr->is_default ? 'border-indigo-500 ring-1 ring-indigo-500 shadow-md' : 'border-gray-200' }}">

            @if($addr->is_default)
                <span class="absolute top-0 right-0 mt-4 mr-4 bg-indigo-100 text-indigo-700 text-xs px-3 py-1 rounded-full">
                    ALAMAT UTAMA
                </span>
            @endif

            <div class="flex justify-between items-start">
                <div class="w-3/4 pr-4">
                    <h4 class="font-bold text-lg text-gray-800">{{ $addr->label }}</h4>
                    <p class="text-gray-900 font-medium">{{ $addr->recipient }}
                        <span class="text-gray-500">| +62{{ $addr->phone }}</span></p>

                    <div class="mt-3 p-3 bg-gray-50 rounded-lg border text-gray-600 text-sm">
                        {{ $addr->line1 }}<br>
                        <b>{{ $addr->city }}, {{ $addr->province }} {{ $addr->postal_code }}</b>
                    </div>
                </div>

                <div class="mt-8 flex flex-col gap-2">
                    <button onclick='openEditModal(@json($addr))'
                            class="px-3 py-2 text-sm bg-white border rounded-lg hover:bg-gray-50">Edit</button>

                    @if(!$addr->is_default)
                    <form action="{{ route('address.destroy', $addr->address_id) }}" method="POST"
                          onsubmit="return confirm('Hapus alamat ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-3 py-2 text-sm border rounded-lg text-red-600 hover:bg-red-50">
                            Hapus
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            @if(!$addr->is_default)
            <div class="mt-4 pt-4 border-t flex justify-end">
                <a href="{{ route('address.setDefault', $addr->address_id) }}"
                   class="text-sm text-indigo-600 font-semibold hover:underline">Jadikan Alamat Utama</a>
            </div>
            @endif
        </div>

        @empty
            <div class="py-16 text-center bg-white rounded-xl border-2 border-dashed text-gray-500">
                Belum ada alamat.
            </div>
        @endforelse
    </div>
</div>

{{-- ============================================================
    MODAL EDIT
============================================================= --}}
<div id="editModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 relative">

            <h3 class="text-lg font-bold mb-4">Edit Alamat</h3>

            <form id="editForm" method="POST" class="space-y-4">
                @csrf @method('PUT')

                {{-- Label --}}
                <div>
                    <label class="text-xs font-bold mb-1 block">Label</label>
                    <input id="edit_label" name="label"
                           class="w-full p-2.5 bg-gray-50 border rounded-lg text-sm">
                </div>

                {{-- Penerima --}}
                <div>
                    <label class="text-xs font-bold mb-1 block">Penerima</label>
                    <input id="edit_recipient" name="recipient"
                           oninput="this.value=this.value.replace(/[^a-zA-Z\s\.]/g,'')"
                           class="w-full p-2.5 bg-gray-50 border rounded-lg text-sm">
                </div>

                {{-- Nomor HP --}}
                <div>
                    <label class="text-xs font-bold mb-1 block">Nomor HP</label>
                    <div class="flex rounded-lg overflow-hidden border bg-gray-50">
                        <span class="px-3 flex items-center bg-gray-100 text-gray-600 font-bold border-r text-sm">+62</span>
                        <input id="edit_phone" name="phone"
                               maxlength="13"
                               placeholder="81234567890"
                               oninput="formatPhone(this)"
                               class="flex-1 p-2.5 text-sm bg-gray-50">
                    </div>
                </div>

                {{-- Alamat --}}
                <div>
                    <label class="text-xs font-bold mb-1 block">Alamat</label>
                    <textarea id="edit_line1" name="line1" rows="2"
                              class="w-full p-2.5 bg-gray-50 border rounded-lg text-sm"></textarea>
                </div>

                {{-- Provinsi & Kota --}}
                <div class="grid grid-cols-2 gap-3">
                    <select id="edit_province" name="province"
                            class="w-full p-2.5 bg-gray-50 border rounded-lg text-sm"></select>

                    <select id="edit_city" name="city"
                            class="w-full p-2.5 bg-gray-50 border rounded-lg text-sm"></select>
                </div>

                {{-- Kode pos --}}
                <input id="edit_postal_code" name="postal_code" maxlength="5"
                       oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                       class="w-full p-2.5 bg-gray-50 border rounded-lg text-sm">

                <div class="mt-6 flex justify-end gap-2">
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold">
                        Simpan
                    </button>

                    <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 bg-gray-200 text-gray-900 rounded-lg text-sm">
                        Batal
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- ============================================================
    SCRIPT JS
============================================================= --}}
<script>

/* ------------------------
   VALIDASI HP +62
------------------------- */
function formatPhone(input) {
    input.value = input.value.replace(/[^0-9]/g, '');

    if (input.value.length > 0 && !input.value.startsWith("8")) {
        input.setCustomValidity("Nomor harus dimulai dengan angka 8");
    } else if (input.value.length < 8 || input.value.length > 13) {
        input.setCustomValidity("Nomor harus 8-13 digit (contoh: 81234567890)");
    } else {
        input.setCustomValidity("");
    }
}

/* ------------------------
   LOAD PROVINSI
------------------------- */
async function loadProvinces(selectId) {
    const select = document.getElementById(selectId);
    select.innerHTML = `<option value="">Memuat...</option>`;

    const res = await fetch("https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json");
    const provs = await res.json();

    select.innerHTML = `<option value="">Pilih Provinsi...</option>`;
    provs.forEach(p => {
        select.innerHTML += `<option value="${p.name}" data-id="${p.id}">${p.name}</option>`;
    });
}

/* ------------------------
   LOAD KOTA
------------------------- */
async function loadCities(provSelectId, citySelectId) {
    const provSelect = document.getElementById(provSelectId);
    const citySelect = document.getElementById(citySelectId);

    const selected = provSelect.options[provSelect.selectedIndex];
    const provId = selected?.dataset.id;

    if (!provId) {
        citySelect.disabled = true;
        citySelect.innerHTML = `<option value="">Pilih Provinsi...</option>`;
        return;
    }

    const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provId}.json`);
    const cities = await res.json();

    citySelect.disabled = false;
    citySelect.innerHTML = `<option value="">Pilih Kota...</option>`;
    cities.forEach(c => {
        citySelect.innerHTML += `<option value="${c.name}">${c.name}</option>`;
    });
}

/* INIT ADD FORM */
loadProvinces("provinceSelect");
document.getElementById("provinceSelect").addEventListener("change", () => {
    loadCities("provinceSelect", "citySelect");
});

/* ------------------------
   OPEN EDIT MODAL
------------------------- */
async function openEditModal(data) {
    const modal = document.getElementById("editModal");
    const form = document.getElementById("editForm");

    form.action = `/addresses/${data.address_id}`;

    // Load provinsi
    await loadProvinces("edit_province");

    // Set provinsi terpilih
    const provSel = document.getElementById("edit_province");
    [...provSel.options].forEach(o => {
        if (o.value === data.province) o.selected = true;
    });

    // Load kota berdasarkan provinsi
    await loadCities("edit_province", "edit_city");

    // Set kota terpilih
    const citySel = document.getElementById("edit_city");
    [...citySel.options].forEach(o => {
        if (o.value === data.city) o.selected = true;
    });

    document.getElementById("edit_label").value = data.label;
    document.getElementById("edit_recipient").value = data.recipient;
    document.getElementById("edit_phone").value = data.phone;
    document.getElementById("edit_line1").value = data.line1;
    document.getElementById("edit_postal_code").value = data.postal_code;

    modal.classList.remove("hidden");
}

function closeEditModal() {
    document.getElementById("editModal").classList.add("hidden");
}

</script>

@endsection
