@extends('layouts.app')

@section('title', 'Buat Pesanan')

@section('content')
<div x-data="orderForm()" class="max-w-4xl mx-auto">
    <!-- Header dengan informasi -->
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-orange-600">Buat Pesanan</h1>
        <a href="{{ route('menu') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition flex items-center text-sm sm:text-base">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Menu
        </a>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-xl shadow-2xl">
            <div class="flex flex-col items-center">
                <div class="w-16 h-16 border-4 border-orange-200 border-t-orange-600 rounded-full animate-spin mb-4"></div>
                <p class="text-gray-700 font-medium">Memproses pesanan...</p>
                <p class="text-sm text-gray-500">Mohon tunggu sebentar</p>
            </div>
        </div>
    </div>

    <!-- Cart Info Banner -->
    <div x-show="form.items.length > 0" class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg mb-6">
        <div class="flex items-center">
            <i class="fas fa-shopping-cart text-green-600 text-xl mr-3"></i>
            <div>
                <p class="text-green-800 font-medium">
                    <span x-text="form.items.length"></span> item dalam pesanan
                </p>
                <p class="text-green-600 text-sm">Total: Rp <span x-text="formatPrice(total)"></span></p>
            </div>
        </div>
    </div>

    <form @submit.prevent="submitOrder" class="space-y-6">
        <!-- Customer Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 flex items-center">
                <i class="fas fa-user text-orange-600 mr-2"></i>
                Informasi Pelanggan
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           x-model="form.customer_name" 
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition"
                           placeholder="Masukkan nama Anda"
                           required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-phone text-gray-500 mr-1"></i>
                        No. Telepon
                    </label>
                    <input type="tel" 
                           x-model="form.customer_phone" 
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition"
                           placeholder="Contoh: 08123456789">
                </div>
            </div>
        </div>

        <!-- Order Type (Simplified to Only Offline) -->
        <input type="hidden" x-model="form.order_type" value="offline">

        <!-- Order Items Summary -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 flex items-center">
                <i class="fas fa-shopping-bag text-orange-600 mr-2"></i>
                Ringkasan Pesanan
            </h2>
            
            <!-- Items List -->
            <div class="space-y-3 mb-4">
                <template x-if="form.items.length === 0">
                    <div class="text-center py-8 bg-gray-50 rounded-lg">
                        <i class="fas fa-shopping-cart text-5xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">Belum ada item yang dipilih</p>
                        <a href="{{ route('menu') }}" class="text-orange-600 hover:text-orange-700 mt-2 inline-block">
                            <i class="fas fa-arrow-left mr-1"></i>Kembali ke Menu
                        </a>
                    </div>
                </template>
                
                <template x-for="(item, index) in form.items" :key="index">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-semibold" x-text="item.name"></p>
                            <p class="text-sm text-gray-500">
                                <span x-text="item.quantity"></span> x Rp <span x-text="formatPrice(item.price)"></span>
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-orange-600">Rp <span x-text="formatPrice(item.subtotal)"></span></p>
                            <button type="button" 
                                    @click="removeItem(index)"
                                    class="text-red-500 hover:text-red-700 text-sm">
                                <i class="fas fa-trash-alt mr-1"></i>Hapus
                            </button>
                        </div>
                    </div>
                </template>
            </div>
            
            <!-- Subtotal & Fees -->
            <div class="border-t pt-4 space-y-2">
                <div class="flex justify-between items-center text-gray-600">
                    <span>Subtotal</span>
                    <span>Rp <span x-text="formatPrice(subtotal)"></span></span>
                </div>
                
                <!-- No online fees -->

                <div class="flex justify-between items-center text-lg pt-2 border-t border-dashed">
                    <span class="font-bold">Total Pembayaran</span>
                    <span class="font-bold text-orange-600 text-2xl">
                        Rp <span x-text="formatPrice(total)"></span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Delivery Address (Only for Online) -->
        <!-- Removed Delivery Address -->

        <!-- Payment Method -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 flex items-center">
                <i class="fas fa-credit-card text-orange-600 mr-2"></i>
                Metode Pembayaran
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                <label class="flex items-center p-3 sm:p-4 border-2 rounded-lg cursor-pointer transition hover:border-orange-500"
                       :class="{'border-orange-500 bg-orange-50': form.payment_method === 'e_wallet', 'border-gray-200': form.payment_method !== 'e_wallet'}">
                    <input type="radio" 
                           x-model="form.payment_method" 
                           value="e_wallet" 
                           class="mr-3 w-4 h-4 text-orange-600 shrink-0">
                    <div>
                        <span class="font-semibold text-sm sm:text-base">📱 E-Wallet</span>
                        <p class="text-[10px] sm:text-xs text-gray-500">QRIS, OVO, Dana</p>
                    </div>
                </label>
                <label class="flex items-center p-3 sm:p-4 border-2 rounded-lg cursor-pointer transition hover:border-orange-500"
                       :class="{'border-orange-500 bg-orange-50': form.payment_method === 'cashier', 'border-gray-200': form.payment_method !== 'cashier'}">
                    <input type="radio" 
                           x-model="form.payment_method" 
                           value="cashier" 
                           class="mr-3 w-4 h-4 text-orange-600 shrink-0">
                    <div>
                        <span class="font-semibold text-sm sm:text-base">💰 Ke Kasir</span>
                        <p class="text-[10px] sm:text-xs text-gray-500">Tunai / Kartu</p>
                    </div>
                </label>
                <label class="flex items-center p-3 sm:p-4 border-2 rounded-lg cursor-pointer transition hover:border-orange-500"
                       :class="{'border-orange-500 bg-orange-50': form.payment_method === 'bank_transfer', 'border-gray-200': form.payment_method !== 'bank_transfer'}">
                    <input type="radio" 
                           x-model="form.payment_method" 
                           value="bank_transfer" 
                           class="mr-3 w-4 h-4 text-orange-600 shrink-0">
                    <div>
                        <span class="font-semibold text-sm sm:text-base">🏦 Transfer</span>
                        <p class="text-[10px] sm:text-xs text-gray-500">BCA, Mandiri</p>
                    </div>
                </label>
            </div>
        </div>

        <!-- Notes -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 flex items-center">
                <i class="fas fa-pen text-orange-600 mr-2"></i>
                Catatan Tambahan
            </h2>
            <textarea x-model="form.notes" 
                      rows="3" 
                      class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition"
                      placeholder="Contoh: Tidak pakai bawang, Level pedas sedang, dll..."></textarea>
        </div>

        <!-- Submit Button -->
        <div class="sticky bottom-4">
            <button type="submit" 
                    class="w-full bg-orange-600 text-white py-4 px-4 rounded-xl font-bold text-lg hover:bg-orange-700 transition shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="form.items.length === 0">
                <i class="fas fa-check-circle mr-2"></i>
                Buat Pesanan
                <span x-show="form.items.length > 0" class="ml-2 bg-white text-orange-600 px-2 py-1 rounded-full text-sm">
                    <span x-text="form.items.length"></span> item
                </span>
            </button>
        </div>
    </form>
