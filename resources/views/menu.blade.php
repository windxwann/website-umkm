@extends('layouts.app')

@section('title', 'Menu')

@section('content')
<div class="container mx-auto px-3 sm:px-4 py-4 sm:py-8">
    <!-- Header -->
    <div class="text-center mb-6 sm:mb-8 md:mb-12">
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-orange-600 mb-2 sm:mb-4">Menu Dapoer Cemal Cemil Jiemas</h1>
        <p class="text-sm sm:text-base md:text-lg text-gray-600">Nikmati berbagai hidangan lezat pilihan kami</p>
    </div>

    <!-- Category Filter - Improved responsive horizontal scroll -->
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
        <!-- Gradient shadow indicator for scroll -->
        <div class="absolute right-0 top-0 bottom-0 w-8 bg-gradient-to-l from-gray-50 to-transparent pointer-events-none md:hidden"></div>
    </div>

    <!-- Menu Grid - Improved responsive grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-3 sm:gap-4 md:gap-6" id="menuGrid">
        @forelse($categories as $category)
            @foreach($category->products as $product)
            <div class="menu-item bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden flex flex-col"
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
                    
                    <!-- Badge status -->
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

<!-- Cart Modal - Improved responsive -->
<div id="cartModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-all duration-300 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto transform transition-all duration-300 scale-95 max-h-[90vh] flex flex-col" id="cartModalContent">
        <div class="p-3 sm:p-4 border-b flex justify-between items-center bg-gradient-to-r from-orange-600 to-orange-500 text-white rounded-t-lg">
            <h2 class="text-base sm:text-lg md:text-xl font-semibold flex items-center">
                <i class="fas fa-shopping-cart mr-2"></i>
                <span class="hidden sm:inline">Keranjang Belanja</span>
                <span class="sm:hidden">Keranjang</span>
            </h2>
            <button onclick="closeCart()" class="hover:text-gray-200 transition-colors p-1">
                <i class="fas fa-times text-xl sm:text-2xl"></i>
            </button>
        </div>
        
        <div class="p-3 sm:p-4 overflow-y-auto flex-grow" id="cartItems">
            <!-- Cart items will be loaded here -->
        </div>
        
        <div class="p-3 sm:p-4 border-t bg-gray-50 flex flex-col gap-2">
            <div class="flex justify-between items-center text-xs sm:text-sm">
                <span class="text-gray-600">Total Item:</span>
                <span class="font-semibold" id="totalItems">0</span>
            </div>
            <div class="flex justify-between mb-2">
                <span class="font-semibold text-gray-700 text-sm sm:text-base">Total Harga:</span>
                <span class="font-bold text-orange-600 text-base sm:text-lg md:text-xl" id="cartTotal">Rp 0</span>
            </div>
            
            <div class="flex gap-2 mt-2">
                <button onclick="closeCart()" 
                        class="flex-1 bg-gray-500 text-white py-2 sm:py-3 rounded-lg hover:bg-gray-600 transition-colors text-sm font-medium">
                    Lanjut
                </button>
                <button onclick="proceedToCheckout()" 
                        class="flex-[2] bg-orange-600 text-white py-2 sm:py-3 rounded-lg hover:bg-orange-700 transition-colors font-semibold text-sm" 
                        id="checkoutBtn">
                    Checkout (0)
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Notification Toast - Mobile friendly -->
<div id="notificationToast" class="fixed bottom-4 left-4 right-4 sm:left-auto sm:right-4 sm:bottom-4 bg-green-600 text-white px-4 sm:px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-y-20 opacity-0 z-50 flex items-center justify-center sm:justify-start">
    <i class="fas fa-check-circle mr-2 text-sm sm:text-base"></i>
    <span id="notificationMessage" class="text-sm sm:text-base">Produk ditambahkan ke keranjang</span>
</div>

@push('styles')
<style>
    /* Hide scrollbar for filter */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    
    /* Menu item animations */
    .menu-item {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .filter-btn.active {
        background-color: #f97316 !important;
        color: white !important;
    }
    
    /* Button pulse animation */
    .btn-pulse {
        animation: pulse 0.3s ease;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    /* Cart animations */
    .cart-item {
        transition: all 0.3s ease;
    }
    
    .cart-item-remove {
        animation: slideOut 0.3s ease forwards;
    }
    
    @keyframes slideOut {
        to {
            opacity: 0;
            transform: translateX(20px);
        }
    }
    
    /* Cart badge */
    .cart-count-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background-color: #f97316;
        color: white;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        font-size: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Line clamp utilities */
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
    
    /* Quantity button */
    .quantity-btn:active {
        transform: scale(0.95);
    }
    
    /* Touch-friendly targets */
    @media (max-width: 640px) {
        button, 
        .filter-btn,
        .add-to-cart-btn,
        .quantity-btn {
            min-height: 36px;
        }
    }
    
    /* Modal backdrop */
    #cartModal.show {
        display: flex;
    }
    
    #cartModal.show #cartModalContent {
        transform: scale(1);
    }
    
    /* Scale animation */
    .scale-125 {
        transform: scale(1.25);
    }
