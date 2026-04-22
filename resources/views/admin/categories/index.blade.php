@extends('admin.layouts.app')

@section('title', 'Manajemen Kategori')
@section('page-title', 'Daftar Kategori')

@section('content')
<div class="bg-white rounded-2xl shadow-lg">
    <!-- Header -->
    <div class="p-6 border-b flex flex-wrap justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">
            <i class="fas fa-tags mr-2 text-orange-600"></i>Semua Kategori
        </h2>
        <a href="{{ route('admin.categories.create') }}" 
           class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition flex items-center">
            <i class="fas fa-plus mr-2"></i>Tambah Kategori
        </a>
    </div>

    <!-- Categories Grid -->
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($categories as $category)
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl shadow-md overflow-hidden border border-gray-100 hover:shadow-xl transition-all duration-300">
                <!-- Category Image -->
                <div class="h-40 overflow-hidden relative">
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
                             class="w-full h-full object-cover group-hover:scale-110 transition duration-300"
                             onerror="this.onerror=null; this.src='https://via.placeholder.com/400x200?text=No+Image';">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-orange-100 to-orange-50 flex items-center justify-center">
                            <i class="fas fa-tag text-6xl text-orange-300"></i>
                        </div>
                    @endif
                    <div class="absolute top-2 right-2">
                        <span class="bg-orange-600 text-white px-3 py-1 rounded-full text-xs font-semibold">
                            {{ $category->products_count ?? 0 }} Produk
                        </span>
                    </div>
                </div>

                <!-- Category Info -->
                <div class="p-5">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $category->name }}</h3>
                    <p class="text-gray-600 text-sm mb-4">{{ $category->description ?? 'Tidak ada deskripsi' }}</p>
                    
                    <!-- Action Buttons -->
                    <div class="flex justify-between items-center">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.categories.edit', $category) }}" 
                               class="bg-blue-100 text-blue-600 px-3 py-2 rounded-lg hover:bg-blue-200 transition" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Yakin ingin menghapus kategori ini?')" 
                                        class="bg-red-100 text-red-600 px-3 py-2 rounded-lg hover:bg-red-200 transition" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                        <a href="{{ route('admin.products.index', ['category' => $category->id]) }}" 
                           class="text-orange-600 hover:text-orange-700 text-sm font-semibold">
                            Lihat Produk <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <div class="bg-gray-50 rounded-2xl p-12">
                    <i class="fas fa-tags text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-2xl font-semibold text-gray-700 mb-2">Belum Ada Kategori</h3>
                    <p class="text-gray-500 mb-6">Mulai dengan menambahkan kategori menu pertama Anda</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    @if($categories->hasPages())
    <div class="p-6 border-t">
        {{ $categories->links() }}
    </div>
    @endif
</div>
@endsection