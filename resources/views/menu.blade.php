@extends('layouts.app')

@section('title', 'Menu')

@section('content')
<div class="container mx-auto px-3 sm:px-4 py-4 sm:py-8">
    <!-- Header -->
    <div class="text-center mb-6 sm:mb-8 md:mb-12">
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-orange-600 mb-2 sm:mb-4">Menu Dapoer Cemal Cemil Jiemas</h1>
        <p class="text-sm sm:text-base md:text-lg text-gray-600">Nikmati berbagai hidangan lezat pilihan kami</p>
    </div>

    <!-- Category Filter -->
    <div class="relative mb-6 sm:mb-8">
        <div class="flex flex-nowrap md:flex-wrap justify-start md:justify-center gap-2 sm:gap-3 overflow-x-auto pb-3 md:pb-0 no-scrollbar -mx-3 px-3 sm:mx-0 sm:px-0" 
             id="filterContainer">
            <button onclick="filterMenu('all', this)" 
                    class="filter-btn px-3 sm:px-5 py-1.5 sm:py-2 rounded-full bg-orange-600 text-white hover:bg-orange-700 transition-all whitespace-nowrap shadow-sm text-xs sm:text-sm font-medium">
                Semua
            </button>
            @foreach($categories as $category)
            <button onclick="filterMenu('{{ $category->slug }}', this)" 
                    class="filter-btn px-3 sm:px-5 py-1.5 sm:py-2 rounded-full bg-gray-200 text-gray-700 hover:bg-orange-600 hover:text-white transition-all whitespace-nowrap shadow-sm text-xs sm:text-sm font-medium">
                {{ $category->name }}
            </button>
            @endforeach
        </div>
        <div class="absolute right-0 top-0 bottom-0 w-8 bg-gradient-to-l from-gray-50 to-transparent pointer-events-none md:hidden"></div>
    </div>

    <!-- Menu Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-3 sm:gap-4 md:gap-6" id="menuGrid">
        @forelse($categories as $category)
            @foreach($category->products as $product)
            <div class="menu-item bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden flex flex-col"
                 data-category="{{ $category->slug }}">
                
                {{-- Gambar Menu --}}
                <div class="relative overflow-hidden bg-gray-100" style="height: 180px; max-height: 200px;">
                    @if($product->image)
                        @php
                            $imagePath = $product->image;
                            if (!str_contains($imagePath, 'products/')) {
                                $imagePath = 'products/' . $imagePath;
                            }
                        @endphp
                        <img src="{{ asset('storage/' . $imagePath) }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
                             loading="lazy"
                             onerror="this.onerror=null; this.src='https://via.placeholder.com/400x200?text=Gambar+Tidak+Tersedia'; this.classList.add('opacity-50');">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                            <i class="fas fa-utensils text-4xl sm:text-5xl text-gray-400"></i>
                        </div>
                    @endif
                    
                    @if(!$product->is_available)
                    <div class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full font-semibold">
                        Habis
                    </div>
                    @endif
                </div>
                
                <div class="p-3 sm:p-4 flex-grow flex flex-col">
                    <div class="flex justify-between items-start gap-2 mb-2">
                        <h3 class="text-base sm:text-lg md:text-xl font-semibold text-gray-800 line-clamp-1 flex-1">{{ $product->name }}</h3>
                        <span class="text-orange-600 font-bold text-sm sm:text-base whitespace-nowrap">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </span>
                    </div>
                    
                    <p class="text-gray-600 text-xs sm:text-sm mb-3 line-clamp-2 flex-grow">
                        {{ Str::limit($product->description ?? 'Tidak ada deskripsi', 80) }}
                    </p>
                    
                    <div class="flex justify-between items-center mt-auto pt-2">
                        <span class="text-xs sm:text-sm {{ $product->is_available ? 'text-green-600' : 'text-red-600' }}">
                            <i class="fas {{ $product->is_available ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                            <span class="hidden sm:inline">{{ $product->is_available ? 'Tersedia' : 'Habis' }}</span>
                        </span>
                        
                        @if($product->is_available)
                        <button onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, this)"
                                class="add-to-cart-btn bg-orange-600 text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg hover:bg-orange-700 transition-all transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-opacity-50 text-xs sm:text-sm font-medium">
                            <i class="fas fa-cart-plus sm:mr-2"></i>
                            <span class="hidden sm:inline">Tambah</span>
                        </button>
                        @else
                        <button disabled class="bg-gray-400 text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg cursor-not-allowed text-xs sm:text-sm">
                            <i class="fas fa-ban sm:mr-2"></i>
                            <span class="hidden sm:inline">Habis</span>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        @empty
            <div class="col-span-full text-center py-12 sm:py-16 md:py-20">
                <i class="fas fa-utensils text-5xl sm:text-6xl text-gray-300 mb-4"></i>
                <p class="text-lg sm:text-xl text-gray-500">Belum ada menu tersedia</p>
                @auth
                    @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.products.create') }}" class="inline-block mt-4 bg-orange-600 text-white px-5 sm:px-6 py-2.5 sm:py-3 rounded-lg hover:bg-orange-700 text-sm sm:text-base">
                        <i class="fas fa-plus mr-2"></i>Tambah Menu
                    </a>
                    @endif
                @endauth
            </div>
        @endforelse
    </div>
</div>

