<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi - {{ now()->format('d-m-Y') }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #f97316; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #f97316; font-size: 24px; }
        .report-info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 8px; text-align: left; font-size: 11px; text-transform: uppercase; }
        td { border: 1px solid #e2e8f0; padding: 8px; font-size: 11px; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #f1f5f9; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ setting('restaurant_name', 'Dapoer Jiemas') }}</h1>
        <p>{{ setting('address', 'Jl. Kuliner No. 123, Jakarta') }}</p>
    </div>

    <div class="report-info">
        <p><strong>Periode Laporan:</strong> {{ request('start_date') ? request('start_date') : 'Awal' }} s/d {{ request('end_date') ? request('end_date') : 'Hari ini' }}</p>
        <p><strong>Dicetak pada:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No. Order</th>
                <th>Pelanggan</th>
                <th>Meja</th>
                <th>Status</th>
                <th>Metode</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>#{{ $order->order_number }}</td>
                <td>{{ $order->customer_name }}</td>
                <td>{{ $order->qrCodeRelation->meja ?? $order->table_number ?? $order->qr_code ?? '-' }}</td>
                <td>{{ strtoupper($order->order_status) }}</td>
                <td>{{ strtoupper($order->payment_method) }}</td>
                <td class="text-right">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="5" class="text-right">TOTAL PENDAPATAN</td>
                <td class="text-right">Rp{{ number_format($orders->sum('total_amount'), 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>