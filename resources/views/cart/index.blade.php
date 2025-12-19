@extends('layouts.app')

@section('content')
<div class="max-w-screen-xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Keranjang Belanja</h1>

    <form id="cart-form" action="{{ route('checkout.index') }}" method="GET">
        <div class="flex flex-col lg:flex-row gap-8">

            <!-- List Item Keranjang -->
            <div class="flex-grow space-y-6">
                @if($groupedItems->isEmpty())
                    <div class="bg-white border border-gray-200 rounded-xl p-12 text-center">
                        <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 00-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <h3 class="text-lg font-medium text-gray-900">Keranjangmu masih kosong</h3>
                        <p class="text-gray-500 mb-6">Yuk mulai belanja dan temukan barang impianmu!</p>
                        <a href="{{ route('home') }}" class="text-white bg-[#EC1C25] hover:bg-[#c4161e] focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-6 py-3 transition-colors">
                            Mulai Belanja
                        </a>
                    </div>
                @else
                    <!-- Loop per Toko -->
                    @foreach($groupedItems as $sellerId => $items)
                        @php
                            $seller = $items->first()->product->seller;
                        @endphp
                        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden store-group" id="store-{{ $sellerId }}">
                            <!-- Header Toko -->
                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" class="w-4 h-4 text-[#EC1C25] bg-gray-100 border-gray-300 rounded focus:ring-[#EC1C25] store-checkbox" data-seller="{{ $sellerId }}">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-gray-200 overflow-hidden">
                                            <img src="{{ $seller->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($seller->name) }}" class="w-full h-full object-cover">
                                        </div>
                                        <span class="font-bold text-gray-800">{{ $seller->name }}</span>
                                    </div>
                                </div>
                                <!-- Hapus Toko Button -->
                                <button type="button" onclick="confirmDeleteStore('{{ $sellerId }}')" class="text-xs text-red-500 hover:text-red-700 font-medium hover:underline">Hapus Toko</button>
                            </div>

                            <!-- List Produk -->
                            <div class="divide-y divide-gray-100">
                                @foreach($items as $item)
                                    <div class="p-6 flex flex-col sm:flex-row items-start sm:items-center gap-4 hover:bg-gray-50/50 transition-colors item-row" data-id="{{ $item->cart_item_id }}" data-price="{{ $item->unit_price }}">
                                        <!-- Checkbox Item -->
                                        <!-- Name array 'selected_items[]' agar bisa dikirim ke checkout -->
                                        <input type="checkbox" name="selected_items[]" value="{{ $item->cart_item_id }}" data-seller="{{ $sellerId }}" class="w-4 h-4 text-[#EC1C25] bg-gray-100 border-gray-300 rounded focus:ring-[#EC1C25] item-checkbox seller-{{ $sellerId }}">

                                        <!-- Gambar Produk -->
                                        <div class="w-20 h-20 rounded-lg bg-gray-100 border border-gray-200 overflow-hidden flex-shrink-0">
                                            <img src="{{ $item->product->main_image ? asset('storage/'.$item->product->main_image) : 'https://placehold.co/100' }}" class="w-full h-full object-cover">
                                        </div>

                                        <!-- Info Produk -->
                                        <div class="flex-grow">
                                            <a href="{{ route('product.show', $item->product_id) }}" class="text-base font-medium text-gray-900 hover:text-[#EC1C25] line-clamp-2">
                                                {{ $item->product->title }}
                                            </a>
                                            <p class="text-sm font-bold text-gray-900 mt-1">Rp{{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                            <p class="text-xs text-gray-500 mt-1">Stok: {{ $item->product->stock }}</p>
                                        </div>

                                        <!-- Aksi Qty & Delete -->
                                        <div class="flex flex-col items-end gap-3">
                                            <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                                                <button type="button" onclick="updateQty({{ $item->cart_item_id }}, -1)" class="px-3 py-1 bg-gray-50 hover:bg-gray-200 text-gray-600 transition-colors">-</button>
                                                <input type="text" id="qty-{{ $item->cart_item_id }}" value="{{ $item->quantity }}" class="w-12 text-center border-0 py-1 text-sm text-gray-900 focus:ring-0 item-qty" readonly>
                                                <button type="button" onclick="updateQty({{ $item->cart_item_id }}, 1)" class="px-3 py-1 bg-gray-50 hover:bg-gray-200 text-gray-600 transition-colors">+</button>
                                            </div>

                                            <div class="flex items-center gap-3">
                                                <span class="text-sm font-bold text-[#EC1C25] item-subtotal" id="subtotal-{{ $item->cart_item_id }}">
                                                    Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                                </span>
                                                <button type="button" onclick="confirmDeleteItem('{{ $item->cart_item_id }}')" class="text-gray-400 hover:text-red-500 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Ringkasan Belanja (Sticky) -->
            <div class="lg:w-96 flex-shrink-0">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 sticky top-24">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Ringkasan Belanja</h3>

                    <div class="flex justify-between items-center mb-2 text-sm">
                        <span class="text-gray-600">Total Harga (<span id="total-selected-count">0</span> barang)</span>
                        <span class="font-bold text-gray-900" id="grand-total">Rp0</span>
                    </div>

                    <hr class="my-4 border-gray-100">

                    <div class="flex justify-between items-center mb-6">
                        <span class="text-base font-bold text-gray-900">Total Tagihan</span>
                        <span class="text-xl font-bold text-[#EC1C25]" id="final-total">Rp0</span>
                    </div>

                    <button type="submit" id="checkout-btn" class="w-full text-white bg-[#EC1C25] hover:bg-[#c4161e] focus:ring-4 focus:ring-red-300 font-bold rounded-lg text-sm px-5 py-3 text-center shadow-lg transition-all transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        Beli Sekarang
                    </button>

                    <p class="text-xs text-gray-400 mt-4 text-center">
                        Pastikan barang yang dipilih sudah benar.
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Form Hidden untuk Delete (Agar aman CSRF) -->
<form id="delete-item-form" method="POST" style="display: none;">
    @csrf @method('DELETE')
</form>

<form id="delete-store-form" method="POST" style="display: none;">
    @csrf @method('DELETE')
</form>


<script>
    // --- 1. LOGIKA CHECKBOX ---

    // Checkbox Toko (Parent)
    document.querySelectorAll('.store-checkbox').forEach(storeCheck => {
        storeCheck.addEventListener('change', function() {
            const sellerId = this.dataset.seller;
            // Use attribute selector for more robustness
            const itemChecks = document.querySelectorAll(`.item-checkbox[data-seller="${sellerId}"]`);

            itemChecks.forEach(itemCheck => {
                itemCheck.checked = this.checked;
            });

            recalculateTotal();
        });
    });

    // Checkbox Item (Child)
    document.querySelectorAll('.item-checkbox').forEach(itemCheck => {
        itemCheck.addEventListener('change', function() {
            const sellerId = this.dataset.seller;
            if (!sellerId) return; // safety check

            const storeCheck = document.querySelector(`.store-checkbox[data-seller="${sellerId}"]`);
            const allItemsInStore = document.querySelectorAll(`.item-checkbox[data-seller="${sellerId}"]`);

            // Cek apakah semua item di toko ini dicentang
            const allChecked = Array.from(allItemsInStore).every(c => c.checked);
            const someChecked = Array.from(allItemsInStore).some(c => c.checked);

            if (storeCheck) {
                storeCheck.checked = allChecked;
                storeCheck.indeterminate = someChecked && !allChecked; // Efek visual (opsional)
            }

            recalculateTotal();
        });
    });


    // --- 2. LOGIKA PERHITUNGAN TOTAL ---

    function recalculateTotal() {
        let total = 0;
        let count = 0;
        const selectedStores = new Set();

        document.querySelectorAll('.item-checkbox:checked').forEach(checkbox => {
            const row = checkbox.closest('.item-row');
            const qty = parseInt(row.querySelector('.item-qty').value);
            const price = parseFloat(row.dataset.price);

            total += qty * price;
            count += qty;

            // Cek toko (validasi max 20 toko)
            const sellerId = checkbox.dataset.seller;
            if (sellerId) {
                selectedStores.add(sellerId);
            }
        });

        // Update UI Text
        document.getElementById('grand-total').innerText = 'Rp' + new Intl.NumberFormat('id-ID').format(total);
        document.getElementById('final-total').innerText = 'Rp' + new Intl.NumberFormat('id-ID').format(total);
        document.getElementById('total-selected-count').innerText = count;

        // Validasi Checkout Button
        const btn = document.getElementById('checkout-btn');
        if (count > 0) {
            if (selectedStores.size > 20) {
                btn.disabled = true;
                btn.innerText = 'Maksimal 20 Toko';
                btn.classList.add('bg-gray-400');
                btn.classList.remove('bg-[#EC1C25]');
            } else {
                btn.disabled = false;
                btn.innerText = `Beli (${count})`;
                btn.classList.remove('bg-gray-400');
                btn.classList.add('bg-[#EC1C25]');
            }
        } else {
            btn.disabled = true;
            btn.innerText = 'Pilih Barang';
            btn.classList.add('bg-gray-400');
            btn.classList.remove('bg-[#EC1C25]');
        }
    }


    // --- 3. LOGIKA UPDATE QTY (AJAX) ---

    function updateQty(itemId, change) {
        const input = document.getElementById('qty-' + itemId);
        let newQty = parseInt(input.value) + change;

        if (newQty < 1) return; // Minimal 1

        // Disable UI sementara
        input.disabled = true;

        fetch(`/cart/update/${itemId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ quantity: newQty })
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                input.value = newQty;
                // Update subtotal text di baris item
                document.getElementById('subtotal-' + itemId).innerText = 'Rp' + data.subtotal;

                // Recalculate Grand Total (karena qty berubah)
                recalculateTotal();
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: data.message, confirmButtonColor: '#EC1C25' });
            }
        })
        .catch(error => {
            console.error('Error:', error);
        })
        .finally(() => {
            input.disabled = false;
        });
    }


    // --- 4. LOGIKA DELETE ---

    function confirmDeleteItem(itemId) {
        Swal.fire({
            title: 'Hapus item ini?',
            text: "Barang akan dihapus dari keranjang.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('delete-item-form');
                form.action = `/cart/item/${itemId}`;
                form.submit();
            }
        });
    }

    function confirmDeleteStore(sellerId) {
        Swal.fire({
            title: 'Hapus semua item toko?',
            text: "Semua barang dari toko ini akan dihapus.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('delete-store-form');
                form.action = `/cart/store/${sellerId}`;
                form.submit();
            }
        });
    }

    // Init Recalculate on Load (jika browser menyimpan state checkbox saat refresh)
    document.addEventListener('DOMContentLoaded', recalculateTotal);

</script>
@endsection
