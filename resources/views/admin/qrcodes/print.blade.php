<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Print QR Code - {{ $qrcode->code }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #e5e7eb;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .qr-card {
            background: white;
            width: 320px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-align: center;
            overflow: hidden;
        }
        .qr-header {
            background: linear-gradient(135deg, #f97316, #ea580c);
            padding: 16px;
            color: white;
        }
        .qr-header h2 { font-size: 18px; font-weight: 600; }
        .qr-header p { font-size: 11px; opacity: 0.9; margin-top: 4px; }
        .qr-body { padding: 24px; }
        .qr-body img { width: 160px; height: 160px; margin-bottom: 16px; }
        .qr-code { font-family: monospace; font-size: 11px; background: #f3f4f6; padding: 8px; border-radius: 8px; word-break: break-all; }
        .qr-info { margin-top: 16px; font-size: 12px; color: #6b7280; }
        .qr-footer { background: #f9fafb; padding: 12px; font-size: 10px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
        @media print {
            body { background: white; padding: 0; }
            .qr-card { box-shadow: none; width: 100%; }
            .qr-header { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="qr-card">
        <div class="qr-header">
            <h2>DAPOER CEMAL CEMIL</h2>
            <p>Scan QR Code untuk memesan</p>
        </div>
        <div class="qr-body">
            <img src="{{ $qrImage }}" alt="QR Code">
            <div class="qr-code">{{ $qrcode->code }}</div>
            <div class="qr-info">
                <div>Meja: {{ $qrcode->meja ?? '-' }}</div>
                @if($qrcode->nama_tempat)
                <div>{{ $qrcode->nama_tempat }}</div>
                @endif
            </div>
        </div>
        <div class="qr-footer">
            www.dapoercemalcemil.com | Scan untuk pesan makanan
        </div>
    </div>
    <script>window.print();</script>
</body>
</html>