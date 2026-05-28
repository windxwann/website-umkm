@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<!-- Hero Carousel Section - Compact & Modern -->
<div class="relative mb-8 sm:mb-12 overflow-hidden rounded-xl shadow-lg bg-gray-900"
     x-data="{ 
        activeSlide: 0, 
        slides: [
        @for($i = 1; $i <= 3; $i++)
            { 
                image: '{{ setting("banner{$i}_image") ? asset("storage/".setting("banner{$i}_image")) : asset("carousel_promo_seafood_1776694644555.png") }}', 
                title: '{{ setting("banner{$i}_title", ($i==1 ? "Seafood Feast" : ($i==2 ? "Masakan Sunda" : "Suasana Nyaman"))) }}', 
                desc: '{{ setting("banner{$i}_desc", ($i==1 ? "Nikmati kelezatan hasil laut segar." : ($i==2 ? "Rasakan kehangatan masakan rumah." : "Tempat terbaik untuk kumpul keluarga."))) }}',
                cta: '{{ $i == 1 ? "Lihat Menu Seafood" : ($i == 2 ? "Lihat Masakan Sunda" : "Tentang Kami") }}',
                link: '{{ setting("banner{$i}_link", ($i==1 ? route("menu")."?category=seafood" : ($i==2 ? route("menu")."?category=masakan-sunda" : route("about")))) }}'
            },
        @endfor
        ],
        next() { this.activeSlide = (this.activeSlide + 1) % this.slides.length },
        prev() { this.activeSlide = (this.activeSlide - 1 + this.slides.length) % this.slides.length },
        init() { setInterval(() => this.next(), 5000) }
     }">
    
    <!-- Slides -->
    <div class="relative h-[200px] sm:h-[280px] md:h-[320px]">
        <template x-for="(slide, index) in slides" :key="index">
            <div x-show="activeSlide === index" 
                 x-transition.opacity.duration.500ms
                 class="absolute inset-0">
                <img :src="slide.image" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/50"></div>
                
                <!-- Content - Centered -->
                <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-6">
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white mb-2" x-text="slide.title"></h1>
                    <p class="text-sm sm:text-base text-gray-200 mb-4 max-w-md px-4" x-text="slide.desc"></p>
                    <a :href="slide.link" 
                       class="bg-orange-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-orange-700 transition shadow-md text-sm sm:text-base"
                       x-text="slide.cta">
                    </a>
                </div>
            </div>
        </template>
    </div>

    <!-- Navigation Buttons - Sejajar vertical di tengah -->
    <button @click="prev()" class="absolute left-3 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/60 text-white w-8 h-8 rounded-full transition backdrop-blur-sm flex items-center justify-center">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
    </button>
    <button @click="next()" class="absolute right-3 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/60 text-white w-8 h-8 rounded-full transition backdrop-blur-sm flex items-center justify-center">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
    </button>

    <!-- Simple Dot Indicators -->
    <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5">
        <template x-for="(slide, index) in slides" :key="index">
            <button @click="activeSlide = index" 
                    :class="activeSlide === index ? 'bg-white w-5' : 'bg-white/40 w-1.5'"
                    class="h-1.5 rounded-full transition-all duration-300"></button>
        </template>
    </div>
</div>

