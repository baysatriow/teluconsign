@props(['action', 'method' => 'POST', 'product' => null, 'categories', 'conditions'])

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" id="productForm" class="bg-white p-8 rounded-xl shadow-sm border border-gray-200">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    {{-- SECTION 1: Upload Foto (Grid Layout) --}}
    <div class="mb-8">
        <label class="block text-sm font-bold text-gray-700 mb-2">Foto Produk <span class="text-red-500">*</span> <span class="text-xs font-normal text-gray-500">(Min 1, Max 5)</span></label>

        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            {{-- Tombol Upload --}}
            <label class="border-2 border-dashed border-gray-300 rounded-lg flex flex-col items-center justify-center cursor-pointer hover:bg-gray-50 h-32 transition-colors relative group">
                <svg class="w-8 h-8 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span class="text-xs text-gray-500 mt-1 font-medium group-hover:text-gray-700">Tambah Foto</span>
                {{-- Input file berbeda name-nya untuk Create vs Edit --}}
                <input type="file" name="{{ $product ? 'images_new[]' : 'images[]' }}" multiple accept="image/*" class="hidden" id="imageInput" onchange="handleImagePreview(this)">
            </label>

            {{-- Container Preview (Akan diisi JS) --}}
            <div id="previewContainer" class="contents">
                {{-- Jika Edit Mode, tampilkan gambar yang sudah ada --}}
                @if($product && $product->images)
                    @foreach($product->images as $img)
                        <div class="relative h-32 w-full rounded-lg overflow-hidden border border-gray-200 group existing-image-item" data-id="{{ $img->product_image_id }}">
                            <img src="{{ asset('storage/' . $img->url) }}" class="h-full w-full object-cover">
                            @if($img->is_primary)
                                <span class="absolute top-1 left-1 bg-green-500 text-white text-[10px] px-2 py-0.5 rounded shadow">Utama</span>
                            @endif
                            {{-- Tombol Hapus Gambar Lama --}}
                            <button type="button" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity" onclick="markImageForDeletion(this, {{ $img->product_image_id }})">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Input hidden untuk gambar yang akan dihapus (Edit Mode) --}}
        <div id="deletedImagesContainer"></div>
    </div>

    <hr class="border-gray-100 my-6">

    {{-- SECTION 2: Informasi Produk --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Nama Produk --}}
        <div class="col-span-2">
            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Produk <span class="text-red-500">*</span></label>
            <input type="text" name="title" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 p-2.5 text-sm" placeholder="Contoh: Sepatu Nike Air Jordan" value="{{ old('title', $product->title ?? '') }}" required>
        </div>

        {{-- Kategori --}}
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
            <div class="flex gap-2">
                <div class="relative w-full">
                    <select name="category_id" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 p-2.5 text-sm appearance-none" required>
                        <option value="" disabled {{ old('category_id', $product->category_id ?? '') ? '' : 'selected' }}>Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->category_id }}" {{ old('category_id', $product->category_id ?? '') == $cat->category_id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                    </div>
                </div>
                {{-- Tombol Quick Add Category --}}
                <button type="button" onclick="openCategoryModal()" class="bg-indigo-50 text-indigo-600 p-2.5 rounded-lg border border-indigo-100 hover:bg-indigo-100 transition-colors" title="Tambah Kategori Baru">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </button>
            </div>
        </div>

        {{-- Kondisi --}}
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Kondisi <span class="text-red-500">*</span></label>
            <select name="condition" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 p-2.5 text-sm">
                <option value="new" {{ old('condition', $product->condition->value ?? '') == 'new' ? 'selected' : '' }}>Baru</option>
                <option value="used" {{ old('condition', $product->condition->value ?? '') == 'used' ? 'selected' : '' }}>Bekas (Pre-loved)</option>
            </select>
        </div>

        {{-- Harga --}}
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Harga (Rp) <span class="text-red-500">*</span></label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-sm">Rp</span>
                <input type="number" name="price" class="w-full rounded-lg border-gray-300 pl-10 p-2.5 text-sm" placeholder="0" value="{{ old('price', isset($product->price) ? (int)$product->price : '') }}" required min="100">
            </div>
        </div>

        {{-- Stok --}}
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Stok <span class="text-red-500">*</span></label>
            <input type="number" name="stock" class="w-full rounded-lg border-gray-300 p-2.5 text-sm" value="{{ old('stock', $product->stock ?? 1) }}" min="1" required>
        </div>

        {{-- Lokasi --}}
        <div class="col-span-2">
            <label class="block text-sm font-bold text-gray-700 mb-1">Lokasi Pengiriman <span class="text-red-500">*</span></label>
            <input type="text" name="location" class="w-full rounded-lg border-gray-300 p-2.5 text-sm" placeholder="Kota Bandung" value="{{ old('location', $product->location ?? '') }}" required>
        </div>

        {{-- Deskripsi --}}
        <div class="col-span-2">
            <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi Produk <span class="text-red-500">*</span></label>
            <textarea name="description" rows="5" class="w-full rounded-lg border-gray-300 p-2.5 text-sm" placeholder="Jelaskan detail produk, minus (jika ada), dan kelengkapan..." required>{{ old('description', $product->description ?? '') }}</textarea>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="mt-8 flex items-center justify-end gap-4">
        <a href="{{ route('products.index') }}" class="text-gray-600 text-sm font-medium hover:underline">Batal</a>
        <button type="submit" class="bg-[var(--tc-btn-bg)] text-white px-8 py-3 rounded-lg text-sm font-bold shadow-lg hover:opacity-90 transition-all flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ $product ? 'Update Produk' : 'Tayangkan Sekarang' }}
        </button>
    </div>
