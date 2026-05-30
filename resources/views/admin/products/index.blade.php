@extends('admin.layouts.app')

@section('title', 'Manajemen Produk')
@section('page-title', 'Produk')

@section('content')
<!-- Filter & Actions - Compact -->
<div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
    <form method="GET" class="w-full lg:w-auto flex flex-wrap gap-2">
        <!-- Search Input -->
        <div class="relative w-full sm:w-64">
            <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="search" placeholder="Cari nama produk..." 
                   value="{{ request('search') }}"
                   class="w-full pl-9 pr-3 py-2 bg-white border border-gray-100 rounded-xl text-[11px] font-bold focus:outline-none focus:ring-4 focus:ring-orange-500/5 transition-all shadow-sm">
        </div>

        <!-- Category Filter -->
        <div class="relative w-full sm:w-40">
            <i data-lucide="tag" class="w-3.5 h-3.5 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
            <select name="category" class="w-full pl-9 pr-8 py-2 bg-white border border-gray-100 rounded-xl text-[11px] font-bold focus:outline-none focus:ring-4 focus:ring-orange-500/5 transition-all shadow-sm appearance-none cursor-pointer">
                <option value="">Semua Kategori</option>
                @foreach($categories ?? [] as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Availability Filter -->
        <div class="relative w-full sm:w-36">
            <i data-lucide="check-circle" class="w-3.5 h-3.5 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
            <select name="availability" class="w-full pl-9 pr-8 py-2 bg-white border border-gray-100 rounded-xl text-[11px] font-bold focus:outline-none focus:ring-4 focus:ring-orange-500/5 transition-all shadow-sm appearance-none cursor-pointer">
                <option value="">Semua Status</option>
                <option value="available" {{ request('availability') == 'available' ? 'selected' : '' }}>Tersedia</option>
                <option value="unavailable" {{ request('availability') == 'unavailable' ? 'selected' : '' }}>Habis</option>
            </select>
        </div>

        <!-- Reset Button -->
        @if(request('search') || request('category') || request('availability'))
        <a href="{{ route('admin.products.index') }}" 
           class="flex items-center justify-center p-2 bg-slate-100 text-slate-500 rounded-xl hover:bg-slate-200 transition-all shrink-0 shadow-sm group"
           title="Hapus Filter">
            <i data-lucide="rotate-ccw" class="w-4 h-4 group-hover:rotate-[-45deg] transition-transform"></i>
        </a>
        @endif
        
        <button type="submit" class="hidden"></button>
    </form>

    <a href="{{ route('admin.products.create') }}" 
       class="w-full lg:w-auto flex items-center justify-center gap-2 px-4 py-2.5 bg-orange-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-orange-600/20 hover:scale-105 transition-all">
        <i data-lucide="plus" class="w-4 h-4"></i>
        Tambah Produk
    </a>
</div>

<!-- Stats Bar - Compact -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-8">
    <div class="bg-white p-3 sm:p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
        <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600">
            <i data-lucide="package" class="w-4 h-4"></i>
        </div>
        <div>
            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Total</p>
            <p class="text-xs font-black text-slate-900 leading-none mt-0.5">{{ $products->total() }}</p>
        </div>
    </div>
    <div class="bg-white p-3 sm:p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
        <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center text-emerald-600">
            <i data-lucide="check-circle-2" class="w-4 h-4"></i>
        </div>
        <div>
            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Tersedia</p>
            <p class="text-xs font-black text-slate-900 leading-none mt-0.5">{{ $products->where('is_available', true)->count() }}</p>
        </div>
    </div>
    <div class="bg-white p-3 sm:p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
        <div class="w-8 h-8 bg-rose-50 rounded-lg flex items-center justify-center text-rose-600">
            <i data-lucide="x-circle" class="w-4 h-4"></i>
        </div>
        <div>
            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Habis</p>
            <p class="text-xs font-black text-slate-900 leading-none mt-0.5">{{ $products->where('is_available', false)->count() }}</p>
        </div>
    </div>
    <div class="bg-white p-3 sm:p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
        <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center text-orange-600">
            <i data-lucide="tags" class="w-4 h-4"></i>
        </div>
        <div>
            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Kategori</p>
            <p class="text-xs font-black text-slate-900 leading-none mt-0.5">{{ $categories->count() }}</p>
        </div>
    </div>
</div>

<!-- Product Table - Compact Design -->
<div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden mb-8">
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.25em]">Info Produk</th>
                    <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.25em]">Kategori</th>
                    <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.25em] text-right">Harga</th>
                    <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.25em] text-center">Status</th>
                    <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.25em] text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($products as $product)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-3">
                            <div class="shrink-0">
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
                                    <img src="{{ $imageUrl }}" class="w-10 h-10 object-cover rounded-xl shadow-sm border border-slate-100">
                                @else
                                    <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 border border-slate-100">
                                        <i data-lucide="image" class="w-5 h-5"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-black text-slate-900 tracking-tight leading-none truncate">{{ $product->name }}</p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase mt-1 tracking-tighter">ID #PROD-{{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[8px] font-black uppercase tracking-widest bg-slate-100 text-slate-500">
                            {{ $product->category->name ?? 'Uncategorized' }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-right">
                        <p class="text-xs font-black text-slate-900 leading-none">Rp{{ number_format($product->price, 0, ',', '.') }}</p>
                    </td>
                    <td class="px-6 py-3 text-center">
                        <button onclick="toggleAvailability({{ $product->id }}, this)" 
                                class="status-badge-{{ $product->id }} inline-flex items-center px-2.5 py-1 rounded-lg text-[8px] font-black uppercase tracking-widest transition-all
                                {{ $product->is_available ? 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' : 'bg-rose-50 text-rose-600 hover:bg-rose-100' }}">
                            <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $product->is_available ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                            {{ $product->is_available ? 'Tersedia' : 'Habis' }}
                        </button>
                    </td>
                    <td class="px-6 py-3 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('admin.products.edit', $product) }}" 
                               class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors shadow-sm" 
                               title="Edit">
                                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                            </a>
                            <button onclick="deleteProduct({{ $product->id }}, '{{ $product->name }}')" 
                                    class="p-2 bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-100 transition-colors shadow-sm" 
                                    title="Hapus">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-dashed border-slate-200">
                            <i data-lucide="box" class="w-8 h-8 text-slate-200"></i>
                        </div>
                        <p class="text-slate-400 font-black uppercase tracking-[0.2em] text-[10px]">Belum Ada Produk</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination - Compact -->
    @if($products->hasPages())
    <div class="px-6 py-4 bg-slate-50/30 border-t border-gray-50 text-[10px]">
        {{ $products->appends(request()->query())->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function toggleAvailability(productId, button) {
    const isAvailable = button.innerText.includes('Tersedia');
    const newStatusText = isAvailable ? 'Habis' : 'Tersedia';
    
    // Show confirmation
    Swal.fire({
        title: '<span class="font-black uppercase tracking-tighter text-xl">UBAH STATUS?</span>',
        html: `<p class="text-gray-500 font-medium text-sm">Produk akan ditandai sebagai <b class="${isAvailable ? 'text-rose-600' : 'text-emerald-600'} uppercase">${newStatusText}</b>.</p>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: isAvailable ? '#ef4444' : '#10b981',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'YA, UBAH',
        cancelButtonText: 'BATAL',
        borderRadius: '1.5rem'
    }).then((result) => {
        if (result.isConfirmed) {
            button.innerHTML = '<i data-lucide="loader-2" class="w-3 h-3 animate-spin"></i>';
            lucide.createIcons();
            
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
                    Swal.fire('GAGAL', data.message, 'error');
                }
            });
        }
    });
}

function deleteProduct(id, name) {
    Swal.fire({
        title: '<span class="font-black uppercase tracking-tighter text-xl text-rose-600">HAPUS PRODUK?</span>',
        html: `<p class="text-gray-500 font-medium text-sm">Produk <b>${name}</b> akan dihapus permanen dari sistem.</p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'YA, HAPUS',
        cancelButtonText: 'BATAL',
        borderRadius: '1.5rem'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/products/${id}`;
            form.innerHTML = `@csrf @method('DELETE')`;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush
@endsection