<!-- Categories Section -->
<div class="mb-12">
    <div class="text-center mb-6 sm:mb-8">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">Kategori Menu</h2>
        <p class="text-sm sm:text-base text-gray-500">Pilih kategori favorit Anda</p>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        @php
            $categories = App\Models\Category::withCount('products')->get();
        @endphp
        
        @forelse($categories as $category)
        <div class="group bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <!-- Category Image -->
            <div class="h-48 overflow-hidden">
                @if($category->image && file_exists(public_path('storage/'.$category->image)))
                    <img src="{{ asset('storage/'.$category->image) }}" 
                        alt="{{ $category->name }}"
                        class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                @elseif($category->image && filter_var($category->image, FILTER_VALIDATE_URL))
                    <img src="{{ $category->image }}" 
                        alt="{{ $category->name }}"
                        class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                @else
                    <div class="w-full h-full bg-gradient-to-br from-orange-100 to-orange-50 flex items-center justify-center">
                        <i class="fas fa-tag text-6xl text-orange-300"></i>
                    </div>
                @endif
            </div>
            
            <div class="p-5">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="text-xl font-bold text-gray-800">{{ $category->name }}</h3>
                    <span class="bg-orange-100 text-orange-600 text-xs font-semibold px-2 py-1 rounded-full">
                        {{ $category->products_count }} Menu
                    </span>
                </div>
                <p class="text-gray-500 text-sm mb-4">{{ Str::limit($category->description ?? 'Nikmati berbagai hidangan lezat dari kategori ini', 80) }}</p>
                <a href="{{ route('menu') }}?category={{ $category->slug }}" 
                   class="inline-flex items-center text-orange-600 hover:text-orange-700 font-medium group">
                    Lihat Menu 
                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <div class="bg-gray-50 rounded-2xl p-12">
                <i class="fas fa-tags text-5xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">Belum ada kategori menu</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Promo Section -->
<div class="bg-gradient-to-r from-gray-800 to-gray-900 rounded-2xl shadow-xl overflow-hidden mb-12">
    <div class="grid grid-cols-1 lg:grid-cols-2">
        <div class="p-6 sm:p-8 lg:p-12 text-white">
            <div class="inline-flex items-center bg-orange-500/20 rounded-full px-3 py-1 mb-3 sm:mb-4">
                <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                <span class="text-xs font-medium">PROMO SPESIAL</span>
            </div>
            <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold mb-3 sm:mb-4">Diskon 20% untuk Pembayaran via QRIS</h3>
            <p class="text-gray-300 mb-6">Gunakan e-wallet favoritmu (GoPay, OVO, Dana, LinkAja) dan dapatkan potongan harga spesial untuk setiap pembelian.</p>
            <div class="flex flex-wrap gap-4">
                <img src="https://i.pinimg.com/1200x/98/88/27/988827ba70ba45ec5fb9c36423d8d09e.jpg" 
                     alt="QRIS" class="h-8 object-contain rounded-lg">
                <img src="https://i.pinimg.com/736x/b1/a0/71/b1a071a263b1ceb1ceadd4798d56f531.jpg" 
                     alt="OVO" class="h-8 object-contain rounded-lg">
                <img src="https://i.pinimg.com/736x/fe/ce/b2/feceb2ca508603b06c2f7ba18a5d018d.jpg" 
                     alt="GoPay" class="h-8 object-contain rounded-lg">
                <img src="https://i.pinimg.com/736x/69/fb/e3/69fbe35965bbf72285337b8aabb3a466.jpg" 
                     alt="Dana" class="h-8 object-contain rounded-lg">
            </div>
        </div>
        <div class="relative h-64 lg:h-auto">
            <img src="https://i.pinimg.com/1200x/ee/d8/5f/eed85ffb68bd268117e9994af34fba34.jpg" 
                 alt="Promo" class="w-full h-full object-cover rounded-lg">
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent lg:bg-gradient-to-l"></div>
        </div>
    </div>
</div>

<!-- Info Section -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl shadow-md p-6 text-center hover:shadow-lg transition">
        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-utensils text-orange-600 text-2xl"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Menu Pilihan</h3>
        <p class="text-gray-500 text-sm">Berbagai pilihan menu lezat khas nusantara</p>
    </div>
    <div class="bg-white rounded-xl shadow-md p-6 text-center hover:shadow-lg transition">
        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-credit-card text-orange-600 text-2xl"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Bayar di Tempat</h3>
        <p class="text-gray-500 text-sm">Bayar langsung ke kasir atau via E-Wallet</p>
    </div>
    <div class="bg-white rounded-xl shadow-md p-6 text-center hover:shadow-lg transition">
        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-utensils text-orange-600 text-2xl"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Dine In</h3>
        <p class="text-gray-500 text-sm">Nikmati makanan langsung di tempat</p>
    </div>
</div>

@push('styles')
<style>
    .group-hover\:scale-110 {
        transition: transform 0.5s ease;
    }
</style>
@endpush
@endsection