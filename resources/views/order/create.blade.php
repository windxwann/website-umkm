@extends('layouts.app')

@section('title', 'Buat Pesanan')

@section('content')
<div x-data="orderForm()" class="max-w-4xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2">Buat Pesanan</h1>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                <i class="fas fa-shopping-basket text-orange-600"></i>
                Lengkapi rincian pesanan Anda
            </p>
        </div>
        <a href="{{ route('menu') }}" class="bg-white border border-slate-100 text-slate-600 px-6 py-2.5 rounded-xl hover:bg-slate-50 transition-all font-black text-[10px] uppercase tracking-widest shadow-sm">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Menu
        </a>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white p-8 rounded-[2rem] shadow-2xl border border-slate-100 max-w-sm w-full text-center">
            <div class="w-16 h-16 border-4 border-slate-100 border-t-orange-600 rounded-full animate-spin mb-6 mx-auto"></div>
            <h3 class="text-lg font-black text-slate-900 mb-2">Memproses Pesanan</h3>
            <p class="text-sm font-medium text-slate-500">Mohon tunggu sebentar...</p>
        </div>
    </div>

    <form @submit.prevent="submitOrder" class="space-y-8">
        <!-- Customer Information -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
            <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-3">
                <div class="w-8 h-8 bg-orange-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user text-orange-600 text-xs"></i>
                </div>
                Informasi Pelanggan
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" 
                           x-model="form.customer_name" 
                           class="w-full bg-slate-50 border-none px-5 py-3 rounded-xl text-sm font-bold text-slate-900 focus:ring-4 focus:ring-orange-500/5 transition-all"
                           placeholder="Nama pemesan"
                           required>
                </div>
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">No. WhatsApp</label>
                    <input type="tel" 
                           x-model="form.customer_phone" 
                           class="w-full bg-slate-50 border-none px-5 py-3 rounded-xl text-sm font-bold text-slate-900 focus:ring-4 focus:ring-orange-500/5 transition-all"
                           placeholder="0812xxxxxx">
                </div>
            </div>
        </div>

        <!-- Order Type (Dine In / Take Away) -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
            <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-3">
                <div class="w-8 h-8 bg-orange-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-utensils text-orange-600 text-xs"></i>
                </div>
                Tipe Pesanan
            </h2>
            <div class="grid grid-cols-2 gap-4">
                <!-- Makan di Sini -->
                <label class="relative flex flex-col items-center justify-center p-6 border-2 rounded-2xl cursor-pointer transition-all duration-300 hover:shadow-md text-center gap-3"
                       :class="form.order_type === 'dine_in' ? 'bg-orange-50 border-orange-400 ring-4 ring-orange-500/10' : 'bg-white border-slate-100'">
                    <input type="radio" x-model="form.order_type" value="dine_in" class="hidden">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center transition-all"
                         :class="form.order_type === 'dine_in' ? 'bg-orange-600 text-white' : 'bg-slate-50 text-slate-400'">
                        <i class="fas fa-chair text-xl"></i>
                    </div>
                    <div>
                        <span class="block text-[11px] font-black uppercase tracking-widest"
                              :class="form.order_type === 'dine_in' ? 'text-orange-900' : 'text-slate-900'">Makan di Sini</span>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter mt-0.5">Dine In</p>
                    </div>
                    <div x-show="form.order_type === 'dine_in'"
                         class="absolute top-3 right-3 w-5 h-5 bg-orange-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-white" style="font-size: 8px;"></i>
                    </div>
                </label>

                <!-- Take Away -->
                <label class="relative flex flex-col items-center justify-center p-6 border-2 rounded-2xl cursor-pointer transition-all duration-300 hover:shadow-md text-center gap-3"
                       :class="form.order_type === 'takeaway' ? 'bg-orange-50 border-orange-400 ring-4 ring-orange-500/10' : 'bg-white border-slate-100'">
                    <input type="radio" x-model="form.order_type" value="takeaway" class="hidden">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center transition-all"
                         :class="form.order_type === 'takeaway' ? 'bg-orange-600 text-white' : 'bg-slate-50 text-slate-400'">
                        <i class="fas fa-shopping-bag text-xl"></i>
                    </div>
                    <div>
                        <span class="block text-[11px] font-black uppercase tracking-widest"
                              :class="form.order_type === 'takeaway' ? 'text-orange-900' : 'text-slate-900'">Take Away</span>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter mt-0.5">Bawa Pulang</p>
                    </div>
                    <div x-show="form.order_type === 'takeaway'"
                         class="absolute top-3 right-3 w-5 h-5 bg-orange-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-white" style="font-size: 8px;"></i>
                    </div>
                </label>
            </div>
        </div>

        <!-- Order Items Summary -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
            <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-3">
                <div class="w-8 h-8 bg-orange-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-shopping-bag text-orange-600 text-xs"></i>
                </div>
                Ringkasan Pesanan
            </h2>
            
            <!-- Items List -->
            <div class="space-y-4 mb-8">
                <template x-if="form.items.length === 0">
                    <div class="text-center py-12 bg-slate-50 rounded-[2rem] border border-slate-100">
                        <i class="fas fa-shopping-cart text-4xl text-slate-200 mb-4"></i>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Keranjang masih kosong</p>
                    </div>
                </template>
                
                <template x-for="(item, index) in form.items" :key="index">
                    <div class="flex items-center justify-between p-5 bg-slate-50 rounded-2xl border border-slate-100 group transition-all hover:bg-white hover:shadow-md">
                        <div class="flex-1">
                            <h3 class="text-sm font-black text-slate-900 mb-1" x-text="item.name"></h3>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                <span x-text="item.quantity"></span> x Rp <span x-text="formatPrice(item.price)"></span>
                            </p>
                        </div>
                        <div class="flex items-center gap-6 text-right">
                            <p class="text-sm font-black text-orange-600" x-text="'Rp ' + formatPrice(item.subtotal)"></p>
                            <button type="button" 
                                    @click="removeItem(index)"
                                    class="text-rose-400 hover:text-rose-600 transition-colors">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
            
            <!-- Total -->
            <div class="border-t border-slate-100 pt-6 space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Subtotal</span>
                    <span class="text-sm font-black text-slate-600">Rp <span x-text="formatPrice(subtotal)"></span></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pajak ({{ setting('tax', 0) }}%)</span>
                    <span class="text-sm font-black text-slate-600">Rp <span x-text="formatPrice(Math.round(subtotal * {{ setting('tax', 0) }} / 100))"></span></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Service Charge ({{ setting('service_charge', 0) }}%)</span>
                    <span class="text-sm font-black text-slate-600">Rp <span x-text="formatPrice(Math.round(subtotal * {{ setting('service_charge', 0) }} / 100))"></span></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Packaging Fee</span>
                    <span class="text-sm font-black text-slate-600">Rp <span x-text="formatPrice({{ setting('packaging_fee', 0) }})"></span></span>
                </div>
                <div class="flex justify-between items-center pt-4 border-t border-dashed border-slate-200">
                    <span class="text-xs font-black text-slate-900 uppercase tracking-widest">Total Bayar</span>
                    <span class="text-2xl font-black text-slate-900">Rp <span x-text="formatPrice(total)"></span></span>
                </div>
            </div>
        </div>

        <!-- Payment Method -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
            <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-3">
                <div class="w-8 h-8 bg-orange-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-credit-card text-orange-600 text-xs"></i>
                </div>
                Metode Pembayaran
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <label class="relative flex items-center p-4 border rounded-2xl cursor-pointer transition-all duration-300 hover:shadow-md"
                       :class="form.payment_method === 'e_wallet' ? 'bg-orange-50 border-orange-200 ring-4 ring-orange-500/5' : 'bg-white border-slate-100'">
                    <input type="radio" x-model="form.payment_method" value="e_wallet" class="hidden">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" :class="form.payment_method === 'e_wallet' ? 'bg-orange-600 text-white' : 'bg-slate-50 text-slate-400'">
                            <i class="fas fa-qrcode text-sm"></i>
                        </div>
                        <div>
                            <span class="block text-[10px] font-black uppercase tracking-widest" :class="form.payment_method === 'e_wallet' ? 'text-orange-900' : 'text-slate-900'">QRIS</span>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">E-Wallet</p>
                        </div>
                    </div>
                </label>

                <label class="relative flex items-center p-4 border rounded-2xl cursor-pointer transition-all duration-300 hover:shadow-md"
                       :class="form.payment_method === 'cashier' ? 'bg-orange-50 border-orange-200 ring-4 ring-orange-500/5' : 'bg-white border-slate-100'">
                    <input type="radio" x-model="form.payment_method" value="cashier" class="hidden">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" :class="form.payment_method === 'cashier' ? 'bg-orange-600 text-white' : 'bg-slate-50 text-slate-400'">
                            <i class="fas fa-wallet text-sm"></i>
                        </div>
                        <div>
                            <span class="block text-[10px] font-black uppercase tracking-widest" :class="form.payment_method === 'cashier' ? 'text-orange-900' : 'text-slate-900'">KASIR</span>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Tunai</p>
                        </div>
                    </div>
                </label>

                <label class="relative flex items-center p-4 border rounded-2xl cursor-pointer transition-all duration-300 hover:shadow-md"
                       :class="form.payment_method === 'bank_transfer' ? 'bg-orange-50 border-orange-200 ring-4 ring-orange-500/5' : 'bg-white border-slate-100'">
                    <input type="radio" x-model="form.payment_method" value="bank_transfer" class="hidden">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" :class="form.payment_method === 'bank_transfer' ? 'bg-orange-600 text-white' : 'bg-slate-50 text-slate-400'">
                            <i class="fas fa-university text-sm"></i>
                        </div>
                        <div>
                            <span class="block text-[10px] font-black uppercase tracking-widest" :class="form.payment_method === 'bank_transfer' ? 'text-orange-900' : 'text-slate-900'">TRANSFER</span>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Bank</p>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Notes -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
            <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-3">
                <div class="w-8 h-8 bg-orange-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-pen text-orange-600 text-xs"></i>
                </div>
                Catatan Tambahan
            </h2>
            <textarea x-model="form.notes" 
                      rows="3" 
                      class="w-full bg-slate-50 border-none px-5 py-3 rounded-xl text-sm font-bold text-slate-900 focus:ring-4 focus:ring-orange-500/5 transition-all resize-none"
                      placeholder="Contoh: Level pedas sedang, jangan pakai bawang..."></textarea>
        </div>

        <!-- Submit Button -->
        <div class="pb-12">
            <button type="submit" 
                    class="w-full bg-slate-900 text-white py-5 px-4 rounded-[1.5rem] font-black text-xs uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl shadow-slate-900/10 disabled:opacity-50 disabled:cursor-not-allowed group flex items-center justify-center gap-3"
                    :disabled="form.items.length === 0">
                <i class="fas fa-check-circle text-orange-500 group-hover:text-white transition-colors"></i>
                KONFIRMASI PESANAN SEKARANG
            </button>
        </div>
    </form>
