@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<!-- Welcome Greeting Hero Section -->
<div class="mb-10 md:mb-12 rounded-[1.5rem] md:rounded-[2.5rem] p-6 md:p-12 border border-slate-100 shadow-sm relative overflow-hidden bg-slate-900 min-h-[280px] md:min-h-[380px] flex items-center">
    @if(setting('banner1_image'))
        <img src="{{ asset('storage/' . setting('banner1_image')) }}" alt="Background" class="absolute inset-0 w-full h-full object-cover opacity-60">
    @else
        <div class="absolute inset-0 bg-slate-900 opacity-60"></div>
    @endif
    
    <!-- Dark Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent md:bg-slate-900/60"></div>

    <div class="relative z-10 max-w-xl w-full">
        <div class="inline-flex items-center bg-orange-600 rounded-xl px-3 py-1.5 md:px-4 md:py-2 mb-4 md:mb-5 text-[8px] md:text-[9px] font-black text-white uppercase tracking-widest">
            <i class="fas fa-utensils mr-2 text-white"></i>
            Selamat Datang di {{ setting('restaurant_name', 'Dapoer Jiemas') }}
        </div>
        <h1 class="text-xl sm:text-3xl md:text-5xl font-black text-white leading-[1.1] tracking-tighter mb-3 md:mb-4">
            {{ setting('banner1_title', 'Nikmati Kelezatan Autentik Nusantara.') }}
        </h1>
        <p class="text-xs sm:text-sm md:text-base font-medium text-slate-200 mb-6 md:mb-8 leading-relaxed max-w-sm">
            {{ setting('banner1_desc', 'Sajian hidangan lezat dengan bumbu rempah pilihan yang diracik khusus untuk memanjakan lidah Anda dan keluarga.') }}
        </p>
        <div class="flex flex-row flex-wrap gap-3 md:gap-4">
            <a href="{{ route('menu') }}" class="bg-white text-slate-900 px-5 py-2.5 md:px-8 md:py-3.5 rounded-xl md:rounded-2xl font-black hover:bg-orange-600 hover:text-white transition-all shadow-xl shadow-slate-900/10 text-[9px] md:text-[10px] uppercase tracking-widest whitespace-nowrap">
                Lihat Menu & Pesan
            </a>
            <a href="{{ route('about') }}" class="bg-white/10 border border-white/20 text-white px-5 py-2.5 md:px-8 md:py-3.5 rounded-xl md:rounded-2xl font-black hover:bg-white/20 transition-all text-[9px] md:text-[10px] uppercase tracking-widest backdrop-blur-md whitespace-nowrap">
                Tentang Kami
            </a>
        </div>
    </div>
</div>

<!-- Categories Section -->
<div class="mb-16">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Kategori Menu</h2>
        <a href="{{ route('menu') }}" class="text-[10px] font-black text-orange-600 uppercase tracking-widest hover:text-orange-700">Lihat Semua</a>
    </div>
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        @foreach($categories as $category)
        @php
            $iconMap = [
                'seafood' => 'fas fa-fish',
                'minuman' => 'fas fa-coffee',
                'snack' => 'fas fa-cookie',
                'dessert' => 'fas fa-ice-cream',
                'utama' => 'fas fa-bowl-rice',
                'sunda' => 'fas fa-leaf',
                'ayam' => 'fas fa-drumstick-bite',
                'mie' => 'fas fa-bowl-hot',
            ];
            $slug = strtolower($category->slug);
            $icon = 'fas fa-utensils';
            foreach ($iconMap as $key => $faClass) {
                if (str_contains($slug, $key)) {
                    $icon = $faClass;
                    break;
                }
            }
        @endphp
        <a href="{{ route('menu') }}?category={{ $category->slug }}" class="group bg-slate-50 p-6 rounded-[2rem] shadow-sm border border-slate-200 hover:shadow-lg hover:bg-white transition-all text-center">
            <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-orange-50 transition border border-slate-100">
                <i class="{{ $icon }} text-orange-600 text-xl"></i>
            </div>
            <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest">{{ $category->name }}</h3>
        </a>
        @endforeach
    </div>
</div>

<!-- Featured Products Section -->
<div class="mb-16">
    <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-8">Menu Favorit</h2>
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
        @foreach($products as $product)
            <div class="menu-item bg-white rounded-3xl shadow-sm border border-slate-100 hover:shadow-lg transition-all duration-300 flex flex-col">
                {{-- Gambar Menu --}}
                <div class="relative w-full aspect-video overflow-hidden rounded-t-3xl">
                    @php
                        $prodImagePath = $product->image;
                        if ($prodImagePath && !str_contains($prodImagePath, 'products/')) {
                            $prodImagePath = 'products/' . $prodImagePath;
                        }
                    @endphp
                    <img src="{{ $product->image ? asset('storage/' . $prodImagePath) : asset('default-product.png') }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-full object-cover">
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
                    
                    <button onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, this)"
                            class="mt-auto w-full bg-slate-50 text-slate-900 py-2.5 rounded-xl hover:bg-orange-600 hover:text-white transition-all font-black text-[9px] uppercase tracking-widest">
                        Tambah
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
