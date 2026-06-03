@extends('admin.layouts.app')

@section('title', 'Manajemen Kategori')
@section('page-title', 'Kategori')

@section('content')
<!-- Actions -->
<div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
    <a href="{{ route('admin.categories.create') }}" 
       class="w-full lg:w-auto flex items-center justify-center gap-2 px-6 py-3 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-slate-900/10 hover:bg-orange-600 transition-all">
        <i data-lucide="plus" class="w-4 h-4"></i>
        Tambah Kategori
    </a>
</div>

<!-- Categories Grid -->
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="categoryGrid">
    @forelse($categories as $category)
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 hover:shadow-lg transition-all duration-300 flex flex-col group">
        <!-- Category Image -->
        <div class="relative w-full aspect-video overflow-hidden rounded-t-3xl bg-slate-50">
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
                <div class="w-full h-full flex items-center justify-center text-slate-200">
                    <i data-lucide="image" class="w-8 h-8"></i>
                </div>
            @endif
            
            <div class="absolute top-2 sm:top-3 right-2 sm:right-3">
                <span class="bg-white/90 backdrop-blur px-2 sm:px-3 py-1 rounded-lg text-[8px] sm:text-[9px] font-black text-slate-900 uppercase tracking-widest shadow-sm">
                    {{ $category->products_count ?? 0 }} Produk
                </span>
            </div>
        </div>

        <!-- Category Info -->
        <div class="p-3.5 sm:p-5 flex-grow flex flex-col">
            <h3 class="text-sm sm:text-base font-black text-slate-900 tracking-tight line-clamp-1 mb-1">{{ $category->name }}</h3>
            <p class="text-[10px] sm:text-[11px] font-medium text-slate-500 mb-3 sm:mb-4 line-clamp-2 leading-relaxed">
                {{ $category->description ?? 'Tidak ada deskripsi' }}
            </p>
            
            <!-- Actions -->
            <div class="mt-auto flex items-center justify-between pt-3 sm:pt-4 border-t border-slate-50">
                <div class="flex gap-1.5 sm:gap-2">
                    <a href="{{ route('admin.categories.edit', $category) }}" 
                       class="p-2 sm:p-2.5 bg-slate-50 text-slate-600 rounded-xl hover:bg-orange-600 hover:text-white transition-all shadow-sm" title="Edit">
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                    </a>
                    <button onclick="deleteCategory({{ $category->id }}, '{{ addslashes($category->name) }}')" 
                            class="p-2 sm:p-2.5 bg-slate-50 text-slate-600 rounded-xl hover:bg-rose-600 hover:text-white transition-all shadow-sm" title="Hapus">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    </button>
                </div>
                <a href="{{ route('admin.products.index', ['category' => $category->id]) }}" 
                   class="text-[10px] sm:text-[11px] font-black text-orange-600 uppercase tracking-widest hover:text-orange-700 flex items-center gap-0.5 sm:gap-1">
                    Lihat
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full py-20 text-center">
        <p class="text-sm font-black text-slate-400 uppercase tracking-widest">Belum ada kategori</p>
    </div>
    @endforelse
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deleteCategory(categoryId, categoryName) {
    Swal.fire({
        title: '<span class="font-black uppercase tracking-tighter text-lg text-rose-600">HAPUS KATEGORI?</span>',
        html: `<p class="text-slate-500 font-medium text-xs">Anda yakin ingin menghapus kategori <b>${categoryName}</b>? Produk di dalamnya akan kehilangan kategorinya.</p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'YA, HAPUS',
        cancelButtonText: 'BATAL',
        customClass: { popup: 'rounded-3xl' }
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