@push('styles')
<style>
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    
    .menu-item {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .filter-btn.active {
        background-color: #f97316 !important;
        color: white !important;
    }
    
    .btn-pulse {
        animation: pulse 0.3s ease;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
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
    
    @media (max-width: 640px) {
        button, 
        .filter-btn,
        .add-to-cart-btn {
            min-height: 36px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
// ============================================
// CART SYSTEM - TERINTEGRASI DENGAN LAYOUT
// ============================================

// Key yang sama dengan layout
var CART_STORAGE_KEY = 'restaurant_cart';
window.cart = [];

// Load cart dari localStorage
function loadCart() {
    try {
        var cartData = localStorage.getItem(CART_STORAGE_KEY);
        if (cartData) {
            window.cart = JSON.parse(cartData);
        } else {
            window.cart = [];
        }
    } catch(e) {
        console.error('Error loading cart:', e);
        window.cart = [];
    }
    updateCartBadge();
}

// Save cart ke localStorage
function saveCart() {
    localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(window.cart));
    updateCartBadge();
    
    // Trigger event untuk sync
    window.dispatchEvent(new StorageEvent('storage', {
        key: CART_STORAGE_KEY,
        newValue: JSON.stringify(window.cart)
    }));
}

// Clear cart (setelah checkout)
function clearCart() {
    window.cart = [];
    saveCart();
    console.log('Cart cleared after checkout');
}

// Update badge cart di navbar
function updateCartBadge() {
    var total = 0;
    for (var i = 0; i < window.cart.length; i++) {
        total += window.cart[i].quantity || 0;
    }
    
    // Gunakan fungsi global dari layout jika ada
    if (typeof window.globalUpdateCartCount === 'function') {
        window.globalUpdateCartCount(total);
    } else {
        // Fallback: update langsung
        var cartCountDesktop = document.getElementById('cartCount');
        var cartCountMobile = document.getElementById('cartCountMobile');
        
        if (cartCountDesktop) cartCountDesktop.textContent = total || 0;
        if (cartCountMobile) cartCountMobile.textContent = total || 0;
    }
}

// Filter menu
function filterMenu(category, button) {
    var allButtons = document.querySelectorAll('.filter-btn');
    for (var i = 0; i < allButtons.length; i++) {
        allButtons[i].classList.remove('bg-orange-600', 'text-white');
        allButtons[i].classList.add('bg-gray-200', 'text-gray-700');
    }
    
    button.classList.remove('bg-gray-200', 'text-gray-700');
    button.classList.add('bg-orange-600', 'text-white');
    
    var items = document.querySelectorAll('.menu-item');
    for (var i = 0; i < items.length; i++) {
        var item = items[i];
        if (category === 'all' || item.dataset.category === category) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    }
}

// Auto-filter berdasarkan query parameter URL (?category=slug)
function applyUrlCategoryFilter() {
    var urlParams = new URLSearchParams(window.location.search);
    var categorySlug = urlParams.get('category');
    
    if (!categorySlug || categorySlug === 'all') return;
    
    // Cari tombol filter yang cocok
    var allButtons = document.querySelectorAll('.filter-btn');
    for (var i = 0; i < allButtons.length; i++) {
        var btn = allButtons[i];
        // Ambil slug dari atribut onclick
        var onclickAttr = btn.getAttribute('onclick') || '';
        if (onclickAttr.indexOf("'" + categorySlug + "'") !== -1) {
            filterMenu(categorySlug, btn);
            // Scroll halus ke grid menu
            var menuGrid = document.getElementById('menuGrid');
            if (menuGrid) {
                setTimeout(function() {
                    menuGrid.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 100);
            }
            return;
        }
    }
}

// Add to cart
window.addToCart = function(id, name, price, button) {
    loadCart();
    
    var existingIndex = -1;
    for (var i = 0; i < window.cart.length; i++) {
        if (window.cart[i].id == id) {
            existingIndex = i;
            break;
        }
    }
    
    if (existingIndex !== -1) {
        window.cart[existingIndex].quantity++;
    } else {
        window.cart.push({
            id: id,
            name: name,
            price: price,
            quantity: 1
        });
    }
    
    saveCart();
    
    // Notifikasi
    if (typeof window.showNotification === 'function') {
        window.showNotification('✅ ' + name + ' ditambahkan ke keranjang', 'success');
    } else {
        alert(name + ' ditambahkan ke keranjang');
    }
    
    // Animasi tombol
    if (button) {
        button.classList.add('btn-pulse');
        setTimeout(function() {
            button.classList.remove('btn-pulse');
        }, 300);
    }
}

// Format price
function formatPrice(price) {
    return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Cek apakah ada pesanan yang baru saja dibuat (redirect dari checkout)
function checkOrderSuccess() {
    // Cek sessionStorage untuk tanda pesanan berhasil
    var orderSuccess = sessionStorage.getItem('order_success');
    if (orderSuccess === 'true') {
        // Hapus tanda
        sessionStorage.removeItem('order_success');
        // Kosongkan cart
        clearCart();
        // Tampilkan notifikasi
        if (typeof window.showNotification === 'function') {
            window.showNotification('✅ Pesanan berhasil! Keranjang telah dikosongkan', 'success');
        }
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadCart();
    checkOrderSuccess();
    applyUrlCategoryFilter(); // ← Auto-filter dari URL
    
    // Setup cart icon click (gunakan event listener langsung)
    var cartButtonDesktop = document.getElementById('cartButtonDesktop');
    var cartButtonMobile = document.getElementById('cartButtonMobile');
    
    if (cartButtonDesktop) {
        cartButtonDesktop.addEventListener('click', function(e) {
            e.preventDefault();
            if (typeof window.openCart === 'function') {
                window.openCart();
            }
        });
    }
    
    if (cartButtonMobile) {
        cartButtonMobile.addEventListener('click', function(e) {
            e.preventDefault();
            if (typeof window.openCart === 'function') {
                window.openCart();
            }
        });
    }
});

// Listen untuk perubahan cart dari tab lain
window.addEventListener('storage', function(e) {
    if (e.key === CART_STORAGE_KEY) {
        loadCart();
    }
});

// Expose clearCart ke global
window.clearCart = clearCart;
</script>
@endpush
@endsection