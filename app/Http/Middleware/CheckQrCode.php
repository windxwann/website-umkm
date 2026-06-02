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

        if (session()->has('qr_validated')) {
            return $next($request);
        }

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

        // --- TAMBAHAN KEAMANAN: VERIFIKASI LOCK SESSION ---
        // Jika QR Code terkunci oleh session ID lain, maka paksa scan ulang
        if ($qrCode->current_session_id && $qrCode->current_session_id !== session()->getId()) {
            session()->forget(['qr_code', 'qr_validated', 'cart', 'customer_phone']); // Hapus juga data sensitif lainnya

            // Tambahkan flag untuk frontend agar tahu sesi kadaluarsa
            return redirect()->route('scan.qr')->with('error', 'Sesi Anda telah berakhir karena meja ini telah digunakan oleh pelanggan lain. Silakan scan ulang.');
        }
        // -------------------------------------------------

        // Perpanjang lock session setiap kali ada aktivitas (optional)
        if ($qrCode->current_session_id === session()->getId()) {
            $qrCode->update(['session_expires_at' => now()->addMinutes(60)]);
        }
        // -------------------------------------------------

        session(['qr_validated' => true]);

        return $next($request);
    }
}