@extends('admin.layouts.app')

@section('title', 'Edit Pesanan')
@section('page-title', 'Edit Pesanan')

@section('content')
<div class="space-y-6">
    <!-- Header Mobile Friendly -->
    <div class="bg-gradient-to-r from-orange-600 to-orange-500 rounded-xl p-4 md:p-6 text-white shadow-lg">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold mb-1 md:mb-2">Edit Pesanan #{{ $order->order_number }}</h1>
                <p class="text-orange-100 text-sm md:text-base">
                    <i class="fas fa-edit mr-2"></i>Perbarui informasi dan detail pesanan
                </p>
            </div>
            <div class="flex items-center gap-2 md:gap-4">
                <a href="{{ route('admin.orders.show', $order->id) }}" 
                   class="bg-white/20 text-white border border-white/50 px-3 md:px-4 py-2 rounded-lg hover:bg-white/30 transition flex items-center text-sm font-semibold shadow-sm">
                    <i class="fas fa-eye md:mr-2"></i><span class="hidden md:inline">Detail</span>
                </a>
                <a href="{{ route('admin.orders.index') }}" 
                   class="bg-white text-orange-600 px-3 md:px-4 py-2 rounded-lg hover:bg-gray-100 transition flex items-center text-sm font-semibold shadow-md">
                    <i class="fas fa-arrow-left md:mr-2"></i><span class="hidden md:inline">Kembali</span>
                </a>
                <div class="hidden md:block ml-2">
                    <i class="fas fa-shopping-cart text-6xl opacity-30"></i>
                </div>
            </div>
        </div>
    </div>

    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-xl mr-3"></i>
            <div>
                <p class="font-bold">Error!</p>
                <p>{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Form Section -->
    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" id="orderForm" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Informasi Pelanggan -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-user text-orange-500 mr-2"></i>Informasi Pelanggan
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggan <span class="text-red-500">*</span></label>
                        <input type="text" name="customer_name" id="customer_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('customer_name') border-red-500 @enderror" value="{{ old('customer_name', $order->customer_name) }}" required>
                        @error('customer_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="customer_email" id="customer_email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('customer_email') border-red-500 @enderror" value="{{ old('customer_email', $order->customer_email) }}">
                        @error('customer_email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                        <input type="text" name="customer_phone" id="customer_phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('customer_phone') border-red-500 @enderror" value="{{ old('customer_phone', $order->customer_phone) }}">
                        @error('customer_phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Informasi Pengiriman -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-truck text-orange-500 mr-2"></i>Informasi Pengiriman
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-1">Alamat Pengiriman</label>
                        <textarea name="shipping_address" id="shipping_address" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('shipping_address') border-red-500 @enderror">{{ old('shipping_address', $order->shipping_address) }}</textarea>
                        @error('shipping_address')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label for="shipping_courier" class="block text-sm font-medium text-gray-700 mb-1">Kurir</label>
                            <input type="text" name="shipping_courier" id="shipping_courier" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('shipping_courier') border-red-500 @enderror" value="{{ old('shipping_courier', $order->shipping_courier) }}">
                            @error('shipping_courier')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tracking_number" class="block text-sm font-medium text-gray-700 mb-1">No. Resi</label>
                            <input type="text" name="tracking_number" id="tracking_number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('tracking_number') border-red-500 @enderror" value="{{ old('tracking_number', $order->tracking_number) }}">
                            @error('tracking_number')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="shipping_cost" class="block text-sm font-medium text-gray-700 mb-1">Ongkos Kirim</label>
                            <input type="number" name="shipping_cost" id="shipping_cost" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('shipping_cost') border-red-500 @enderror" value="{{ old('shipping_cost', $order->shipping_cost ?? 0) }}" min="0">
                            @error('shipping_cost')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Produk -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-box text-orange-500 mr-2"></i>Detail Produk
                </h3>
                <button type="button" class="bg-green-100 text-green-700 px-3 py-1.5 rounded-lg hover:bg-green-200 transition text-sm font-medium flex items-center" id="addProduct">
                    <i class="fas fa-plus mr-1"></i> Tambah Item
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full" id="productsTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Subtotal</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($order->items as $index => $item)
                        <tr id="productRow{{ $index + 1 }}">
                            <td class="px-6 py-4">
                                <select name="products[{{ $index }}][id]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 product-select text-sm" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->price }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} - Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-6 py-4">
                                <input type="number" name="products[{{ $index }}][price]" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 price-input text-sm" value="{{ $item->price }}" readonly>
                            </td>
                            <td class="px-6 py-4">
                                <input type="number" name="products[{{ $index }}][quantity]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 quantity-input text-sm" value="{{ $item->quantity }}" min="1" required>
                            </td>
                            <td class="px-6 py-4">
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 subtotal-input text-sm font-semibold text-gray-700" readonly value="{{ number_format($item->subtotal, 0, ',', '.') }}">
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button type="button" class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition remove-row" {{ $loop->first ? 'disabled' : '' }}>
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right font-semibold text-gray-700">Total</td>
                            <td class="px-6 py-3">
                                <input type="text" id="totalAmount" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 font-bold text-gray-700 text-sm" readonly value="{{ number_format($order->total_amount, 0, ',', '.') }}">
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right font-semibold text-gray-700">Diskon</td>
                            <td class="px-6 py-3">
                                <input type="number" name="discount" id="discount" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm" value="{{ $order->discount ?? 0 }}" min="0">
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right font-bold text-gray-800 text-lg">Grand Total</td>
                            <td class="px-6 py-4">
                                <input type="text" id="grandTotal" class="w-full px-3 py-2 border-none bg-transparent font-bold text-orange-600 text-lg" readonly value="{{ number_format($order->grand_total, 0, ',', '.') }}">
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Catatan & Pembayaran -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 placeholder-gray-400" placeholder="Tambahkan pesan atau instruksi khusus...">{{ old('notes', $order->notes) }}</textarea>
                </div>
                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                    <select name="payment_method" id="payment_method" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">Pilih Metode Pembayaran</option>
                        <option value="cash" {{ $order->payment_method == 'cash' ? 'selected' : '' }}>Tunai</option>
                        <option value="transfer" {{ $order->payment_method == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="credit_card" {{ $order->payment_method == 'credit_card' ? 'selected' : '' }}>Kartu Kredit</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-3 pb-8">
            <button type="reset" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                Reset
            </button>
            <button type="submit" class="px-6 py-2.5 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition shadow-md font-medium flex items-center">
                <i class="fas fa-save mr-2"></i> Update Pesanan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
let productIndex = {{ count($order->items) }};

document.getElementById('addProduct').addEventListener('click', function() {
    const tbody = document.querySelector('#productsTable tbody');
    const newRow = document.querySelector('#productRow1').cloneNode(true);
    
    productIndex++;
    newRow.id = 'productRow' + productIndex;
    newRow.querySelectorAll('select, input').forEach(element => {
        const name = element.getAttribute('name');
        if (name) {
            element.setAttribute('name', name.replace(/\[\d+\]/, '[' + (productIndex - 1) + ']'));
        }
        if (element.tagName === 'SELECT') {
            element.value = '';
        } else if (element.type === 'number' && element.classList.contains('quantity-input')) {
            element.value = 1;
        } else {
            element.value = '';
        }
    });
    
    newRow.querySelector('.remove-row').disabled = false;
    tbody.appendChild(newRow);
    
    attachEventListeners();
});

function attachEventListeners() {
    document.querySelectorAll('.product-select').forEach(select => {
        select.addEventListener('change', function() {
            const row = this.closest('tr');
            const priceInput = row.querySelector('.price-input');
            const selectedOption = this.options[this.selectedIndex];
            
            if (selectedOption.value) {
                const price = selectedOption.dataset.price;
                priceInput.value = price;
            } else {
                priceInput.value = '';
            }
            calculateSubtotal(row);
            calculateTotal();
        });
    });

    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('input', function() {
            const row = this.closest('tr');
            calculateSubtotal(row);
            calculateTotal();
        });
    });

    document.querySelectorAll('.remove-row').forEach(button => {
        button.addEventListener('click', function() {
            if (document.querySelectorAll('#productsTable tbody tr').length > 1) {
                this.closest('tr').remove();
                calculateTotal();
            }
        });
    });
}

function calculateSubtotal(row) {
    const price = parseFloat(row.querySelector('.price-input').value) || 0;
    const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
    const subtotal = price * quantity;
    row.querySelector('.subtotal-input').value = subtotal.toLocaleString('id-ID');
}

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.subtotal-input').forEach(input => {
        total += parseFloat(input.value.replace(/\./g, '')) || 0;
    });
    
    document.getElementById('totalAmount').value = total.toLocaleString('id-ID');
    
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const shippingCost = parseFloat(document.getElementById('shipping_cost').value) || 0;
    const grandTotal = total - discount + shippingCost;
    document.getElementById('grandTotal').value = grandTotal.toLocaleString('id-ID');
}

document.getElementById('discount').addEventListener('input', calculateTotal);
document.getElementById('shipping_cost').addEventListener('input', calculateTotal);

// Initial event listeners
attachEventListeners();

// Calculate initial totals
calculateTotal();
</script>
@endpush
@endsection