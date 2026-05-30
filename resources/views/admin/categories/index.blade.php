@extends('admin.layouts.app')

@section('title', 'Manajemen Kategori')
@section('page-title', 'Kategori')

@section('content')
<!-- Actions & Stats - Compact -->
<div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
    <a href="{{ route('admin.categories.create') }}" 
       class="w-full lg:w-auto flex items-center justify-center gap-2 px-4 py-2.5 bg-orange-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-orange-600/20 hover:scale-[1.02] transition-all">
        <i data-lucide="plus" class="w-4 h-4"></i>
        Tambah Kategori
    </a>
</div>

<!-- Stats Bar - Compact -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-8">
    <div class="bg-white p-3 sm:p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
        <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600">
            <i data-lucide="grid" class="w-4 h-4"></i>
        </div>
        <div>
            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Total</p>
            <p class="text-xs font-black text-slate-900 leading-none mt-0.5">{{ $categories->total() }}</p>
        </div>
    </div>
    <div class="bg-white p-3 sm:p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
        <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center text-emerald-600">
            <i data-lucide="package" class="w-4 h-4"></i>
        </div>
        <div>
            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Produk</p>
            <p class="text-xs font-black text-slate-900 leading-none mt-0.5">{{ $categories->sum('products_count') }}</p>
        </div>
    </div>
    <div class="bg-white p-3 sm:p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
        <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center text-orange-600">
            <i data-lucide="check-circle" class="w-4 h-4"></i>
        </div>
        <div>
            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Aktif</p>
            <p class="text-xs font-black text-slate-900 leading-none mt-0.5">{{ $categories->count() }}</p>
        </div>
    </div>
    <div class="bg-white p-3 sm:p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
        <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center text-purple-600">
            <i data-lucide="sparkles" class="w-4 h-4"></i>
        </div>
        <div>
            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Baru</p>
            <p class="text-xs font-black text-slate-900 leading-none mt-0.5">{{ $categories->where('created_at', '>=', now()->subDays(7))->count() }}</p>
        </div>
    </div>
</div>

<!-- Categories Grid - Compact Design -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 mb-8">
    @forelse($categories as $category)
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden hover:shadow-lg transition-all duration-300 group flex flex-col">
        <!-- Category Image -->
        <div class="h-32 overflow-hidden relative bg-slate-100">
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
                <img src="{{ $imageUrl }}" alt="{{ $category->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            @else
                <div class="w-full h-full flex items-center justify-center text-slate-300">
                    <i data-lucide="image" class="w-8 h-8"></i>
                </div>
            @endif
            
            <div class="absolute top-3 right-3">
                <span class="bg-white/90 backdrop-blur px-2 py-1 rounded-lg text-[9px] font-black text-slate-900 uppercase tracking-widest shadow-sm">
                    {{ $category->products_count ?? 0 }} Produk
                </span>
            </div>
        </div>

        <!-- Category Info -->
        <div class="p-5 flex-1 flex flex-col">
            <h3 class="text-xs font-black text-slate-900 tracking-tight leading-none mb-2 truncate">{{ $category->name }}</h3>
            <p class="text-[10px] font-bold text-slate-400 leading-relaxed line-clamp-2 flex-1 mb-4">
                {{ $category->description ?? 'Tidak ada deskripsi' }}
            </p>
            
            <!-- Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-50 mt-auto">
                <div class="flex gap-1.5">
                    <a href="{{ route('admin.categories.edit', $category) }}" 
                       class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors" title="Edit">
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                    </a>
                    <button onclick="deleteCategory({{ $category->id }}, '{{ $category->name }}')" 
                            class="p-2 bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-100 transition-colors" title="Hapus">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    </button>
                </div>
                <a href="{{ route('admin.products.index', ['category' => $category->id]) }}" 
                   class="text-[9px] font-black text-orange-600 uppercase tracking-widest hover:text-orange-700 flex items-center gap-1">
                    Lihat
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full py-16 text-center">
        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-dashed border-slate-200">
            <i data-lucide="folder-open" class="w-8 h-8 text-slate-200"></i>
        </div>
        <p class="text-slate-400 font-black uppercase tracking-[0.2em] text-[10px]">Belum Ada Kategori</p>
    </div>
    @endforelse
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deleteCategory(categoryId, categoryName) {
    Swal.fire({
        title: '<span class="font-black uppercase tracking-tighter text-xl text-rose-600">HAPUS KATEGORI?</span>',
        html: `<p class="text-gray-500 font-medium text-sm">Anda yakin ingin menghapus kategori <b>${categoryName}</b>? Produk di dalamnya akan kehilangan kategorinya.</p>`,
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
            form.action = `/admin/categories/${categoryId}`;
            form.innerHTML = `@csrf @method('DELETE')`;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush
@endsection
