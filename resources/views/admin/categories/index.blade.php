@extends('admin.layouts.app')

@section('title', 'Manajemen Kategori')
@section('page-title', 'Daftar Kategori')

@section('content')
<div class="container mx-auto px-4 py-4 md:py-8">
    <!-- Header Mobile Friendly -->
    <div class="mb-6 md:mb-8 bg-gradient-to-r from-orange-600 to-orange-500 rounded-xl p-4 md:p-6 text-white shadow-lg">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold mb-1 md:mb-2">Manajemen Kategori</h1>
                <p class="text-orange-100 text-sm md:text-base">
                    <i class="fas fa-tags mr-2"></i>Kelola semua kategori menu
                </p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-folder-open text-6xl opacity-30"></i>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6 md:mb-8">
        <div class="bg-white rounded-xl shadow p-3 md:p-4 border-l-4 border-blue-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Total Kategori</p>
                    <p class="text-xl md:text-2xl font-bold text-blue-600">{{ $categories->total() }}</p>
                </div>
                <div class="bg-blue-100 w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center">
                    <i class="fas fa-tags text-blue-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-3 md:p-4 border-l-4 border-green-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Total Produk</p>
                    <p class="text-xl md:text-2xl font-bold text-green-600">{{ $categories->sum('products_count') }}</p>
                </div>
                <div class="bg-green-100 w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center">
                    <i class="fas fa-box text-green-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-3 md:p-4 border-l-4 border-orange-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Kategori Aktif</p>
                    <p class="text-xl md:text-2xl font-bold text-orange-600">{{ $categories->count() }}</p>
                </div>
                <div class="bg-orange-100 w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-orange-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-3 md:p-4 border-l-4 border-purple-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Kategori Baru</p>
                    <p class="text-xl md:text-2xl font-bold text-purple-600">{{ $categories->where('created_at', '>=', now()->subDays(7))->count() }}</p>
                </div>
                <div class="bg-purple-100 w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center">
                    <i class="fas fa-star text-purple-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Section -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-4 md:p-6 border-b flex flex-col md:flex-row justify-between items-start md:items-center gap-3 bg-gradient-to-r from-gray-50 to-white">
            <h2 class="text-lg md:text-xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-folder-tree text-orange-600 mr-2"></i>
                Daftar Kategori
                <span class="ml-2 text-sm text-gray-500">({{ $categories->total() }})</span>
            </h2>
            <a href="{{ route('admin.categories.create') }}" 
               class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition flex items-center text-sm shadow-md hover:shadow-lg">
                <i class="fas fa-plus mr-2"></i>Tambah Kategori
            </a>
        </div>

        <!-- Categories Grid - Responsive -->
        <div class="p-4 md:p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-4 md:gap-6">
                @forelse($categories as $category)
                <div class="group bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md overflow-hidden border border-gray-200 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <!-- Category Image -->
                    <div class="h-40 overflow-hidden relative bg-gray-100">
                        @php
                            $imageUrl = null;
                            if ($category->image) {
                                $imagePath = str_starts_with($category->image, 'categories/') ? $category->image : 'categories/' . $category->image;
                                if (Storage::disk('public')->exists($imagePath)) {
                                    $imageUrl = asset('storage/' . $imagePath);
                                } elseif (Storage::disk('public')->exists($category->image)) {
                                    $imageUrl = asset('storage/' . $category->image);
                                }
                            }
                        @endphp
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" 
                                 alt="{{ $category->name }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition duration-500"
                                 onerror="this.onerror=null; this.src='https://via.placeholder.com/400x200?text=No+Image';">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-orange-100 to-orange-50 flex items-center justify-center">
                                <i class="fas fa-folder-open text-6xl text-orange-300 group-hover:scale-110 transition duration-300"></i>
                            </div>
                        @endif
                        
                        <!-- Badge Count -->
                        <div class="absolute top-3 right-3">
                            <span class="bg-orange-600 text-white px-2.5 py-1 rounded-full text-xs font-semibold shadow-md">
                                <i class="fas fa-box mr-1 text-xs"></i>
                                {{ $category->products_count ?? 0 }} Produk
                            </span>
                        </div>
                    </div>

                    <!-- Category Info -->
                    <div class="p-4 md:p-5">
                        <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-2 line-clamp-1">{{ $category->name }}</h3>
                        <p class="text-gray-600 text-xs md:text-sm mb-4 line-clamp-2 min-h-[40px]">
                            {{ $category->description ?? 'Tidak ada deskripsi' }}
                        </p>
                        
                        <!-- Action Buttons -->
                        <div class="flex justify-between items-center">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.categories.edit', $category) }}" 
                                   class="bg-blue-100 text-blue-600 px-3 py-2 rounded-lg hover:bg-blue-200 transition" title="Edit Kategori">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deleteCategory({{ $category->id }}, '{{ $category->name }}')" 
                                        class="bg-red-100 text-red-600 px-3 py-2 rounded-lg hover:bg-red-200 transition" title="Hapus Kategori">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                            <a href="{{ route('admin.products.index', ['category' => $category->id]) }}" 
                               class="text-orange-600 hover:text-orange-700 text-xs md:text-sm font-semibold flex items-center gap-1 group">
                                Lihat Produk
                                <i class="fas fa-arrow-right group-hover:translate-x-1 transition"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full">
                    <div class="text-center py-12 md:py-16">
                        <div class="bg-gray-50 rounded-2xl p-8 md:p-12">
                            <i class="fas fa-folder-open text-6xl md:text-7xl text-gray-300 mb-4"></i>
                            <h3 class="text-xl md:text-2xl font-semibold text-gray-700 mb-2">Belum Ada Kategori</h3>
                            <p class="text-gray-500 mb-6 text-sm md:text-base">Mulai dengan menambahkan kategori menu pertama Anda</p>
                            <a href="{{ route('admin.categories.create') }}" 
                               class="bg-orange-600 text-white px-5 md:px-6 py-2.5 md:py-3 rounded-lg hover:bg-orange-700 transition inline-flex items-center">
                                <i class="fas fa-plus mr-2"></i>Tambah Kategori
                            </a>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
        @if($categories->hasPages())
        <div class="px-4 md:px-6 py-4 border-t">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
                <div class="text-xs sm:text-sm text-gray-500">
                    Menampilkan {{ $categories->firstItem() ?? 0 }} - {{ $categories->lastItem() ?? 0 }} dari {{ $categories->total() }} kategori
                </div>
                <div>
                    {{ $categories->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Delete category with SweetAlert2 confirmation
function deleteCategory(categoryId, categoryName) {
    Swal.fire({
        title: 'Hapus Kategori?',
        html: `Anda yakin ingin menghapus kategori <strong>${categoryName}</strong>?<br>
               <span class="text-sm text-yellow-600">⚠️ Produk dalam kategori ini akan kehilangan kategorinya.</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-xl',
            confirmButton: 'px-6 py-2 rounded-lg font-semibold',
            cancelButton: 'px-6 py-2 rounded-lg font-semibold'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang menghapus kategori',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/categories/${categoryId}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Line clamp utility
document.addEventListener('DOMContentLoaded', function() {
    // Add line-clamp styles if not exists
    if (!document.querySelector('style#line-clamp')) {
        const style = document.createElement('style');
        style.id = 'line-clamp';
        style.textContent = `
            .line-clamp-1 {
                display: -webkit-box;
                -webkit-line-clamp: 1;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
        `;
        document.head.appendChild(style);
    }
});
</script>
@endpush

@push('styles')
<style>
    /* Smooth transitions */
    .group {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Scale animation on hover */
    .group:hover {
        transform: translateY(-4px);
    }
    
    /* Line clamp utilities */
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush
@endsection