</form>

{{-- MODAL QUICK ADD CATEGORY --}}
<div id="quickCategoryModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl w-full max-w-sm p-6 shadow-2xl transform transition-all">
        <h3 class="text-lg font-bold mb-4 text-gray-800">Tambah Kategori Baru</h3>
        <div class="space-y-4">
            <input type="text" id="newCategoryName" class="w-full rounded-lg border-gray-300 text-sm p-2.5" placeholder="Nama Kategori (mis: Elektronik)">
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeCategoryModal()" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Batal</button>
                <button type="button" onclick="saveNewCategory()" class="px-4 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Simpan</button>
            </div>
        </div>
    </div>
</div>

{{-- JAVASCRIPT --}}
<script>
    // 1. Image Preview & Validation
    function handleImagePreview(input) {
        const container = document.getElementById('previewContainer');
        // Hapus preview gambar *baru* sebelumnya, tapi pertahankan gambar *lama* (existing)
        const newPreviews = container.querySelectorAll('.new-preview-item');
        newPreviews.forEach(el => el.remove());

        // Hitung total gambar (lama yang belum dihapus + baru)
        const existingCount = container.querySelectorAll('.existing-image-item:not(.hidden)').length;
        const newCount = input.files.length;
        const total = existingCount + newCount;

        if (total > 5) {
            Swal.fire({
                icon: 'warning',
                title: 'Terlalu Banyak Foto',
                text: 'Maksimal total 5 foto produk.',
                confirmButtonColor: '#3d4c67'
            });
            input.value = ''; // Reset input
            return;
        }

        Array.from(input.files).forEach((file) => {
            if(file.size > 2 * 1024 * 1024) { // 2MB Check
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar',
                    text: `File ${file.name} melebihi 2MB.`,
                });
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative h-32 w-full rounded-lg overflow-hidden border border-gray-200 group new-preview-item';
                div.innerHTML = `
                    <img src="${e.target.result}" class="h-full w-full object-cover">
                    <span class="absolute top-1 right-1 bg-blue-500 text-white text-[10px] px-2 py-0.5 rounded shadow">Baru</span>
                `;
                container.appendChild(div);
            }
            reader.readAsDataURL(file);
        });
    }

    // 2. Mark Image for Deletion (Edit Mode)
    function markImageForDeletion(btn, id) {
        Swal.fire({
            title: 'Hapus foto ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Sembunyikan elemen visual
                const parentDiv = btn.closest('.existing-image-item');
                parentDiv.classList.add('hidden');

                // Tambahkan input hidden untuk dikirim ke controller
                const container = document.getElementById('deletedImagesContainer');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'deleted_images[]';
                input.value = id;
                container.appendChild(input);

                // Validasi jumlah minimal
                const visibleImages = document.querySelectorAll('.existing-image-item:not(.hidden)').length;
                const newImages = document.getElementById('imageInput').files.length;

                // Note: Validasi final tetap di server, ini hanya UX
            }
        })
    }

    // 3. Quick Category Logic
    function openCategoryModal() {
        document.getElementById('quickCategoryModal').classList.remove('hidden');
        document.getElementById('quickCategoryModal').classList.add('flex');
    }
    function closeCategoryModal() {
        document.getElementById('quickCategoryModal').classList.add('hidden');
        document.getElementById('quickCategoryModal').classList.remove('flex');
    }

    function saveNewCategory() {
        const name = document.getElementById('newCategoryName').value;
        if(!name) return;

        // AJAX Request ke Route Category Quick Store
        fetch('{{ route("categories.quickStore") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ name: name })
        })
        .then(response => response.json())
        .then(data => {
            if(data.category) {
                // Tambahkan ke dropdown dan pilih
                const select = document.querySelector('select[name="category_id"]');
                const option = new Option(data.category.name, data.category.category_id, true, true);
                select.add(option);
                closeCategoryModal();
                Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Kategori ditambahkan!', timer: 1500, showConfirmButton: false });
            }
        })
        .catch(error => {
            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan saat menyimpan kategori.' });
        });
    }
</script>