</style>
@endpush

@push('scripts')
<script>
// Cart functionality
window.cart = [];

// Load cart from localStorage
function loadCart() {
    const savedCart = localStorage.getItem('cart');
    if (savedCart) {
        try {
            window.cart = JSON.parse(savedCart);
        } catch (e) {
            console.error('Error loading cart:', e);
            window.cart = [];
        }
    } else {
        window.cart = [];
    }
    updateCartCount();
}

// Save cart to localStorage
function saveCart() {
    localStorage.setItem('cart', JSON.stringify(window.cart));
}

// Filter menu by category
function filterMenu(category, button) {
    // Update active button
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('bg-orange-600', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });
    
    button.classList.remove('bg-gray-200', 'text-gray-700');
    button.classList.add('bg-orange-600', 'text-white');
    
    // Filter items
    const items = document.querySelectorAll('.menu-item');
    items.forEach(item => {
        if (category === 'all' || item.dataset.category === category) {
            item.style.display = 'flex';
            setTimeout(() => {
                item.style.opacity = '1';
                item.style.transform = 'scale(1)';
            }, 10);
        } else {
            item.style.opacity = '0';
            item.style.transform = 'scale(0.8)';
            setTimeout(() => {
                item.style.display = 'none';
            }, 300);
        }
    });
}

// Add to cart function
window.addToCart = function(id, name, price, button) {
    loadCart();
    
    const existingItemIndex = window.cart.findIndex(item => item.id === id);
    
    if (existingItemIndex !== -1) {
        window.cart[existingItemIndex].quantity += 1;
    } else {
        window.cart.push({
            id: id,
            name: name,
            price: price,
            quantity: 1
        });
    }
    
    saveCart();
    updateCartCount();
    showNotification(name + ' ditambahkan ke keranjang', 'success');
    
    if (button) {
        button.classList.add('btn-pulse');
        setTimeout(() => {
            button.classList.remove('btn-pulse');
        }, 300);
    }
}

// Update cart count in navbar
function updateCartCount() {
    const totalItems = window.cart.reduce((total, item) => total + item.quantity, 0);
    
    const cartIcon = document.querySelector('.fa-shopping-cart')?.parentElement;
    if (cartIcon) {
        const oldBadge = cartIcon.querySelector('.cart-count-badge');
        if (oldBadge) oldBadge.remove();
        
        if (totalItems > 0) {
            const badge = document.createElement('span');
            badge.className = 'cart-count-badge';
            badge.textContent = totalItems > 99 ? '99+' : totalItems;
            cartIcon.style.position = 'relative';
            cartIcon.appendChild(badge);
        }
    }
    
    const totalItemsElement = document.getElementById('totalItems');
    if (totalItemsElement) totalItemsElement.textContent = totalItems;
    
    updateCheckoutButton();
}

// Update checkout button
function updateCheckoutButton() {
    const checkoutBtn = document.getElementById('checkoutBtn');
    const totalItems = window.cart.reduce((total, item) => total + item.quantity, 0);
    
    if (checkoutBtn) {
        if (window.cart.length === 0) {
            checkoutBtn.classList.add('opacity-50', 'pointer-events-none');
            checkoutBtn.innerHTML = 'Checkout (0)';
        } else {
            checkoutBtn.classList.remove('opacity-50', 'pointer-events-none');
            checkoutBtn.innerHTML = `Checkout (${totalItems})`;
        }
    }
}

// Show notification toast
function showNotification(message, type = 'success') {
    const toast = document.getElementById('notificationToast');
    const messageEl = document.getElementById('notificationMessage');
    
    toast.className = `fixed bottom-4 left-4 right-4 sm:left-auto sm:right-4 sm:bottom-4 px-4 sm:px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 z-50 flex items-center justify-center sm:justify-start ${
        type === 'success' ? 'bg-green-600' : 'bg-red-600'
    } text-white`;
    
    messageEl.textContent = message;
    
    toast.classList.remove('translate-y-20', 'opacity-0');
    toast.classList.add('translate-y-0', 'opacity-100');
    
    setTimeout(() => {
        toast.classList.add('translate-y-20', 'opacity-0');
        toast.classList.remove('translate-y-0', 'opacity-100');
    }, 2500);
}

// Proceed to checkout
window.proceedToCheckout = function() {
    if (window.cart.length === 0) {
        showNotification('Keranjang belanja kosong!', 'error');
        return;
    }
    
    const checkoutBtn = document.getElementById('checkoutBtn');
    const originalText = checkoutBtn.innerHTML;
    checkoutBtn.disabled = true;
    checkoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
    
    fetch('{{ route("cart.save-to-session") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ cart: window.cart })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification('Mengalihkan ke halaman pemesanan...', 'success');
            setTimeout(() => {
                window.location.href = '{{ route("order.create") }}';
            }, 1000);
        } else {
            showNotification('Gagal menyimpan keranjang: ' + data.message, 'error');
            checkoutBtn.disabled = false;
            checkoutBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan saat menyimpan keranjang', 'error');
        checkoutBtn.disabled = false;
        checkoutBtn.innerHTML = originalText;
    });
}

