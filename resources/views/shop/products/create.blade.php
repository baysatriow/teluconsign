@extends('layouts.app')

@section('content')
<div class="max-w-screen-lg mx-auto px-4 py-8">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Produk Baru</h1>
            <p class="text-sm text-gray-500 mt-1">Lengkapi detail produk semenarik mungkin untuk memikat pembeli.</p>
        </div>
        <a href="{{ route('shop.index') }}" class="text-sm text-gray-500 hover:text-[#EC1C25] font-medium transition-colors">
            &larr; Kembali ke Dashboard
        </a>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

        <form action="{{ route('shop.products.store') }}" method="POST" enctype="multipart/form-data" id="createProductForm">
            @csrf

            <!-- Input Hidden untuk Status (Active/Archived) -->
            <input type="hidden" name="status_input" id="status_input" value="active">

            <div class="p-6 md:p-8 grid gap-8 md:grid-cols-2">
                <!-- Kiri: Info Dasar -->
                <div class="space-y-6">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Nama Produk <span class="text-red-500">*</span></label>
                        <input type="text" name="title" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5" placeholder="Contoh: Laptop Gaming ASUS ROG Bekas" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Kategori <span class="text-red-500">*</span></label>
                            <select name="category_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5 cursor-pointer" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->category_id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Kondisi <span class="text-red-500">*</span></label>
                            <select name="condition" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5 cursor-pointer" required>
                                <option value="used">Bekas (Preloved)</option>
                                <option value="new">Baru</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Harga (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="price" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5" placeholder="100000" required>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Berat (Gram) <span class="text-red-500">*</span></label>
                            <input type="number" name="weight" value="1000" min="1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5" required>
                        </div>
                         <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Stok <span class="text-red-500">*</span></label>
                            <input type="number" name="stock" value="1" min="1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5" required>
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Deskripsi Lengkap <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="6" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-[#EC1C25] focus:border-[#EC1C25]" placeholder="Jelaskan spesifikasi, kelengkapan, minus (jika ada), dan alasan jual..." required></textarea>
                    </div>
                </div>

                <!-- Kanan: Upload -->
                <div class="space-y-6">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Foto Produk <span class="text-red-500">*</span></label>
                        <p class="text-xs text-gray-500 mb-3">Upload minimal 1 foto. Foto pertama akan menjadi sampul utama.</p>

                        <div class="flex items-center justify-center w-full">
                            <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors relative overflow-hidden">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" viewBox="0 0 20 16"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/></svg>
                                    <p class="mb-1 text-sm text-gray-500 text-center"><span class="font-semibold">Klik untuk upload</span></p>
                                    <p class="text-xs text-gray-500 text-center">PNG, JPG, WEBP (Max. 2MB)</p>
                                </div>
                                <input id="dropzone-file" name="images[]" type="file" class="hidden" accept="image/*" multiple required onchange="previewImages(this)" />
                            </label>
                        </div>
                    </div>
                    <div id="image-preview-container" class="grid grid-cols-3 gap-3 hidden"></div>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3">

                <!-- Tombol Draft -->
                <button type="button" onclick="submitForm('archived')" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-gray-200 transition-colors">
                    Simpan Draft
                </button>

                <!-- Tombol Terbitkan -->
                <button type="button" onclick="submitForm('active')" id="btn-publish" class="text-white bg-[#EC1C25] hover:bg-[#c4161e] focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-8 py-2.5 text-center shadow-lg transition-all">
                    Terbitkan Produk
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Preview Logic (Sama seperti sebelumnya)
    function previewImages(input) {
        const container = document.getElementById('image-preview-container');
        container.innerHTML = '';
        if (input.files && input.files.length > 0) {
            if(input.files.length > 5) {
                Swal.fire({icon: 'warning', title: 'Terlalu Banyak', text: 'Maks 5 foto.', confirmButtonColor: '#EC1C25'});
                input.value = ''; container.classList.add('hidden'); return;
            }
            container.classList.remove('hidden');
            Array.from(input.files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative aspect-square rounded-lg overflow-hidden border border-gray-200 shadow-sm bg-gray-100';
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-full h-full object-cover';
                    if (index === 0) {
                        const badge = document.createElement('span');
                        badge.className = 'absolute top-1 left-1 bg-[#EC1C25] text-white text-[10px] font-bold px-2 py-0.5 rounded shadow-sm';
                        badge.innerText = 'UTAMA';
                        div.appendChild(badge);
                    }
                    div.appendChild(img);
                    container.appendChild(div);
                }
                reader.readAsDataURL(file);
            });
        } else { container.classList.add('hidden'); }
    }

    // Submit Handler (Active vs Draft)
    function submitForm(status) {
        // Set hidden input value
        document.getElementById('status_input').value = status;

        // Trigger submit event manually
        const form = document.getElementById('createProductForm');
        form.dispatchEvent(new Event('submit'));
    }

    // AJAX Handler
    document.getElementById('createProductForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const btn = document.getElementById('btn-publish');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerHTML = 'Memproses...';

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json().then(data => ({status: res.status, body: data})))
        .then(({status, body}) => {
            if (status === 200) {
                Swal.fire({
                    icon: 'success', title: 'Berhasil!', text: body.message, confirmButtonColor: '#EC1C25', timer: 2000
                }).then(() => { window.location.href = body.redirect_url; });
            } else if (status === 422) {
                let errorHtml = '<ul style="text-align: left; font-size: 0.9em;">';
                for (const [key, msgs] of Object.entries(body.errors)) {
                    msgs.forEach(msg => { errorHtml += `<li class="text-red-600 mb-1">• ${msg}</li>`; });
                }
                errorHtml += '</ul>';
                Swal.fire({icon: 'warning', title: 'Periksa Inputan', html: errorHtml, confirmButtonColor: '#EC1C25'});
            } else {
                throw new Error(body.message || 'Error server.');
            }
        })
        .catch(error => {
            Swal.fire({icon: 'error', title: 'Gagal', text: error.message, confirmButtonColor: '#EC1C25'});
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerText = originalText;
        });
    });
</script>
@endsection
