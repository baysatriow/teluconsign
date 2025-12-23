@extends('layouts.admin')

@section('content')
<div x-data="{ 
    addModalOpen: false, 
    editModalOpen: false, 
    deleteModalOpen: false,
    
    // Form Data
    modalTitle: 'Tambah Kategori',
    categoryId: null,
    categoryName: '',
    parentId: '',
    
    // Delete Data
    deleteTargetId: null,
    deleteTargetName: '',
    hasProducts: false,
    productCount: 0,
    childrenCount: 0,
    deleteAction: 'none', // none, reassign, force_delete
    reassignTargetId: '',

    // Reassign Dropdown Data (Searchable)
    dropdownOpen: false,
    dropdownSearch: '',
    reassignName: '-- Pilih Kategori Tujuan --',
    categoryOptions: {{ $allCategories->map(fn($c) => ['id' => $c->category_id, 'name' => $c->name])->toJson() }},

    get filteredReassignOptions() {
        if (this.dropdownSearch === '') {
            return this.categoryOptions.filter(opt => opt.id != this.deleteTargetId);
        }
        return this.categoryOptions.filter(opt => 
            opt.name.toLowerCase().includes(this.dropdownSearch.toLowerCase()) && 
            opt.id != this.deleteTargetId
        );
    },

    // Methods
    selectReassignCategory(id, name) {
        this.reassignTargetId = id;
        this.reassignName = name;
        this.dropdownOpen = false;
        this.dropdownSearch = '';
    },

    openAddModal(pId = '') {
        this.addModalOpen = true;
        this.modalTitle = 'Tambah Kategori';
        this.categoryId = null;
        this.categoryName = '';
        this.parentId = pId;
        this.editModalOpen = false;
    },

    openEditModal(id, name, pId) {
        this.editModalOpen = true;
        this.addModalOpen = false;
        this.modalTitle = 'Edit Kategori';
        this.categoryId = id;
        this.categoryName = name;
        this.parentId = pId ? pId : '';
    },

    async checkDelete(id) {
        // Fetch check logic
        let response = await fetch('{{ route('admin.categories.check', ':id') }}'.replace(':id', id));
        let data = await response.json();
        
        this.deleteTargetId = id;
        this.deleteTargetName = data.category_name;
        this.productCount = data.product_count;
        this.childrenCount = data.children_count;
        this.hasProducts = (this.productCount > 0);
        
        this.deleteAction = 'none'; // reset
        this.deleteModalOpen = true;
    }
}">

    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Manajemen Kategori</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola struktur kategori produk secara hierarkis.</p>
        </div>
        <button @click="openAddModal()" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-red-500/30 transition-transform hover:-translate-y-0.5 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Induk
        </button>
    </div>

    <!-- Tree View Card -->
    <div class="bg-white rounded-2xl shadow-soft border border-gray-100 p-6 min-h-[400px]">
        @if($categories->count() > 0)
            <ul class="space-y-2">
                @foreach($categories as $category)
                    @include('admin.categories.partials.category_item', ['category' => $category])
                @endforeach
            </ul>
        @else
            <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                <p>Belum ada kategori.</p>
            </div>
        @endif
    </div>

    <!-- ADD / EDIT MODAL -->
    <div x-show="addModalOpen || editModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="addModalOpen || editModalOpen" class="fixed inset-0 transition-opacity" @click="addModalOpen = false; editModalOpen = false">
                <div class="absolute inset-0 bg-gray-900 opacity-50"></div>
            </div>

            <div x-show="addModalOpen || editModalOpen" class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl relative">
                
                <h3 class="text-xl font-bold text-gray-900 mb-4" x-text="modalTitle"></h3>
                
                <form :action="editModalOpen ? '{{ url('admin/categories') }}/' + categoryId : '{{ route('admin.categories.store') }}'" method="POST">
                    @csrf
                    <template x-if="editModalOpen">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Kategori</label>
                        <input type="text" name="name" x-model="categoryName" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 font-medium" required placeholder="Contoh: Elektronik">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Induk Kategori</label>
                        <select name="parent_id" x-model="parentId" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500">
                            <option value="">(Tanpa Induk / Root)</option>
                            @foreach($allCategories as $cat)
                                <option value="{{ $cat->category_id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Biarkan kosong jika ini adalah kategori utama.</p>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" @click="addModalOpen = false; editModalOpen = false" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-red-600 text-white font-bold hover:bg-red-700 shadow-lg shadow-red-500/30" x-text="editModalOpen ? 'Simpan Perubahan' : 'Tambah Kategori'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- DELETE CONFIRMATION MODAL -->
    <div x-show="deleteModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
         <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" @click="deleteModalOpen = false">
                <div class="absolute inset-0 bg-gray-900 opacity-60"></div>
            </div>

            <div class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl relative">
                <div class="flex items-start gap-4 mb-4">
                    <div class="p-3 bg-red-50 rounded-full text-red-500 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Hapus Kategori?</h3>
                        <p class="text-sm text-gray-500 mt-1">Anda akan menghapus kategori <b class="text-gray-900" x-text="deleteTargetName"></b>.</p>
                    </div>
                </div>

                <form :action="'{{ url('admin/categories') }}/' + deleteTargetId" method="POST">
                    @csrf
                    @method('DELETE')

                    <!-- Warning if has children -->
                    <div x-show="childrenCount > 0" class="mb-4 bg-yellow-50 border border-yellow-200 text-yellow-800 p-3 rounded-lg text-sm">
                        ⚠️ Kategori ini memiliki <b x-text="childrenCount"></b> sub-kategori. Sub-kategori akan menjadi kategori utama (root) jika induknya dihapus.
                    </div>

                    <!-- Options if has products -->
                    <div x-show="hasProducts" class="mb-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <p class="text-sm text-gray-800 font-bold mb-3">
                            ⚠️ Terdapat <span class="text-red-600" x-text="productCount"></span> produk dalam kategori ini. 
                            Pilih tindakan:
                        </p>
                        
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg cursor-pointer hover:border-indigo-400 transition-colors">
                                <input type="radio" name="action" value="reassign" x-model="deleteAction" class="text-indigo-600 focus:ring-indigo-500">
                                <div>
                                    <span class="block text-sm font-bold text-gray-900">Pindahkan Produk</span>
                                    <span class="block text-xs text-gray-500">Pindahkan semua produk ke kategori lain.</span>
                                </div>
                            </label>

                            <div x-show="deleteAction === 'reassign'" class="ml-7 mt-2" x-transition>
                                
                                <input type="hidden" name="target_category_id" x-model="reassignTargetId">

                                <div class="relative">
                                    <button type="button" @click="dropdownOpen = !dropdownOpen; if(dropdownOpen) $nextTick(() => $refs.searchInput.focus())" 
                                            class="w-full flex items-center justify-between text-sm bg-white border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-red-500 text-left hover:border-red-400 transition-colors">
                                        <span x-text="reassignName" :class="reassignTargetId ? 'text-gray-900 font-medium' : 'text-gray-500'"></span>
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div x-show="dropdownOpen" @click.away="dropdownOpen = false" 
                                         class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-60 overflow-y-auto"
                                         style="display: none;">
                                        
                                        <!-- Search Input -->
                                        <div class="sticky top-0 bg-white p-2 border-b border-gray-100">
                                            <input x-ref="searchInput" x-model="dropdownSearch" type="text" 
                                                   class="w-full text-sm border-gray-200 rounded-md focus:border-red-500 focus:ring-red-500 px-3 py-2" 
                                                   placeholder="Cari kategori...">
                                        </div>

                                        <!-- Options -->
                                        <ul>
                                            <template x-for="option in filteredReassignOptions" :key="option.id">
                                                <li @click="selectReassignCategory(option.id, option.name)"
                                                    class="px-3 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 cursor-pointer transition-colors font-medium">
                                                    <span x-text="option.name"></span>
                                                </li>
                                            </template>
                                            <li x-show="filteredReassignOptions.length === 0" class="px-3 py-4 text-sm text-gray-400 italic text-center">
                                                Kategori tidak ditemukan.
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <label class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg cursor-pointer hover:border-red-400 transition-colors">
                                <input type="radio" name="action" value="force_delete" x-model="deleteAction" class="text-red-600 focus:ring-red-500">
                                <div>
                                    <span class="block text-sm font-bold text-red-700">Hapus Semua Produk</span>
                                    <span class="block text-xs text-gray-500">Produk akan dihapus permanen. Tidak disarankan.</span>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <div x-show="!hasProducts && childrenCount == 0" class="mb-6 text-sm text-gray-600">
                        Kategori ini kosong, aman untuk dihapus.
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="deleteModalOpen = false" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-red-600 text-white font-bold hover:bg-red-700 shadow-lg" :disabled="hasProducts && deleteAction === 'none'">Hapus Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
