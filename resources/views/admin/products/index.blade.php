@extends('admin.layouts.app')

@section('title', 'Manajemen Produk')
@section('page-title', 'Daftar Produk')

@section('content')
<div class="container mx-auto px-4 py-4 md:py-8">
    <!-- Header Mobile Friendly -->
    <div class="mb-6 md:mb-8 bg-gradient-to-r from-orange-600 to-orange-500 rounded-xl p-4 md:p-6 text-white shadow-lg">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold mb-1 md:mb-2">Manajemen Produk</h1>
                <p class="text-orange-100 text-sm md:text-base">
                    <i class="fas fa-box mr-2"></i>Kelola semua produk menu
                </p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-utensils text-6xl opacity-30"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Search - Responsive -->
    <div class="bg-white rounded-xl shadow-lg p-4 md:p-6 mb-6 md:mb-8">
        <form method="GET" class="flex flex-col md:flex-row gap-3 md:gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-search text-gray-400 mr-1"></i> Cari Produk
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" name="search" placeholder="Cari nama produk..." 
                           value="{{ request('search') }}"
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm">
                </div>
            </div>
            <div class="w-full md:w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-folder text-gray-400 mr-1"></i> Kategori
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <i class="fas fa-folder"></i>
                    </span>
                    <select name="category" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 text-sm appearance-none">
                        <option value="">Semua Kategori</option>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="w-full md:w-40">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-info-circle text-gray-400 mr-1"></i> Status
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <i class="fas fa-circle"></i>
                    </span>
                    <select name="availability" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 text-sm appearance-none">
                        <option value="">Semua Status</option>
                        <option value="available" {{ request('availability') == 'available' ? 'selected' : '' }}>✅ Tersedia</option>
                        <option value="unavailable" {{ request('availability') == 'unavailable' ? 'selected' : '' }}>❌ Habis</option>
                    </select>
                </div>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-orange-600 text-white px-5 md:px-6 py-2.5 rounded-lg hover:bg-orange-700 transition flex items-center justify-center text-sm shadow-md hover:shadow-lg">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.products.index') }}" class="bg-gray-500 text-white px-5 md:px-6 py-2.5 rounded-lg hover:bg-gray-600 transition flex items-center justify-center text-sm shadow-md hover:shadow-lg">
                    <i class="fas fa-redo-alt mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6 md:mb-8">
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-blue-500 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Total Produk</p>
                    <p class="text-xl md:text-2xl font-bold text-blue-600">{{ $products->total() }}</p>
                </div>
                <div class="bg-blue-100 w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center">
                    <i class="fas fa-box text-blue-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-green-500 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Tersedia</p>
                    <p class="text-xl md:text-2xl font-bold text-green-600">{{ $products->where('is_available', true)->count() }}</p>
                </div>
                <div class="bg-green-100 w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-red-500 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Habis</p>
                    <p class="text-xl md:text-2xl font-bold text-red-600">{{ $products->where('is_available', false)->count() }}</p>
                </div>
                <div class="bg-red-100 w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-orange-500 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Kategori</p>
                    <p class="text-xl md:text-2xl font-bold text-orange-600">{{ $categories->count() ?? 0 }}</p>
                </div>
                <div class="bg-orange-100 w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center">
                    <i class="fas fa-tags text-orange-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-4 md:p-6 border-b flex flex-col md:flex-row justify-between items-start md:items-center gap-3 bg-gradient-to-r from-gray-50 to-white">
            <h2 class="text-lg md:text-xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-list text-orange-600 mr-2"></i>
                Daftar Produk
                <span class="ml-2 text-sm text-gray-500">({{ $products->total() }})</span>
            </h2>
            <a href="{{ route('admin.products.create') }}" 
               class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition flex items-center text-sm shadow-md hover:shadow-lg">
                <i class="fas fa-plus mr-2"></i>Tambah Produk
            </a>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 whitespace-nowrap">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Gambar</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Harga</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($products as $index => $product)
                    <tr class="hover:bg-gray-50 transition duration-200" id="product-row-{{ $product->id }}">
                        <td class="px-6 py-4 text-sm">{{ $index + $products->firstItem() }}</td>
                        <td class="px-6 py-4">
                            @php
                                $imageUrl = null;
                                if ($product->image) {
                                    $imagePath = str_starts_with($product->image, 'products/') ? $product->image : 'products/' . $product->image;
                                    if (Storage::disk('public')->exists($imagePath)) {
                                        $imageUrl = asset('storage/' . $imagePath);
                                    }
                                }
                            @endphp
                            @if($imageUrl)
                                <img src="{{ $imageUrl }}" 
                                     alt="{{ $product->name }}"
                                     class="w-10 h-10 object-cover rounded-lg shadow-sm"
                                     onerror="this.onerror=null; this.src='https://via.placeholder.com/100?text=No+Image';">
                            @else
                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400 text-lg"></i>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-800">{{ $product->name }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                <i class="fas fa-folder mr-1 text-gray-500"></i>
                                {{ $product->category->name ?? 'Tidak ada kategori' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-bold text-orange-600">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            <button onclick="toggleAvailability({{ $product->id }}, this)" 
                                    class="status-badge-{{ $product->id }} px-3 py-1 rounded-full text-xs font-medium transition-all duration-200 hover:shadow-md
                                    {{ $product->is_available ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}">
                                <i class="fas {{ $product->is_available ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                {{ $product->is_available ? 'Tersedia' : 'Habis' }}
                            </button>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-3">
                                <a href="{{ route('admin.products.edit', $product) }}" 
                                   class="text-blue-600 hover:text-blue-800 transition-transform hover:scale-110 inline-block" 
                                   title="Edit Produk">
                                    <i class="fas fa-edit text-lg"></i>
                                </a>
                                <button onclick="deleteProduct({{ $product->id }}, '{{ $product->name }}')" 
                                        class="text-red-600 hover:text-red-800 transition-transform hover:scale-110 inline-block" 
                                        title="Hapus Produk">
                                    <i class="fas fa-trash-alt text-lg"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-box-open text-5xl mb-3 text-gray-300"></i>
                            <p class="text-lg">Belum ada produk</p>
                            <p class="text-sm text-gray-400 mt-1">Klik "Tambah Produk" untuk mulai menambahkan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden divide-y divide-gray-200">
            @forelse($products as $product)
            <div class="p-4 hover:bg-gray-50 transition duration-200" id="mobile-product-{{ $product->id }}">
                <div class="flex items-start space-x-3">
                    @php
                        $imageUrl = null;
                        if ($product->image) {
                            $imagePath = str_starts_with($product->image, 'products/') ? $product->image : 'products/' . $product->image;
                            if (Storage::disk('public')->exists($imagePath)) {
                                $imageUrl = asset('storage/' . $imagePath);
                            }
                        }
                    @endphp
                    @if($imageUrl)
                        <img src="{{ $imageUrl }}" 
                             alt="{{ $product->name }}"
                             class="w-16 h-16 object-cover rounded-lg shadow-sm"
                             onerror="this.onerror=null; this.src='https://via.placeholder.com/100?text=No+Image';">
                    @else
                        <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-image text-gray-400 text-3xl"></i>
                        </div>
                    @endif
                    
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-gray-800">{{ $product->name }}</h3>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    <i class="fas fa-folder mr-1"></i>
                                    {{ $product->category->name ?? 'Tanpa Kategori' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-orange-600">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="flex justify-between items-center mt-3 pt-2 border-t border-gray-100">
                            <button onclick="toggleAvailability({{ $product->id }}, this)" 
                                    class="status-badge-{{ $product->id }} px-2 py-1 rounded-full text-xs font-medium transition-all duration-200
                                    {{ $product->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <i class="fas {{ $product->is_available ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                {{ $product->is_available ? 'Tersedia' : 'Habis' }}
                            </button>
                            <div class="flex space-x-4">
                                <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:text-blue-800 transition-transform hover:scale-110" title="Edit">
                                    <i class="fas fa-edit text-base"></i>
                                </a>
                                <button onclick="deleteProduct({{ $product->id }}, '{{ $product->name }}')" class="text-red-600 hover:text-red-800 transition-transform hover:scale-110" title="Hapus">
                                    <i class="fas fa-trash-alt text-base"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-box-open text-5xl mb-3 text-gray-300"></i>
                <p class="text-lg">Belum ada produk</p>
                <p class="text-sm text-gray-400 mt-1">Klik "Tambah Produk" untuk mulai menambahkan</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
        <div class="px-4 md:px-6 py-4 border-t bg-gray-50">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
                <div class="text-xs sm:text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Menampilkan {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} produk
                </div>
                <div>
                    {{ $products->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Toggle availability with SweetAlert2 and optimistic UI update
function toggleAvailability(productId, button) {
    const oldStatus = button.classList.contains('bg-green-100');
    const newStatusText = oldStatus ? 'Habis' : 'Tersedia';
    const newStatusClass = oldStatus ? 'bg-red-100 text-red-800 hover:bg-red-200' : 'bg-green-100 text-green-800 hover:bg-green-200';
    const newIcon = oldStatus ? 'fa-times-circle' : 'fa-check-circle';
    
    // Optimistic UI update
    const oldHtml = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Loading...';
    button.disabled = true;
    
    fetch(`/admin/products/${productId}/toggle-availability`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Update button style and text
            button.className = `status-badge-${productId} px-2 py-1 rounded-full text-xs font-medium transition-all duration-200 ${newStatusClass}`;
            button.innerHTML = `<i class="fas ${newIcon} mr-1"></i> ${newStatusText}`;
            button.disabled = false;
            
            // Update stats cards
            updateStatsCards(oldStatus, newStatusText);
            
            // Show success toast
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: `Status produk berhasil diubah menjadi ${newStatusText}`,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#fff',
                iconColor: '#f97316'
            });
        } else {
            // Revert on error
            button.innerHTML = oldHtml;
            button.disabled = false;
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message || 'Gagal mengubah status produk',
                confirmButtonColor: '#f97316',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.innerHTML = oldHtml;
        button.disabled = false;
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan. Silakan coba lagi.',
            confirmButtonColor: '#f97316',
            confirmButtonText: 'OK'
        });
    });
}

// Update stats cards when toggling availability
function updateStatsCards(oldStatus, newStatusText) {
    const availableCard = document.querySelector('.border-l-4.border-green-500 .text-xl.md\\:text-2xl.font-bold');
    const unavailableCard = document.querySelector('.border-l-4.border-red-500 .text-xl.md\\:text-2xl.font-bold');
    
    if (availableCard && unavailableCard) {
        let available = parseInt(availableCard.textContent);
        let unavailable = parseInt(unavailableCard.textContent);
        
        if (oldStatus) {
            // Was available, now unavailable
            available--;
            unavailable++;
        } else {
            // Was unavailable, now available
            available++;
            unavailable--;
        }
        
        availableCard.textContent = available;
        unavailableCard.textContent = unavailable;
    }
}

// Delete product with SweetAlert2 confirmation
function deleteProduct(productId, productName) {
    Swal.fire({
        title: 'Hapus Produk?',
        html: `
            <div class="text-left">
                <p class="mb-2">Anda yakin ingin menghapus produk <strong class="text-orange-600">${productName}</strong>?</p>
                <div class="bg-red-50 p-3 rounded-lg mt-3">
                    <p class="text-red-600 text-sm flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Tindakan ini tidak dapat dibatalkan!
                    </p>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-trash-alt mr-2"></i>Ya, Hapus!',
        cancelButtonText: '<i class="fas fa-times mr-2"></i>Batal',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-xl',
            confirmButton: 'px-4 py-2 rounded-lg font-semibold',
            cancelButton: 'px-4 py-2 rounded-lg font-semibold'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang menghapus produk',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/products/${productId}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush
@endsection