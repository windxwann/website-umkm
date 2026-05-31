<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran - {{ $order->order_number }}</title>
    <style>
        @page { size: 80mm auto; margin: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            width: 80mm;
            margin: 0 auto;
            padding: 10mm;
            font-size: 12px;
            color: #1e293b;
        }
        .header { text-align: center; margin-bottom: 10px; border-bottom: 1px dashed #cbd5e1; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 16px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; }
        .header p { margin: 2px 0; font-size: 10px; color: #64748b; }
        .divider { border-top: 1px dashed #cbd5e1; margin: 10px 0; }
        .items { margin: 10px 0; }
        .item { display: flex; justify-content: space-between; margin-bottom: 4px; }
        .total { border-top: 1px dashed #cbd5e1; margin-top: 10px; padding-top: 10px; font-weight: 800; }
        .footer { text-align: center; margin-top: 20px; font-size: 10px; color: #64748b; }
        .text-center { text-align: center; }
        .font-black { font-weight: 900; }
        .uppercase { text-transform: uppercase; }
        .tracking-widest { letter-spacing: 0.1em; }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2 class="tracking-widest">{{ setting('restaurant_name', 'Dapoer Jiemas') }}</h2>
        <p>{{ setting('address', 'Jl. Kuliner No. 123') }}</p>
        <p>{{ setting('phone', '0812-3456-7890') }}</p>
    </div>

    <div style="font-size: 10px;" class="mb-2">
        <p><strong>Order:</strong> #{{ $order->order_number }}</p>
        <p><strong>Kasir:</strong> {{ auth()->user()->name ?? '-' }}</p>
        <p><strong>Pelanggan:</strong> {{ $order->customer_name }}</p>
        <p><strong>Waktu:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
    </div>

    <div class="divider"></div>

    <div class="items" style="font-size: 11px;">
        @foreach($order->items as $item)
        <div class="item">
            <span style="max-width: 150px;">{{ $item->product_name }} x{{ $item->quantity }}</span>
            <span>Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
        </div>
        @endforeach
    </div>

    <div class="total" style="font-size: 13px;">
        <div class="item">
            <span>TOTAL</span>
            <span>Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="divider"></div>

    <div style="font-size: 10px; margin-top: 5px;">
        <div class="item">
            <span>Metode:</span>
            <span>{{ strtoupper($order->payment_method) }}</span>
        </div>
        <div class="item">
            <span>Status:</span>
            <span class="font-black">{{ $order->payment_status === 'paid' ? 'LUNAS' : 'PENDING' }}</span>
        </div>
    </div>

    <div class="footer">
        <p style="font-size: 14px; font-weight: 900; color: #ea580c;">TERIMA KASIH</p>
        <p>Silakan berkunjung kembali</p>
    </div>
</body>
</html>