</div>

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    
    .radio-card {
        transition: all 0.2s ease;
    }
    
    .radio-card:hover {
        transform: translateY(-2px);
    }
</style>
@endpush

@push('scripts')
<script>
function orderForm() {
    return {
        form: {
            customer_name: '',
            customer_phone: '',
            order_type: 'offline',
            payment_method: 'e_wallet',
            items: [],
            notes: ''
        },
        packagingFee: 0,
        quantities: {},
        
        // 🔥 INISIALISASI: Load cart dari session
        init() {
            // Ambil data cart dari session (dikirim dari controller)
            const cartItems = @json($cartItems ?? []);
            
            if (cartItems && cartItems.length > 0) {
                console.log('Loading cart from session:', cartItems);
                
                // Reset quantities
                this.quantities = {};
                
                // Load items ke form
                cartItems.forEach(item => {
                    this.quantities[item.id] = item.quantity;
                    this.form.items.push({
                        product_id: item.id,
                        quantity: item.quantity,
                        price: item.price,
                        name: item.name,
                        subtotal: item.price * item.quantity
                    });
                });
                
                // Tampilkan notifikasi
                this.showNotification(`${cartItems.length} item dimuat dari keranjang`, 'success');
            }
        },
        
        getQuantity(productId) {
            return this.quantities[productId] || 0;
        },
        
        increaseQuantity(productId, price, name) {
            this.quantities[productId] = (this.quantities[productId] || 0) + 1;
            this.updateItems(productId, price, name);
        },
        
        decreaseQuantity(productId) {
            if (this.quantities[productId] > 0) {
                this.quantities[productId]--;
                this.updateItems(productId);
            }
        },
        
        updateItems(productId, price, name) {
            const quantity = this.quantities[productId];
            this.form.items = this.form.items.filter(item => item.product_id !== productId);
            
            if (quantity > 0) {
                this.form.items.push({
                    product_id: productId,
                    quantity: quantity,
                    price: price,
                    name: name,
                    subtotal: price * quantity
                });
            }
            
            // Urutkan items berdasarkan product_id
            this.form.items.sort((a, b) => a.product_id - b.product_id);
        },
        
        // 🔥 FUNGSI BARU: Hapus item dari form
        removeItem(index) {
            const item = this.form.items[index];
            if (item) {
                // Hapus dari quantities
                delete this.quantities[item.product_id];
                // Hapus dari form.items
                this.form.items.splice(index, 1);
                this.showNotification(`${item.name} dihapus dari pesanan`, 'info');
            }
        },
        
        get subtotal() {
            return this.form.items.reduce((sum, item) => sum + item.subtotal, 0);
        },
        
        get total() {
            return this.subtotal;
        },
        
        formatPrice(price) {
            return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        },
        
        // 🔥 FUNGSI BARU: Show notification
        showNotification(message, type = 'success') {
            // Cek apakah sudah ada toast
            let toast = document.getElementById('notificationToast');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'notificationToast';
                toast.className = 'fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg transform transition-all duration-500 z-50';
                document.body.appendChild(toast);
            }
            
            const bgColor = type === 'success' ? 'bg-green-500' : (type === 'error' ? 'bg-red-500' : 'bg-blue-500');
            toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg transform transition-all duration-500 z-50 ${bgColor} text-white flex items-center`;
            
            let icon = type === 'success' ? 'fa-check-circle' : (type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle');
            toast.innerHTML = `<i class="fas ${icon} mr-2"></i>${message}`;
            
            toast.style.transform = 'translateX(0)';
            toast.style.opacity = '1';
            
            setTimeout(() => {
                toast.style.transform = 'translateX(100%)';
                toast.style.opacity = '0';
            }, 3000);
        },
        
        submitOrder() {
            if (this.form.items.length === 0) {
                this.showNotification('Pilih minimal 1 menu', 'error');
                return;
            }
            
            // Tampilkan loading
            const loadingOverlay = document.getElementById('loadingOverlay');
            loadingOverlay.classList.remove('hidden');
            loadingOverlay.classList.add('flex');
            
            // Disable submit button
            const submitBtn = event.target.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
            }
            
            fetch('{{ route("order.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(this.form)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hapus cart dari localStorage
                    localStorage.removeItem('restaurant_cart');
                    
                    // Set flag untuk reset cart di halaman lain
                    sessionStorage.setItem('order_success', 'true');
                    
                    // Redirect ke halaman sukses
                    window.location.href = data.redirect;
                } else {
                    this.showNotification(data.message || 'Terjadi kesalahan', 'error');
                    loadingOverlay.classList.add('hidden');
                    loadingOverlay.classList.remove('flex');
                    if (submitBtn) submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.showNotification('Terjadi kesalahan saat memproses pesanan', 'error');
                loadingOverlay.classList.add('hidden');
                loadingOverlay.classList.remove('flex');
                if (submitBtn) submitBtn.disabled = false;
            });
        }
    }
}
</script>
@endpush
@endsection