</div>

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush

@push('scripts')
<script>
function orderForm() {
    return {
        form: {
            customer_name: '',
            customer_phone: '',
            order_type: 'dine_in',
            payment_method: 'e_wallet',
            items: [],
            notes: ''
        },
        packagingFee: 0,
        quantities: {},
        
        init() {
            const cartItems = @json($cartItems ?? []);
            
            if (cartItems && cartItems.length > 0) {
                this.quantities = {};
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
            this.form.items.sort((a, b) => a.product_id - b.product_id);
        },
        
        removeItem(index) {
            const item = this.form.items[index];
            if (item) {
                delete this.quantities[item.product_id];
                this.form.items.splice(index, 1);
            }
        },
        
        get subtotal() {
            return this.form.items.reduce((sum, item) => sum + item.subtotal, 0);
        },
        
        get total() {
            const tax = Math.round(this.subtotal * {{ setting('tax', 0) }} / 100);
            const serviceCharge = Math.round(this.subtotal * {{ setting('service_charge', 0) }} / 100);
            return this.subtotal + tax + serviceCharge + this.packagingFee;
        },
        
        formatPrice(price) {
            return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        },
        
        showNotification(message, type = 'success') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                icon: type,
                title: message
            });
        },
        
        submitOrder() {
            if (this.form.items.length === 0) {
                this.showNotification('Pilih minimal 1 menu', 'error');
                return;
            }
            
            const loadingOverlay = document.getElementById('loadingOverlay');
            loadingOverlay.classList.remove('hidden');
            loadingOverlay.classList.add('flex');
            
            const submitBtn = event.target.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;
            
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
                    localStorage.removeItem('restaurant_cart');
                    sessionStorage.setItem('order_success', 'true');
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
                this.showNotification('Terjadi kesalahan', 'error');
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