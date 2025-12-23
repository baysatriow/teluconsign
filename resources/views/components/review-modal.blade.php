<!-- Review Modal -->
<div id="reviewModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeReviewModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                            Beri Ulasan Produk
                        </h3>
                        <div class="mt-4">
                            <!-- Product Info -->
                            <div class="flex gap-4 mb-6 p-4 bg-gray-50 rounded-xl border border-gray-100">
                                <img id="review-product-image" src="" class="w-16 h-16 object-cover rounded-lg bg-gray-200">
                                <div>
                                    <h4 id="review-product-title" class="text-sm font-bold text-gray-900 line-clamp-2"></h4>
                                    <p id="review-product-price" class="text-xs text-gray-500 mt-1"></p>
                                </div>
                            </div>

                            <form id="reviewForm" onsubmit="submitReview(event)">
                                @csrf
                                <input type="hidden" name="product_id" id="review-product-id">
                                <input type="hidden" name="order_id" id="review-order-id">
                                
                                <!-- Rating Stars -->
                                <div class="mb-6 text-center">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Berikan Rating</label>
                                    <div class="flex justify-center gap-2" id="star-container">
                                        @for($i = 1; $i <= 5; $i++)
                                        <button type="button" onclick="setRating({{ $i }})" class="star-btn text-gray-300 hover:text-yellow-400 focus:outline-none transition-colors" data-value="{{ $i }}">
                                            <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        </button>
                                        @endfor
                                    </div>
                                    <input type="hidden" name="rating" id="rating-input" required>
                                    <p id="rating-error" class="text-xs text-red-500 mt-1 hidden">Silakan pilih rating bintang</p>
                                </div>

                                <!-- Comment -->
                                <div class="mb-4">
                                    <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Ulasan Anda</label>
                                    <textarea name="comment" id="comment" rows="4" class="w-full rounded-xl border-gray-300 focus:border-telu-red focus:ringfocus:ring-telu-red shadow-sm sm:text-sm p-3" placeholder="Tulis pengalaman Anda tentang produk ini... (Min. 5 karakter)" required minlength="5"></textarea>
                                </div>

                                <div class="flex flex-row-reverse gap-3 mt-6">
                                    <button type="submit" id="submit-btn" class="w-full sm:w-auto px-6 py-2.5 bg-[#EC1C25] text-white text-sm font-bold rounded-xl hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#EC1C25] transition-all shadow-lg shadow-red-100 disabled:opacity-50 disabled:cursor-not-allowed">
                                        Kirim Ulasan
                                    </button>
                                    <button type="button" onclick="closeReviewModal()" class="w-full sm:w-auto px-6 py-2.5 bg-white text-gray-700 text-sm font-bold rounded-xl border border-gray-300 hover:bg-gray-50 focus:outline-none transition-all">
                                        Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentRating = 0;

    function openReviewModal(productId, orderId, productTitle, productPrice, productImage) {
        document.getElementById('review-product-id').value = productId;
        document.getElementById('review-order-id').value = orderId;
        document.getElementById('review-product-title').innerText = productTitle;
        document.getElementById('review-product-price').innerText = productPrice;
        document.getElementById('review-product-image').src = productImage;
        
        // Reset form
        document.getElementById('reviewForm').reset();
        setRating(0);
        document.getElementById('reviewModal').classList.remove('hidden');
    }

    function closeReviewModal() {
        document.getElementById('reviewModal').classList.add('hidden');
    }

    function setRating(rating) {
        currentRating = rating;
        document.getElementById('rating-input').value = rating;
        
        const stars = document.querySelectorAll('.star-btn');
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-400');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });
        
        if (rating > 0) {
            document.getElementById('rating-error').classList.add('hidden');
        }
    }

    function submitReview(event) {
        event.preventDefault();
        
        if (currentRating === 0) {
            document.getElementById('rating-error').classList.remove('hidden');
            return;
        }

        const btn = document.getElementById('submit-btn');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerText = 'Mengirim...';

        const formData = new FormData(event.target);
        
        // Parse Form Data to JSON Object
        const data = {};
        formData.forEach((value, key) => data[key] = value);

        fetch('{{ route("reviews.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                closeReviewModal();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message,
                    confirmButtonColor: '#EC1C25'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.message,
                    confirmButtonColor: '#EC1C25'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan sistem',
                confirmButtonColor: '#EC1C25'
            });
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerText = originalText;
        });
    }
</script>
