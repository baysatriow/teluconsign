@extends('layouts.seller')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Edit Produk</h1>
            <p class="text-sm text-gray-500 mt-2">Perbarui informasi produk anda agar tetap relevan.</p>
        </div>
        <a href="{{ route('shop.products.index') }}" class="group flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-telu-red transition-colors">
            <span class="p-1 rounded-full group-hover:bg-red-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </span>
            Kembali
        </a>
    </div>

    <!-- Main Card -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <form action="{{ route('shop.products.update', $product) }}" method="POST" enctype="multipart/form-data" id="editProductForm">
            @csrf
            @method('PUT')
            
            <!-- Hidden Status -->
            <input type="hidden" name="status_input" id="status_input" value="{{ $product->status->value }}">

            <div class="p-8 space-y-10">
                
                <!-- Section 1: Informasi Dasar -->
                <div>
                    <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3 mb-6 flex items-center gap-2">
                        <span class="w-1 h-6 bg-telu-red rounded-full"></span>
                        Informasi Dasar
                    </h3>
                    
                    <div class="space-y-6">
                        <!-- Nama Produk -->
                        <div>
                            <label class="block mb-2 text-sm font-semibold text-gray-900">Nama Produk <span class="text-red-500">*</span></label>
                            <input type="text" name="title" value="{{ old('title', $product->title) }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-3 transition-shadow placeholder-gray-400" 
                                required>
                        </div>

                        <!-- Kategori (Searchable) & Kondisi -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                             <!-- Custom Searchable Select -->
                             <div class="relative" x-data="{ open: false, search: '', selected: '', selectedId: '' }">
                                <label class="block mb-2 text-sm font-semibold text-gray-900">Kategori <span class="text-red-500">*</span></label>
                                
                                <input type="hidden" name="category_id" id="category_id_input" value="{{ $product->category_id }}" required>

                                <input type="text" id="category_search" value="{{ $product->category->name ?? '' }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-3 cursor-pointer"
                                    placeholder="Cari Kategori..." autocomplete="off">
                                
                                <div id="category_dropdown" class="absolute z-10 w-full bg-white rounded-lg shadow-lg border border-gray-200 mt-1 max-h-60 overflow-y-auto hidden">
                                    @foreach($categories as $cat)
                                    <div class="category-option p-3 hover:bg-red-50 cursor-pointer text-sm text-gray-700 transition-colors" 
                                         data-id="{{ $cat->category_id }}" 
                                         data-name="{{ $cat->name }}"
                                         onclick="selectCategory(this)">
                                        {{ $cat->name }}
                                    </div>
                                    @endforeach
                                    <div id="no_category_found" class="p-3 text-sm text-gray-500 text-center hidden">Kategori tidak ditemukan</div>
                                </div>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-semibold text-gray-900">Kondisi Barang <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="condition" value="used" class="peer sr-only" {{ old('condition', $product->condition) == 'used' ? 'checked' : '' }}>
                                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 text-center transition-all peer-checked:border-telu-red peer-checked:bg-red-50 peer-checked:text-telu-red hover:bg-gray-100">
                                            <span class="font-medium text-sm">Bekas</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="condition" value="new" class="peer sr-only" {{ old('condition', $product->condition) == 'new' ? 'checked' : '' }}>
                                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 text-center transition-all peer-checked:border-telu-red peer-checked:bg-red-50 peer-checked:text-telu-red hover:bg-gray-100">
                                            <span class="font-medium text-sm">Baru</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label class="block mb-2 text-sm font-semibold text-gray-900">Deskripsi Lengkap <span class="text-red-500">*</span></label>
                            <textarea name="description" rows="6" 
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-3 transition-shadow placeholder-gray-400" 
                                required>{{ old('description', $product->description) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Harga & Stok -->
                <div>
                     <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3 mb-6 flex items-center gap-2">
                        <span class="w-1 h-6 bg-telu-red rounded-full"></span>
                        Harga & Inventaris
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block mb-2 text-sm font-semibold text-gray-900">Harga (Rp) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 font-bold text-sm">Rp</span>
                                <input type="text" name="price" value="{{ old('price', number_format($product->price,0,',','.')) }}" 
                                    class="numeric-input bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full pl-10 p-3 font-mono" 
                                    required oninput="formatNumber(this)">
                            </div>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-semibold text-gray-900">Stok Barang <span class="text-red-500">*</span></label>
                             <input type="text" name="stock" value="{{ old('stock', number_format($product->stock,0,',','.')) }}" 
                                class="numeric-input bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-3 font-mono" 
                                required oninput="formatNumber(this)">
                        </div>
                         <div>
                            <label class="block mb-2 text-sm font-semibold text-gray-900">Berat (Gram) <span class="text-red-500">*</span></label>
                             <div class="relative">
                                <input type="text" name="weight" value="{{ old('weight', number_format($product->weight,0,',','.')) }}" 
                                    class="numeric-input bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-3 font-mono pr-12" 
                                    required oninput="formatNumber(this)">
                                <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 text-xs font-bold uppercase">gram</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Media -->
                <div>
                     <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3 mb-6 flex items-center gap-2">
                        <span class="w-1 h-6 bg-telu-red rounded-full"></span>
                        Foto Produk <span class="text-gray-400 font-normal text-xs ml-2">(Max 5 Foto)</span>
                    </h3>

                    <!-- File Input (Hidden) -->
                    <input id="hidden-file-input" type="file" class="hidden" accept="image/*" multiple>

                    <!-- Main Grid -->
                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-4" id="media-grid">
                        
                        <!-- Existing Images -->
                        @foreach($product->images->sortBy('sort_order') as $img)
                         <div class="relative group aspect-square rounded-xl overflow-hidden border border-gray-200 shadow-sm existing-image-card" id="existing-img-{{ $img->id }}">
                            <img src="{{ asset('storage/'.$img->url) }}" class="w-full h-full object-cover">
                            <button type="button" onclick="deleteExistingImage({{ $img->id }})" 
                                    class="absolute top-1 right-1 bg-white text-red-600 rounded-full p-1 shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                            @if($img->is_primary)
                                <span class="absolute bottom-0 left-0 right-0 bg-telu-red/90 text-white text-[10px] text-center py-0.5">UTAMA</span>
                            @endif
                        </div>
                        @endforeach

                        <!-- Upload Button -->
                        <div id="upload-button-card" class="aspect-square bg-gray-50 rounded-xl border-2 border-dashed border-gray-300 hover:border-[#EC1C25] hover:bg-red-50/30 transition-all cursor-pointer flex flex-col items-center justify-center group" onclick="document.getElementById('hidden-file-input').click()">
                            <div class="w-10 h-10 bg-white rounded-full shadow-sm flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-gray-400 group-hover:text-[#EC1C25]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </div>
                            <span class="text-xs font-medium text-gray-500 group-hover:text-[#EC1C25]">Tambah Foto</span>
                        </div>

                    </div>
                    <!-- Actual Files Container to be submitted -->
                    <div id="file-inputs-container"></div>
                </div>

            </div>

             <!-- Footer Actions -->
             <div class="px-8 py-5 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3">
                 @if($product->status->value !== 'archived')
                    <button type="button" onclick="submitForm('archived')" class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-gray-200 transition-all">
                        Arsip (Draft)
                    </button>
                 @endif
                 
                 <button type="button" onclick="submitForm('active')" class="text-white bg-[#EC1C25] hover:bg-[#c4161e] focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-8 py-2.5 text-center shadow-lg shadow-red-500/30 transition-all transform hover:-translate-y-0.5">
                    @if($product->status->value === 'archived') Terbitkan Sekarang @else Simpan Perubahan @endif
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // --- 1. Searchable Category Logic ---
    const searchInput = document.getElementById('category_search');
    const dropdown = document.getElementById('category_dropdown');
    const options = document.querySelectorAll('.category-option');
    const hiddenInput = document.getElementById('category_id_input');
    const noResult = document.getElementById('no_category_found');

    searchInput.addEventListener('focus', () => { dropdown.classList.remove('hidden'); });
    
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    searchInput.addEventListener('input', function() {
        const val = this.value.toLowerCase();
        let visibleCount = 0;
        options.forEach(opt => {
            if(opt.dataset.name.toLowerCase().includes(val)) {
                opt.style.display = 'block';
                visibleCount++;
            } else {
                opt.style.display = 'none';
            }
        });
        
        if(visibleCount === 0) noResult.classList.remove('hidden');
        else noResult.classList.add('hidden');
        
        dropdown.classList.remove('hidden');
    });

    function selectCategory(el) {
        searchInput.value = el.dataset.name;
        hiddenInput.value = el.dataset.id;
        dropdown.classList.add('hidden');
    }

    // --- 2. Number Formatting (1000 -> 1.000) ---
    function formatNumber(input) {
        let val = input.value.replace(/\D/g, '');
        if (val === '') {
            input.value = '';
            return;
        }
        input.value = new Intl.NumberFormat('id-ID').format(val);
    }

    // --- 3. Unified Image Grid Logic ---
     const fileInput = document.getElementById('hidden-file-input');
     const grid = document.getElementById('media-grid');
     const uploadBtnCard = document.getElementById('upload-button-card');
     const fileInputsContainer = document.getElementById('file-inputs-container');
     
     // Initialize Total Images with Existing count
     let totalImages = {{ $product->images->count() }}; 
     const MAX_IMAGES = 5;

     // Initial check in case it's full
     updateUploadBtnVisibility();

     fileInput.addEventListener('change', function(e) {
         if (this.files && this.files.length > 0) {
             
            if (totalImages + this.files.length > MAX_IMAGES) {
                 Swal.fire('Limit Tercapai', 'Maksimal 5 foto per produk.', 'warning');
                 this.value = '';
                 return;
            }

            Array.from(this.files).forEach((file, index) => {
                 const card = createPreviewCard(file);
                 grid.insertBefore(card, uploadBtnCard);

                 const hiddenInput = document.createElement('input');
                 hiddenInput.type = 'file';
                 hiddenInput.name = 'images[]';
                 hiddenInput.style.display = 'none';
                 
                 const dataTransfer = new DataTransfer();
                 dataTransfer.items.add(file);
                 hiddenInput.files = dataTransfer.files;
                 
                 card.dataset.inputId = 'file-input-' + Date.now() + '-' + index;
                 hiddenInput.id = card.dataset.inputId;
                 
                 fileInputsContainer.appendChild(hiddenInput);
                 
                 totalImages++;
                 updateUploadBtnVisibility();
             });
             
             this.value = ''; 
         }
     });

     function createPreviewCard(file) {
         const div = document.createElement('div');
         div.className = 'relative group aspect-square rounded-xl overflow-hidden border border-gray-200 shadow-sm bg-gray-100';
         
         const reader = new FileReader();
         reader.onload = (e) => {
             const img = document.createElement('img');
             img.src = e.target.result;
             img.className = 'w-full h-full object-cover';
             div.appendChild(img);
         };
         reader.readAsDataURL(file);

         const btn = document.createElement('button');
         btn.type = 'button';
         btn.className = 'absolute top-1 right-1 bg-white text-red-600 rounded-full p-1 shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50';
         btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
         btn.onclick = function() {
             const inputId = div.dataset.inputId;
             const inputToRemove = document.getElementById(inputId);
             if(inputToRemove) inputToRemove.remove();
             div.remove();
             totalImages--;
             updateUploadBtnVisibility();
         };

         div.appendChild(btn);
         return div;
     }

     function deleteExistingImage(id) {
        Swal.fire({
            title: 'Hapus foto ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/shop/product/image/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') {
                         document.getElementById(`existing-img-${id}`).remove();
                         totalImages--;
                         updateUploadBtnVisibility();
                         Swal.fire('Terhapus!', 'Foto berhasil dihapus.', 'success');
                    }
                });
            }
        });
    }

     function updateUploadBtnVisibility() {
         if (totalImages >= MAX_IMAGES) {
             uploadBtnCard.classList.add('hidden');
         } else {
             uploadBtnCard.classList.remove('hidden');
         }
     }

    function submitForm(status) {
        document.getElementById('status_input').value = status;
        document.getElementById('editProductForm').submit();
    }
</script>
@endsection
