<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\QrCode;

class CheckQrCode
{
    public function handle(Request $request, Closure $next)
    {
        $exceptRoutes = [
            'scan.qr',
            'scan.qr.validate',
            'scan.qr.check',
            'admin.create',
            'admin.qrcodes.download'
        ];

        if ($request->route() && in_array($request->route()->getName(), $exceptRoutes)) {
            return $next($request);
        }

        if ($request->is('admin/*') || $request->is('scan*') || $request->is('cashier/*') || $request->is('login')) {
            return $next($request);
        }

        // Cek dulu apakah sesi masih valid di database
        if (!session()->has('qr_code')) {
            return redirect()->route('scan.qr')->with('error', 'Silakan scan QR code terlebih dahulu untuk mengakses menu');
        }

        $qrCode = QrCode::where('code', session('qr_code'))
                        ->where('status', 'active')
                        ->first();

        if (!$qrCode || !$qrCode->isValid()) {
            session()->forget(['qr_code', 'qr_validated']);
            
            return redirect()->route('scan.qr')->with('error', 'QR Code tidak valid atau sudah kadaluarsa. Silakan scan ulang.');
        }

        // --- TAMBAHAN KEAMANAN: VERIFIKASI LOCK SESSION (STRICT) ---
        $dbSessionId = $qrCode->current_session_id;
        $currentSessionId = session()->getId();

        \Log::info('Middleware Debug: Meja ' . $qrCode->code . ' | DBID: ' . $dbSessionId . ' | CurrentID: ' . $currentSessionId);

        // Jika DB session ID tidak sama dengan session ID pengguna saat ini 
        // (Bisa karena di-reset kasir (null) atau ditimpa pelanggan lain)
        if ($dbSessionId !== $currentSessionId) {
            \Log::info('Middleware: SESI TIDAK COCOK, MENGUSIR PELANGGAN.');
            // Hapus data sesi sensitif
            session()->forget(['qr_code', 'qr_validated', 'cart', 'customer_phone', 'session_start', 'session_id']);
            
            // Redirect ke halaman scan
            $response = redirect()->route('scan.qr')->with('error', 'Sesi Anda telah berakhir. Meja telah direset atau digunakan oleh pelanggan lain.');
            
            // Tambahkan tag meta refresh ke konten respons agar otomatis ter-refresh
            $content = $response->getContent();
            $metaRefresh = '<meta http-equiv="refresh" content="0;url=' . route('scan.qr') . '">';
            $response->setContent(str_replace('</head>', $metaRefresh . '</head>', $content));
            
            return $response;
        }
        \Log::info('Middleware: SESI COCOK, LANJUTKAN.');
        // -------------------------------------------------

        // Jika sudah tervalidasi dan sesi tidak terkunci, lanjutkan
        if (session()->has('qr_validated')) {
            return $next($request);
        }


        // Perpanjang lock session setiap kali ada aktivitas (optional)
        if ($qrCode->current_session_id === session()->getId()) {
            $qrCode->update(['session_expires_at' => now()->addMinutes(60)]);
        }
        // -------------------------------------------------

        session(['qr_validated' => true]);

        return $next($request);
    }
}