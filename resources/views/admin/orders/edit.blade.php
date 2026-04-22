@extends('layouts.admin')

@section('title', 'Edit Pesanan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Pesanan #{{ $order->order_number }}</h1>
        <div>
            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> Detail
            </a>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Pesanan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" id="orderForm">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3">Informasi Pelanggan</h5>
                        
                        <div class="form-group">
                            <label for="customer_name">Nama Pelanggan <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name', $order->customer_name) }}" required>
                            @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="customer_email">Email</label>
                            <input type="email" name="customer_email" id="customer_email" class="form-control @error('customer_email') is-invalid @enderror" value="{{ old('customer_email', $order->customer_email) }}">
                            @error('customer_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="customer_phone">No. Telepon</label>
                            <input type="text" name="customer_phone" id="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror" value="{{ old('customer_phone', $order->customer_phone) }}">
                            @error('customer_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5 class="mb-3">Informasi Pengiriman</h5>
                        
                        <div class="form-group">
                            <label for="shipping_address">Alamat Pengiriman</label>
                            <textarea name="shipping_address" id="shipping_address" rows="3" class="form-control @error('shipping_address') is-invalid @enderror">{{ old('shipping_address', $order->shipping_address) }}</textarea>
                            @error('shipping_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="shipping_courier">Kurir</label>
                            <input type="text" name="shipping_courier" id="shipping_courier" class="form-control @error('shipping_courier') is-invalid @enderror" value="{{ old('shipping_courier', $order->shipping_courier) }}">
                            @error('shipping_courier')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="tracking_number">No. Resi</label>
                            <input type="text" name="tracking_number" id="tracking_number" class="form-control @error('tracking_number') is-invalid @enderror" value="{{ old('tracking_number', $order->tracking_number) }}">
                            @error('tracking_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="shipping_cost">Ongkos Kirim</label>
                            <input type="number" name="shipping_cost" id="shipping_cost" class="form-control @error('shipping_cost') is-invalid @enderror" value="{{ old('shipping_cost', $order->shipping_cost ?? 0) }}" min="0">
                            @error('shipping_cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr>

                <h5 class="mb-3">Detail Produk</h5>
                
                <div class="table-responsive mb-3">
                    <table class="table table-bordered" id="productsTable">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                                <th width="50px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $index => $item)
                            <tr id="productRow{{ $index + 1 }}">
                                <td>
                                    <select name="products[{{ $index }}][id]" class="form-control product-select" required>
                                        <option value="">Pilih Produk</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->price }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }} - Rp {{ number_format($product->price, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="products[{{ $index }}][price]" class="form-control price-input" value="{{ $item->price }}" readonly>
                                </td>
                                <td>
                                    <input type="number" name="products[{{ $index }}][quantity]" class="form-control quantity-input" value="{{ $item->quantity }}" min="1" required>
                                </td>
                                <td>
                                    <input type="text" class="form-control subtotal-input" readonly value="{{ number_format($item->subtotal, 0, ',', '.') }}">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-row" {{ $loop->first ? 'disabled' : '' }}>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Total</strong></td>
                                <td>
                                    <input type="text" id="totalAmount" class="form-control" readonly value="{{ number_format($order->total_amount, 0, ',', '.') }}">
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Diskon</strong></td>
                                <td>
                                    <input type="number" name="discount" id="discount" class="form-control" value="{{ $order->discount ?? 0 }}" min="0">
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Grand Total</strong></td>
                                <td>
                                    <input type="text" id="grandTotal" class="form-control" readonly value="{{ number_format($order->grand_total, 0, ',', '.') }}">
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mb-3">
                    <button type="button" class="btn btn-success" id="addProduct">
                        <i class="fas fa-plus"></i> Tambah Produk
                    </button>
                </div>

                <div class="form-group">
                    <label for="notes">Catatan</label>
                    <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes', $order->notes) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="payment_method">Metode Pembayaran</label>
                    <select name="payment_method" id="payment_method" class="form-control">
                        <option value="">Pilih Metode Pembayaran</option>
                        <option value="cash" {{ $order->payment_method == 'cash' ? 'selected' : '' }}>Tunai</option>
                        <option value="transfer" {{ $order->payment_method == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="credit_card" {{ $order->payment_method == 'credit_card' ? 'selected' : '' }}>Kartu Kredit</option>
                    </select>
                </div>

                <hr>

                <div class="d-flex justify-content-end">
                    <button type="reset" class="btn btn-secondary mr-2">Reset</button>
                    <button type="submit" class="btn btn-primary">Update Pesanan</button>
                </div>
            </form>
        </div>
    </div>
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