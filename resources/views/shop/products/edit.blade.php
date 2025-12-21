@extends('layouts.app')

@section('content')
<div class="max-w-screen-lg mx-auto px-4 py-8">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Produk</h1>
        <a href="{{ route('shop.index') }}" class="text-sm text-gray-500 hover:text-[#EC1C25]">&larr; Kembali</a>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

        <form action="{{ route('shop.products.update', $product->product_id) }}" method="POST" enctype="multipart/form-data" id="editProductForm">
            @csrf
            @method('PUT')

            <input type="hidden" name="status_input" id="status_input" value="{{ $product->status }}">

            <div class="p-6 md:p-8 grid gap-8 md:grid-cols-2">

                <!-- Info Produk -->
                <div class="space-y-6">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Nama Produk</label>
                        <input type="text" name="title" value="{{ $product->title }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Kategori</label>
                            <select name="category_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->category_id }}" {{ $product->category_id == $cat->category_id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Kondisi</label>
                            <select name="condition" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5" required>
                                <option value="used" {{ $product->condition == 'used' ? 'selected' : '' }}>Bekas</option>
                                <option value="new" {{ $product->condition == 'new' ? 'selected' : '' }}>Baru</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Harga (Rp)</label>
                            <input type="number" name="price" value="{{ (int)$product->price }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5" required>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Berat (Gram)</label>
                            <input type="number" name="weight" value="{{ $product->weight }}" min="1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5" required>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Stok</label>
                            <input type="number" name="stock" value="{{ $product->stock }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#EC1C25] focus:border-[#EC1C25] block w-full p-2.5" required>
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Deskripsi</label>
                        <textarea name="description" rows="6" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-[#EC1C25] focus:border-[#EC1C25]" required>{{ $product->description }}</textarea>
                    </div>
                </div>

                <!-- Foto Produk -->
                <div class="space-y-6">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Foto Saat Ini</label>
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            @foreach($product->images as $img)
                            <div class="relative aspect-square rounded-lg overflow-hidden border border-gray-200 group" id="img-card-{{ $img->product_image_id }}">
                                <img src="{{ asset('storage/'.$img->url) }}" class="w-full h-full object-cover">
                                <!-- Tombol Hapus Foto -->
                                <button type="button" onclick="deleteImage({{ $img->product_image_id }})" class="absolute top-1 right-1 bg-red-600 text-white p-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-700" title="Hapus Foto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                                @if($img->url === $product->main_image)
                                    <span class="absolute bottom-1 left-1 bg-gray-900/70 text-white text-[10px] px-2 py-0.5 rounded">UTAMA</span>
                                @endif
                            </div>
                            @endforeach
                        </div>

                        <label class="block mb-2 text-sm font-medium text-gray-900 mt-6">Tambah Foto Baru</label>
                        <input id="new-images" name="images[]" type="file" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" multiple accept="image/*">
                        <p class="mt-1 text-xs text-gray-500">Maksimal upload 5 foto sekaligus.</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <!-- Footer Buttons -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3">

                @if($product->status->value === 'active')
                    <!-- Jika sedang AKTIF -->
                    <button type="button" onclick="submitForm('archived')" class="px-5 py-2.5 text-sm font-medium text-red-600 bg-white border border-red-200 rounded-lg hover:bg-red-50 focus:ring-4 focus:outline-none focus:ring-red-100 transition-colors">
                        Simpan ke Draft (Arsip)
                    </button>
                    <button type="button" onclick="submitForm('active')" id="btn-update" class="text-white bg-[#EC1C25] hover:bg-[#c4161e] font-medium rounded-lg text-sm px-8 py-2.5 shadow-lg">
                        Update Produk
                    </button>
                @else
                    <!-- Jika sedang DRAFT / LAINNYA -->
                    <button type="button" onclick="submitForm('archived')" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-gray-200 transition-colors">
                        Simpan Draft
                    </button>
                    <button type="button" onclick="submitForm('active')" id="btn-update" class="text-white bg-green-600 hover:bg-green-700 hover:text-white border border-green-600 font-medium rounded-lg text-sm px-8 py-2.5 shadow-md hover:shadow-lg transition-all">
                        Terbitkan Produk
                    </button>
                @endif

            </div>
        </form>
    </div>
</div>

<script>
    function deleteImage(id) {
        Swal.fire({
            title: 'Hapus foto ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/myshop/products/image/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') {
                        document.getElementById(`img-card-${id}`).remove();
                        Swal.fire('Terhapus!', 'Foto berhasil dihapus.', 'success');
                    }
                });
            }
        });
    }

    function submitForm(status) {
        document.getElementById('status_input').value = status;
        const form = document.getElementById('editProductForm');
        const btn = document.getElementById('btn-update');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerText = 'Menyimpan...';

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST', // Method POST tapi dengan _method PUT di form
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json().then(data => ({status: res.status, body: data})))
        .then(({status, body}) => {
            if (status === 200) {
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: body.message, confirmButtonColor: '#EC1C25' })
                .then(() => window.location.href = body.redirect_url);
            } else {
                throw new Error(body.message || 'Gagal update.');
            }
        })
        .catch(err => {
            Swal.fire({icon: 'error', title: 'Error', text: err.message});
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerText = originalText;
        });
    }
</script>
@endsection
