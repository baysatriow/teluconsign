@props(['category'])

<li x-data="{ expanded: false }" class="relative">
    <div class="flex items-center justify-between p-3 bg-gray-50 border border-gray-100 rounded-xl hover:bg-white hover:shadow-sm transition-all group">
        
        <div class="flex items-center gap-3">
            <!-- Toggle Button (Only if has children) -->
            @if($category->children->count() > 0)
                <button @click="expanded = !expanded" class="p-1 rounded-lg hover:bg-gray-200 text-gray-500 transition-colors">
                    <svg class="w-4 h-4 transition-transform duration-200" :class="expanded ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            @else
                <span class="w-6"></span> <!-- Spacer -->
            @endif

            <div class="flex flex-col">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-gray-800 text-sm">{{ $category->name }}</span>
                    @if($category->parent_id)
                        <span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full font-bold">Child</span>
                    @else
                        <span class="text-[10px] bg-red-50 text-red-700 px-2 py-0.5 rounded-full font-bold">Root</span>
                    @endif
                </div>
                <div class="text-xs text-gray-400 mt-1 font-medium">
                    {{ $category->products()->count() }} Produk • {{ $category->children->count() }} Sub-kategori
                </div>
            </div>
        </div>

        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
            <!-- Add Child Button -->
            <button @click="openAddModal('{{ $category->category_id }}')" class="p-2 text-gray-400 hover:bg-red-50 hover:text-red-600 rounded-lg transition-colors" title="Tambah Sub-Kategori">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </button>

            <!-- Edit Button -->
            <button @click="openEditModal('{{ $category->category_id }}', '{{ $category->name }}', '{{ $category->parent_id }}')" class="p-2 text-gray-400 hover:bg-red-50 hover:text-red-600 rounded-lg transition-colors" title="Edit Kategori">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </button>

            <!-- Delete Button -->
            <button @click="checkDelete('{{ $category->category_id }}')" class="p-2 text-gray-400 hover:bg-red-50 hover:text-red-600 rounded-lg transition-colors" title="Hapus Kategori">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
        </div>
    </div>

    <!-- Recursive Children -->
    @if($category->children->count() > 0)
        <ul x-show="expanded" x-collapse class="ml-8 mt-2 space-y-2 border-l-2 border-gray-100 pl-2">
            @foreach($category->children as $child)
                @include('admin.categories.partials.category_item', ['category' => $child])
            @endforeach
        </ul>
    @endif
</li>
