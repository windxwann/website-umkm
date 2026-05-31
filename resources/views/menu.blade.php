@extends('layouts.app')

@section('title', 'Menu')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2">Menu Kami</h1>
        <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">Pilih hidangan favorit Anda</p>
    </div>

    <!-- Category Filter -->
    <div class="grid grid-cols-3 md:flex md:justify-center gap-2 mb-10" id="filterContainer">
        <button onclick="filterMenu('all', this)" 
                class="filter-btn col-span-3 md:w-auto px-6 py-2.5 rounded-2xl bg-slate-900 text-white transition-all shadow-lg text-[10px] font-black uppercase tracking-widest">
            Semua
        </button>
        @foreach($categories as $category)
        <button onclick="filterMenu('{{ $category->slug }}', this)" 
                class="filter-btn md:w-auto px-4 py-2.5 rounded-2xl bg-white border border-slate-100 text-slate-600 hover:border-orange-500 hover:text-orange-600 transition-all shadow-sm text-[10px] font-black uppercase tracking-widest truncate">
            {{ $category->name }}
        </button>
        @endforeach
    </div>

    <!-- Menu Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4" id="menuGrid">
        @forelse($categories as $category)
            @foreach($category->products as $product)
            <div class="menu-item bg-white rounded-3xl shadow-sm border border-slate-100 hover:shadow-lg transition-all duration-300 flex flex-col"
                 data-category="{{ $category->slug }}">
                
                {{-- Gambar Menu --}}
                <div class="relative w-full aspect-video overflow-hidden rounded-t-3xl">
                    @if($product->image)
                        @php
                            $imagePath = $product->image;
                            if (!str_contains($imagePath, 'products/')) {
                                $imagePath = 'products/' . $imagePath;
                            }
                        @endphp
                        <img src="{{ asset('storage/' . $imagePath) }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-full object-cover"
                             loading="lazy">
                    @else
                        <div class="w-full h-full bg-slate-50 flex items-center justify-center">
                            <i class="fas fa-utensils text-2xl text-slate-200"></i>
                        </div>
                    @endif
                </div>
                
                <div class="p-4 sm:p-6 flex-grow flex flex-col">
                    <div class="mb-2">
                        <h3 class="text-xs sm:text-sm font-black text-slate-900 tracking-tight line-clamp-1">{{ $product->name }}</h3>
                        <span class="text-orange-600 font-black text-[10px] sm:text-xs">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </span>
                    </div>
                    
                    <p class="text-[9px] sm:text-[11px] font-medium text-slate-400 mb-4 line-clamp-2">
                        {{ Str::limit($product->description ?? 'Tidak ada deskripsi', 50) }}
                    </p>
                    
                    @if($product->is_available)
                    <button onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, this)"
                            class="mt-auto w-full bg-slate-50 text-slate-900 py-2.5 rounded-xl hover:bg-orange-600 hover:text-white transition-all font-black text-[9px] uppercase tracking-widest">
                        Tambah
                    </button>
                    @else
                    <button disabled class="mt-auto w-full bg-slate-100 text-slate-400 py-2.5 rounded-xl cursor-not-allowed font-black text-[9px] uppercase tracking-widest">
                        Habis
                    </button>
                    @endif
                </div>
            </div>
            @endforeach
        @empty
            <div class="col-span-full text-center py-20">
                <p class="text-sm font-black text-slate-400 uppercase tracking-widest">Belum ada menu</p>
            </div>
        @endforelse
    </div>
</div>

@push('styles')
<style>
    .menu-item {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .filter-btn.active {
        background-color: #f97316 !important;
        color: white !important;
        border-color: #f97316 !important;
    }
    .btn-pulse {
        animation: pulse 0.3s ease;
    }
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
</style>
@endpush

@push('scripts')
<script>
// Filter menu
function filterMenu(category, button) {
    var allButtons = document.querySelectorAll('.filter-btn');
    allButtons.forEach(btn => {
        btn.classList.remove('bg-slate-900', 'text-white');
        btn.classList.add('bg-white', 'text-slate-600', 'border', 'border-slate-100');
    });
    
    button.classList.add('bg-slate-900', 'text-white');
    button.classList.remove('bg-white', 'text-slate-600', 'border', 'border-slate-100');
    
    var items = document.querySelectorAll('.menu-item');
    items.forEach(item => {
        if (category === 'all' || item.dataset.category === category) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
}
// ... (sisa script tetap sama)
</script>
@endpush
@endsection