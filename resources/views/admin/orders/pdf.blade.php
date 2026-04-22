<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }
        .header {
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .company-info {
            text-align: right;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .status {
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-processing { background: #cce5ff; color: #004085; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th {
            background: #333;
            color: white;
            padding: 10px;
            text-align: left;
        }
        table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            font-weight: bold;
            background: #f5f5f5;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .signature {
            margin-top: 50px;
        }
        .signature-line {
            width: 200px;
            border-top: 1px solid #333;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <table style="border: none; margin: 0;">
                <tr>
                    <td style="width: 50%; border: none;">
                        <div class="invoice-title">INVOICE</div>
                        <div>No. {{ $order->order_number }}</div>
                    </td>
                    <td style="width: 50%; border: none; text-align: right;">
                        <div style="font-weight: bold;">{{ config('app.name') }}</div>
                        <div>{{ config('app.address', 'Jl. Contoh No. 123') }}</div>
                        <div>Telp: {{ config('app.phone', '(021) 1234567') }}</div>
                        <div>Email: {{ config('app.email', 'info@example.com') }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <table style="border: none; margin: 20px 0;">
            <tr>
                <td style="width: 50%; border: none;">
                    <strong>Bill To:</strong><br>
                    {{ $order->customer_name }}<br>
                    {{ $order->customer_email }}<br>
                    {{ $order->customer_phone }}<br>
                    {{ $order->shipping_address ?? '-' }}
                </td>
                <td style="width: 50%; border: none; text-align: right;">
                    <strong>Tanggal:</strong> {{ $order->created_at->format('d/m/Y H:i') }}<br>
                    <strong>Status:</strong> 
                    <span class="status status-{{ $order->status }}">{{ ucfirst($order->status) }}</span><br>
                    <strong>Pembayaran:</strong> {{ $order->payment_method ?? '-' }}<br>
                    @if($order->tracking_number)
                        <strong>No. Resi:</strong> {{ $order->tracking_number }}
                    @endif
                </td>
            </tr>
        </table>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 45%;">Produk</th>
                    <th style="width: 15%;" class="text-right">Harga</th>
                    <th style="width: 10%;" class="text-center">Jml</th>
                    <th style="width: 25%;" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table style="border: none; margin-top: 10px;">
            <tr>
                <td style="width: 70%; border: none;"></td>
                <td style="width: 30%; border: none;">
                    <table style="margin: 0;">
                        <tr>
                            <td>Subtotal</td>
                            <td class="text-right">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        </tr>
                        @if($order->discount > 0)
                        <tr>
                            <td>Diskon</td>
                            <td class="text-right">- Rp {{ number_format($order->discount, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        @if($order->shipping_cost > 0)
                        <tr>
                            <td>Ongkos Kirim</td>
                            <td class="text-right">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr style="font-weight: bold;">
                            <td>Grand Total</td>
                            <td class="text-right">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        @if($order->notes)
        <div style="margin-top: 20px; padding: 10px; background: #f9f9f9;">
            <strong>Catatan:</strong><br>
            {{ $order->notes }}
        </div>
        @endif

        <div class="signature">
            <table style="border: none;">
                <tr>
                    <td style="width: 50%; border: none;">
                        <div>Penerima,</div>
                        <div class="signature-line"></div>
                        <div>({{ $order->customer_name }})</div>
                    </td>
                    <td style="width: 50%; border: none; text-align: right;">
                        <div>Hormat kami,</div>
                        <div class="signature-line" style="margin-left: auto;"></div>
                        <div>(Admin)</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>Terima kasih telah berbelanja di {{ config('app.name') }}</p>
            <p>Invoice ini adalah bukti pembayaran yang sah</p>
            <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>