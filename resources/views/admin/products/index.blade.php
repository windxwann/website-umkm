@extends('admin.layouts.app')

@section('title', 'Manajemen Produk')
@section('page-title', 'Daftar Produk')

@section('content')
<div class="bg-white rounded-2xl shadow-lg">
    <!-- Header -->
    <div class="p-6 border-b flex flex-wrap justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">
            <i class="fas fa-box mr-2 text-orange-600"></i>Semua Produk
        </h2>
        <div class="flex space-x-3">
            <a href="{{ route('admin.products.create') }}" 
               class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition flex items-center">
                <i class="fas fa-plus mr-2"></i>Tambah Produk
            </a>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="p-6 border-b">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" placeholder="Cari nama produk..." 
                       value="{{ request('search') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            </div>
            <div class="w-48">
                <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories ?? [] as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-40">
                <select name="availability" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    <option value="">Semua Status</option>
                    <option value="available" {{ request('availability') == 'available' ? 'selected' : '' }}>Tersedia</option>
                    <option value="unavailable" {{ request('availability') == 'unavailable' ? 'selected' : '' }}>Habis</option>
                </select>
            </div>
            <button type="submit" class="bg-orange-600 text-white px-6 py-2 rounded-lg hover:bg-orange-700">
                <i class="fas fa-search mr-2"></i>Filter
            </button>
        </form>
    </div>

    <!-- Products Table -->
    <div class="overflow-x-auto">
        <table class="w-full" id="productsTable">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gambar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($products as $index => $product)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">{{ $index + $products->firstItem() }}</td>
                    <td class="px-6 py-4">
                        @php
                            $imageUrl = null;
                            
                            if ($product->image) {
                                // Cek apakah sudah ada 'products/' prefix
                                if (str_starts_with($product->image, 'products/')) {
                                    $imagePath = $product->image;
                                } else {
                                    $imagePath = 'products/' . $product->image;
                                }
                                
                                // Cek apakah file benar-benar ada di storage
                                if (Storage::disk('public')->exists($imagePath)) {
                                    $imageUrl = asset('storage/' . $imagePath);
                                } else {
                                    // Coba tanpa prefix 'products/'
                                    if (Storage::disk('public')->exists($product->image)) {
                                        $imageUrl = asset('storage/' . $product->image);
                                    }
                                }
                            }
                        @endphp
                        
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" 
                                 alt="{{ $product->name }}"
                                 class="w-12 h-12 object-cover rounded-lg"
                                 onerror="this.onerror=null; this.src='https://via.placeholder.com/150?text=No+Image';">
                        @else
                            <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-semibold">{{ $product->name }}</td>
                    <td class="px-6 py-4">{{ $product->category->name ?? 'Tidak ada kategori' }}</td>
                    <td class="px-6 py-4 font-semibold text-orange-600">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4">
                        <button onclick="toggleAvailability({{ $product->id }})" 
                                class="px-2 py-1 rounded-full text-xs {{ $product->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $product->is_available ? 'Tersedia' : 'Habis' }}
                        </button>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-3">
                            <a href="{{ route('admin.products.edit', $product) }}" 
                               class="text-blue-600 hover:text-blue-800" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus produk {{ $product->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="p-6">
        {{ $products->links() }}
    </div>
</div>

@push('scripts')
<script>
function toggleAvailability(productId) {
    fetch(`/admin/products/${productId}/toggle-availability`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Gagal mengubah status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}
</script>
@endpush
@endsection