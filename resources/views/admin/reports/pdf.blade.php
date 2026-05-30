<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 11px; color: #1e293b; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #ea580c; padding-bottom: 10px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th, .table td { border: 1px solid #e2e8f0; padding: 8px; text-align: left; }
        .table th { background: #f8fafc; text-transform: uppercase; font-size: 9px; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .orange { color: #ea580c; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0; color: #0f172a;">{{ setting('restaurant_name', 'Dapoer Jiemas') }}</h1>
        <p style="margin: 5px 0; font-weight: bold;">Laporan Penjualan</p>
        <p style="font-size: 10px;">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>No. Order</th>
                <th>Tanggal</th>
                <th>Customer</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $order->customer_name }}</td>
                <td class="text-right">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right text-bold">TOTAL PENDAPATAN</td>
                <td class="text-right text-bold orange">Rp{{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