// Open cart modal
window.openCart = function() {
    loadCart();
    
    const modal = document.getElementById('cartModal');
    const cartItems = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');
    
    if (window.cart.length === 0) {
        cartItems.innerHTML = `
            <div class="text-center py-8 sm:py-12">
                <i class="fas fa-shopping-cart text-5xl sm:text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-base sm:text-lg">Keranjang belanja kosong</p>
                <p class="text-gray-400 text-xs sm:text-sm mt-2">Silakan pilih menu yang tersedia</p>
            </div>
        `;
        cartTotal.textContent = 'Rp 0';
    } else {
        let html = '';
        let total = 0;
        let totalItems = 0;
        
        window.cart.forEach((item) => {
            const subtotal = item.price * item.quantity;
            total += subtotal;
            totalItems += item.quantity;
            
            html += `
                <div class="cart-item flex flex-col sm:flex-row justify-between items-start sm:items-center mb-3 pb-3 border-b last:border-0 hover:bg-gray-50 p-2 rounded-lg transition-colors" data-item-id="${item.id}">
                    <div class="flex-1 w-full sm:w-auto mb-2 sm:mb-0">
                        <h4 class="font-semibold text-gray-800 text-sm sm:text-base">${item.name}</h4>
                        <p class="text-xs sm:text-sm text-gray-500">Rp ${formatPrice(item.price)}</p>
                        <div class="flex items-center mt-2">
                            <button onclick="updateQuantity(${item.id}, -1, this)" 
                                    class="quantity-btn w-7 h-7 sm:w-8 sm:h-8 bg-gray-200 rounded-full hover:bg-gray-300 transition-colors flex items-center justify-center">
                                <i class="fas fa-minus text-xs"></i>
                            </button>
                            <span class="mx-2 sm:mx-3 font-semibold w-6 text-center text-sm">${item.quantity}</span>
                            <button onclick="updateQuantity(${item.id}, 1, this)" 
                                    class="quantity-btn w-7 h-7 sm:w-8 sm:h-8 bg-gray-200 rounded-full hover:bg-gray-300 transition-colors flex items-center justify-center">
                                <i class="fas fa-plus text-xs"></i>
                            </button>
                        </div>
                    </div>
                    <div class="text-right w-full sm:w-auto">
                        <p class="font-bold text-orange-600 text-sm sm:text-base">Rp ${formatPrice(subtotal)}</p>
                        <button onclick="removeFromCart(${item.id}, this)" 
                                class="text-red-500 hover:text-red-700 text-xs mt-1 transition-colors">
                            <i class="fas fa-trash-alt mr-1"></i>Hapus
                        </button>
                    </div>
                </div>
            `;
        });
        
        cartItems.innerHTML = html;
        cartTotal.textContent = 'Rp ' + formatPrice(total);
        document.getElementById('totalItems').textContent = totalItems;
    }
    
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

// Close cart modal
window.closeCart = function() {
    const modal = document.getElementById('cartModal');
    modal.classList.remove('show');
    document.body.style.overflow = 'auto';
}

// Update quantity
window.updateQuantity = function(productId, change, button) {
    loadCart();
    
    const itemIndex = window.cart.findIndex(item => item.id === productId);
    
    if (itemIndex !== -1) {
        window.cart[itemIndex].quantity += change;
        
        if (window.cart[itemIndex].quantity <= 0) {
            window.cart.splice(itemIndex, 1);
            showNotification('Item dihapus dari keranjang', 'success');
        }
        
        saveCart();
        updateCartCount();
        openCart();
    }
}

// Remove from cart
window.removeFromCart = function(productId, button) {
    loadCart();
    
    const itemElement = document.querySelector(`[data-item-id="${productId}"]`);
    if (itemElement) {
        itemElement.classList.add('cart-item-remove');
    }
    
    setTimeout(() => {
        window.cart = window.cart.filter(item => item.id !== productId);
        saveCart();
        updateCartCount();
        
        if (window.cart.length === 0) {
            closeCart();
            showNotification('Keranjang belanja kosong', 'success');
        } else {
            openCart();
        }
    }, 200);
}

// Format price
function formatPrice(price) {
    return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadCart();
    
    const cartIcon = document.querySelector('.fa-shopping-cart')?.parentElement;
    if (cartIcon) {
        cartIcon.addEventListener('click', function(e) {
            e.preventDefault();
            openCart();
        });
    }
});

// Close modal when clicking outside
document.getElementById('cartModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeCart();
});

// Prevent closing when clicking inside modal
document.getElementById('cartModalContent')?.addEventListener('click', function(e) {
    e.stopPropagation();
});

// ESC key to close modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('cartModal');
        if (modal.classList.contains('show')) closeCart();
    }
});
</script>
@endpush
@endsection