@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-extrabold text-gray-900 mb-8 tracking-tight">Keranjang Belanja</h1>

    <form id="cart-form" action="{{ route('checkout.index') }}" method="GET">
        <div class="flex flex-col lg:flex-row gap-8">

            <!-- List Item Keranjang -->
            <div class="flex-grow space-y-6">
                @if($groupedItems->isEmpty())
                    <div class="bg-white border border-gray-100 rounded-2xl p-16 text-center shadow-sm">
                        <div class="w-32 h-32 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-16 h-16 text-[#EC1C25]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Keranjangmu masih kosong</h3>
                        <p class="text-gray-500 mb-8 max-w-md mx-auto">Sepertinya kamu belum menambahkan barang apapun. Yuk mulai eksplorasi koleksi terbaik kami!</p>
                        <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-8 py-3 bg-[#EC1C25] hover:bg-[#c4161e] text-white font-bold rounded-xl transition-all shadow-lg shadow-red-500/30 transform hover:-translate-y-1">
                            Mulai Belanja
                        </a>
                    </div>
                @else
                    <!-- Loop per Toko -->
                    @foreach($groupedItems as $sellerId => $items)
                        @php
                            $seller = $items->first()->product->seller;
                        @endphp
                        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden store-group transition-shadow hover:shadow-md" id="store-{{ $sellerId }}">
                            <!-- Header Toko -->
                            <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-100 flex justify-between items-center backdrop-blur-sm">
                                <div class="flex items-center gap-4">
                                    <input type="checkbox" class="w-5 h-5 text-[#EC1C25] bg-white border-gray-300 rounded focus:ring-[#EC1C25] focus:ring-offset-0 cursor-pointer store-checkbox transition-all" data-seller="{{ $sellerId }}">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-white border border-gray-200 overflow-hidden shadow-sm">
                                            <img src="{{ $seller->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($seller->name).'&background=random' }}" class="w-full h-full object-cover">
                                        </div>
                                        <a href="{{ route('shop.show', $sellerId) }}" class="font-bold text-gray-900 hover:text-[#EC1C25] transition-colors flex items-center gap-1">
                                            {{ $seller->name }}
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </a>
                                    </div>
                                </div>
                                <!-- Hapus Toko Button -->
                                <button type="button" onclick="confirmDeleteStore('{{ $sellerId }}')" class="text-xs text-gray-400 hover:text-red-500 font-medium transition-colors flex items-center gap-1 group">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    <span class="group-hover:underline">Hapus</span>
                                </button>
                            </div>

                            <!-- List Produk -->
                            <div class="divide-y divide-gray-50">
                                @foreach($items as $item)
                                    <div class="p-6 flex flex-col sm:flex-row items-center gap-6 hover:bg-gray-50/30 transition-colors item-row group relative" data-id="{{ $item->cart_item_id }}" data-price="{{ $item->unit_price }}">
                                        <!-- Checkbox Item -->
                                        <input type="checkbox" name="selected_items[]" value="{{ $item->cart_item_id }}" data-seller="{{ $sellerId }}" data-product-id="{{ $item->product_id }}" class="w-5 h-5 text-[#EC1C25] bg-gray-100 border-gray-300 rounded focus:ring-[#EC1C25] focus:ring-offset-0 cursor-pointer item-checkbox seller-{{ $sellerId }}">

                                        <!-- Gambar Produk -->
                                        <div class="w-24 h-24 rounded-xl bg-gray-100 border border-gray-200 overflow-hidden flex-shrink-0 relative">
                                            <img src="{{ $item->product->main_image ? asset('storage/'.$item->product->main_image) : 'https://placehold.co/200' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                            @if($item->product->stock <= 5)
                                                <span class="absolute bottom-0 left-0 right-0 bg-red-500/90 text-white text-[10px] font-bold text-center py-0.5">Sisa {{ $item->product->stock }}</span>
                                            @endif
                                        </div>

                                        <!-- Info Produk -->
                                        <div class="flex-grow text-center sm:text-left w-full sm:w-auto">
                                            <a href="{{ route('product.show', $item->product_id) }}" class="text-lg font-bold text-gray-900 hover:text-[#EC1C25] transition-colors line-clamp-2 leading-tight">
                                                {{ $item->product->title }}
                                            </a>
                                            <div class="flex items-center justify-center sm:justify-start gap-2 mt-2">
                                                 <span class="px-2 py-0.5 rounded-md bg-gray-100 text-xs font-bold text-gray-500 uppercase">{{ $item->product->condition == 'new' ? 'Baru' : 'Bekas' }}</span>
                                                 <span class="text-sm font-semibold text-gray-500">Stok: {{ $item->product->stock }}</span>
                                            </div>
                                            <p class="text-xl font-extrabold text-[#EC1C25] mt-2">Rp{{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                        </div>

                                        <!-- Aksi Qty & Delete -->
                                        <div class="flex flex-col items-center sm:items-end gap-3 w-full sm:w-auto">
                                            <div class="flex items-center gap-1 bg-white border border-gray-200 rounded-xl p-1 shadow-sm">
                                                <button type="button" onclick="updateQty({{ $item->cart_item_id }}, -1, {{ $item->product->stock }})" class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-600 font-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"></path></svg>
                                                </button>
                                                <input type="text" id="qty-{{ $item->cart_item_id }}" value="{{ $item->quantity }}" class="w-10 text-center border-0 p-0 text-sm font-bold text-gray-900 focus:ring-0 item-qty bg-transparent" readonly data-max="{{ $item->product->stock }}">
                                                <button type="button" onclick="updateQty({{ $item->cart_item_id }}, 1, {{ $item->product->stock }})" class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-600 font-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed" @if($item->quantity >= $item->product->stock) disabled @endif>
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                                                </button>
                                            </div>

                                            <div class="flex items-center gap-4">
                                                <span class="text-sm font-medium text-gray-500">
                                                    Total: <span class="font-bold text-gray-900 item-subtotal" id="subtotal-{{ $item->cart_item_id }}">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                                </span>
                                                <button type="button" onclick="confirmDeleteItem('{{ $item->cart_item_id }}')" class="p-2 text-gray-300 hover:text-red-500 transition-colors rounded-full hover:bg-red-50" title="Hapus Item">
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
                <div class="bg-white border border-gray-100 rounded-2xl shadow-lg shadow-gray-200/50 p-6 sticky top-24">
                    <h3 class="text-lg font-extrabold text-gray-900 mb-6">Ringkasan Belanja</h3>

                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500 font-medium">Total Harga (<span id="total-selected-count" class="font-bold text-gray-900">0</span> barang)</span>
                            <span class="font-bold text-gray-900" id="grand-total">Rp0</span>
                        </div>
                         <!-- Promo Code (Visual Only for now) -->
                        <div class="flex gap-2">
                            <input type="text" placeholder="Kode Promo" class="flex-grow px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-[#EC1C25]">
                            <button type="button" class="px-4 py-2 bg-gray-200 text-gray-500 font-bold rounded-lg text-xs hover:bg-gray-300 transition-colors">Pakai</button>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-4 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-base font-bold text-gray-900">Total Tagihan</span>
                            <span class="text-2xl font-extrabold text-[#EC1C25]" id="final-total">Rp0</span>
                        </div>
                    </div>

                    <button type="submit" id="checkout-btn" class="w-full text-white bg-[#EC1C25] hover:bg-[#c4161e] focus:ring-4 focus:ring-red-200 font-bold rounded-xl text-base px-5 py-4 text-center shadow-lg shadow-red-500/30 transition-all transform hover:-translate-y-1 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none" disabled>
                        Beli Sekarang
                    </button>

                    <p class="text-xs text-center text-gray-400 mt-4 leading-relaxed">
                        Dengan membeli, Anda menyetujui <a href="#" class="text-[#EC1C25] hover:underline">Syarat & Ketentuan</a> kami.
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

    function updateQty(itemId, change, maxStock) {
        const input = document.getElementById('qty-' + itemId);
        let newQty = parseInt(input.value) + change;

        // Minimal 1
        if (newQty < 1) return;
        
        // Auto-adjust if exceeds max stock
        if (newQty > maxStock) {
            newQty = maxStock;
            Swal.fire({
                icon: 'warning', 
                title: 'Maksimal Stock',
                text: `Quantity telah disesuaikan ke maksimal stock: ${maxStock}`,
                toast: true,
                position: 'top-end',
                timer: 3000,
                showConfirmButton: false,
                timerProgressBar: true
            });
        }

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

                // Update button state
                const plusBtn = input.nextElementSibling;
                if (newQty >= maxStock) {
                    plusBtn.disabled = true;
                    plusBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    plusBtn.disabled = false;
                    plusBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }

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

    // Init Recalculate on Load
    document.addEventListener('DOMContentLoaded', function() {
        recalculateTotal();

        // Auto-select item from query param (Buy Now)
        const urlParams = new URLSearchParams(window.location.search);
        const selectedProductId = urlParams.get('selected_product');
        
        if(selectedProductId) {
            const checkbox = document.querySelector(`.item-checkbox[data-product-id="${selectedProductId}"]`);
            if(checkbox) {
                checkbox.checked = true;
                // Trigger change event to update parent store checkbox and totals
                checkbox.dispatchEvent(new Event('change'));
                
                // Scroll to item
                setTimeout(() => {
                    checkbox.closest('.item-row').scrollIntoView({ behavior: 'smooth', block: 'center' });
                    // Highlight effect
                    checkbox.closest('.item-row').classList.add('bg-red-50');
                    setTimeout(() => checkbox.closest('.item-row').classList.remove('bg-red-50'), 2000);
                }, 500);
            }
        }
    });

</script>
@endsection
