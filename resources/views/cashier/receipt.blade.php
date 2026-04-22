<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran - {{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            width: 300px;
            margin: 0 auto;
            padding: 20px;
            font-size: 14px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px dashed #333;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
        }
        .items {
            margin: 15px 0;
        }
        .item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .item-name {
            max-width: 180px;
        }
        .total {
            border-top: 2px solid #333;
            margin-top: 10px;
            padding-top: 10px;
            font-weight: bold;
        }
        .payment-info {
            margin: 15px 0;
            padding: 10px 0;
            border-top: 1px dashed #333;
            border-bottom: 1px dashed #333;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            border-top: 2px dashed #333;
            padding-top: 10px;
        }
        .thankyou {
            font-size: 16px;
            font-weight: bold;
            color: #f97316;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .mb-1 {
            margin-bottom: 5px;
        }
        .mb-2 {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>DAPOER CEMAL CEMIL</h2>
        <p>Jl. Kuliner No. 123, Jakarta</p>
        <p>Telp: 0812-3456-7890</p>
        <p>{{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="mb-2">
        <p><strong>No. Order:</strong> {{ $order->order_number }}</p>
        <p><strong>Kasir:</strong> {{ auth()->user()->name }}</p>
        <p><strong>Customer:</strong> {{ $order->customer_name }}</p>
        <p><strong>Tipe:</strong> {{ $order->order_type === 'offline' ? 'Makan di Tempat' : 'Bawa Pulang' }}</p>
    </div>

    <div class="items">
        <div class="item" style="border-bottom: 1px solid #333; padding-bottom: 5px;">
            <span><strong>Item</strong></span>
            <span><strong>Subtotal</strong></span>
        </div>
        
        @foreach($order->items as $item)
        <div class="item">
            <span class="item-name">{{ $item->product_name }} x{{ $item->quantity }}</span>
            <span class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
        </div>
        @endforeach
    </div>

    <div class="total">
        <div class="item">
            <span>Total</span>
            <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="payment-info">
        <div class="item">
            <span>Metode Pembayaran</span>
            <span>
                @if($order->payment_method === 'cashier')
                    Tunai
                @elseif($order->payment_method === 'e_wallet')
                    E-Wallet
                @else
                    Transfer Bank
                @endif
            </span>
        </div>
        
        @if($order->payment_method === 'cashier')
        <div class="item">
            <span>Jumlah Dibayar</span>
            <span>Rp {{ number_format($order->paid_amount, 0, ',', '.') }}</span>
        </div>
        <div class="item">
            <span>Kembalian</span>
            <span>Rp {{ number_format($order->paid_amount - $order->total_amount, 0, ',', '.') }}</span>
        </div>
        @endif
        
        <div class="item">
            <span>Status</span>
            <span>{{ $order->payment_status === 'paid' ? 'LUNAS' : 'PENDING' }}</span>
        </div>
    </div>

    <div class="footer">
        <p class="thankyou">TERIMA KASIH</p>
        <p>Silakan datang kembali</p>
        <p>Barang yang sudah dibeli tidak dapat dikembalikan</p>
        <p style="margin-top: 10px;">www.dapoercemalcemil.com</p>
    </div>

    <script>
        window.print();
    </script>
</body>
</html>