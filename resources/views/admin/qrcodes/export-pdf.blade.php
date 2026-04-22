<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Export QR Code</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #f97316;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #f97316;
            font-size: 24px;
            margin: 0;
        }
        .header p {
            color: #666;
            margin: 5px 0 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background: #f97316;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }
        tr:hover {
            background: #f5f5f5;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #999;
        }
        .badge-active {
            color: green;
            font-weight: bold;
        }
        .badge-inactive {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Data QR Code</h1>
        <p>Dapoer Cemal Cemil Jiemas</p>
        <p>Tanggal Export: {{ now()->format('d F Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode QR</th>
                <th>Meja</th>
                <th>Nama Tempat</th>
                <th>Status</th>
                <th>Scan Count</th>
                <th>Dibuat</th>
                <th>Kadaluarsa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($qrCodes as $index => $qr)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $qr->code }}</td>
                <td>{{ $qr->meja ?? '-' }}</td>
                <td>{{ $qr->nama_tempat ?? '-' }}</td>
                <td>
                    @if($qr->status === 'active')
                        <span class="badge-active">Aktif</span>
                    @else
                        <span class="badge-inactive">Nonaktif</span>
                    @endif
                </td>
                <td>{{ $qr->scan_count ?? 0 }}x</td>
                <td>{{ $qr->created_at ? $qr->created_at->format('d/m/Y H:i') : '-' }}</td>
                <td>{{ $qr->expired_at ? $qr->expired_at->format('d/m/Y H:i') : 'Permanen' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Total QR Code: {{ $qrCodes->count() }}</p>
        <p>Dicetak pada: {{ now()->format('d F Y H:i:s') }}</p>
    </div>
</body>
</html>