<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QrCode as QrCodeModel;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class QrCodeController extends Controller
{
    /**
     * Menampilkan halaman scan QR code
     */
    public function showScan()
    {
        // Cek apakah session masih valid
        if (session()->has('qr_validated') && session()->has('qr_code')) {
            $qrCode = QrCodeModel::where('code', session('qr_code'))
                            ->where('status', 'active')
                            ->first();
                            
            if ($qrCode && $qrCode->isValid()) {
                return redirect()->route('home');
            } else {
                // Gunakan forget, jangan pernah gunakan flush() untuk membersihkan session!
                session()->forget(['qr_validated', 'qr_code', 'session_start', 'qr_scan_time']);
            }
        }
        
        return view('scan-qr');
    }

    /**
     * Validasi QR code yang di-scan
     */
    public function validateQr(Request $request, $qr_code = null)
    {
        // Mendapatkan kode dari parameter URL (GET) atau input form (POST)
        $scannedCode = $qr_code ?? $request->qr_code;

        if (!$scannedCode) {
            return redirect()->route('scan.qr')->with('error', 'Kode QR tidak ditemukan');
        }
        
        // 1. Ekstrak data jika ternyata QR menampung format Full URL
        $scannedCode = trim($scannedCode);
        
        if (filter_var($scannedCode, FILTER_VALIDATE_URL)) {
            $parsedUrl = parse_url($scannedCode);
            if (isset($parsedUrl['query'])) {
                parse_str($parsedUrl['query'], $query);
                // Ambil nilai dari parameter url, misal ?code=ABC atau ?qr_code=ABC
                $scannedCode = $query['qr_code'] ?? $query['code'] ?? $query['qr'] ?? $scannedCode;
            } else {
                // Ambil rute paling ujung, misal http://web.com/scan/QR-MEJA-001
                $pathSegments = explode('/', rtrim($parsedUrl['path'], '/'));
                $scannedCode = end($pathSegments);
            }
        }

        // 2. Pastikan bentuknya selalu Uppercase (menyinkronkan JS dan input manual)
        $scannedCode = strtoupper(trim($scannedCode));

        $qrCode = QrCodeModel::where('code', $scannedCode)
                        ->where('status', 'active')
                        ->first();

        if (!$qrCode || !$qrCode->isValid()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'QR Code tidak valid atau sudah kadaluarsa']);
            }
            return back()->with('error', 'QR Code tidak valid atau sudah kadaluarsa');
        }

        $activeOrder = Order::where('qr_code', $qrCode->code)
            ->whereIn('order_status', ['waiting', 'processed'])
            ->exists();
            
        if ($activeOrder) {
            // Jika ada order aktif, pastikan sesinya sama
            if ($qrCode->current_session_id && $qrCode->current_session_id !== session()->getId()) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Meja ini sedang digunakan oleh pelanggan lain.']);
                }
                return back()->with('error', 'Meja ini sedang digunakan oleh pelanggan lain.');
            }
        } else {
            // Jika tidak ada order aktif, cek apakah session lock masih berlaku
            if ($qrCode->current_session_id && 
                $qrCode->current_session_id !== session()->getId() && 
                $qrCode->session_expires_at && 
                $qrCode->session_expires_at->isFuture()) {
                
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Meja ini baru saja dipesan/diakses. Silakan tunggu beberapa saat atau hubungi kasir.']);
                }
                return back()->with('error', 'Meja ini sedang diakses oleh pelanggan lain.');
            }
        }

        // 3. PERBAIKAN FATAL: Menghindari session()->flush();
        // flush() menghapus CSRF _token, hal ini akan memicu error 419 di request selanjutnya.
        
        // Regenerate ID Saja untuk alasan keamanan (mencegah session fixation)
        $request->session()->regenerate();
        $newSessionId = session()->getId();
        
        // Simpan Data Ke Sesi secara spesifik
        session([
            'qr_code' => $qrCode->code,
            'qr_validated' => true,
            'session_start' => now()->toDateTimeString(),
            'qr_scan_time' => now()->toDateTimeString()
        ]);
        
        // Update scan count
        $qrCode->update([
            'current_session_id' => $newSessionId,
            'session_expires_at' => now()->addMinutes(60), // Lock selama 1 jam (atau sampai order selesai)
            'scan_count' => $qrCode->scan_count + 1,
            'last_scanned_at' => now()
        ]);

        // 🔥 FORCED OFFLINE MODE (REMOVED GEOLOCATION)
        $orderMode = 'offline';
        $distance = null;

        session([
            'order_mode' => $orderMode,
            'user_distance' => $distance
        ]);

        Log::info('QR Code valid: ' . $qrCode->code . " Mode: " . $orderMode);
        Log::info('New Session ID: ' . session()->getId());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true, 
                'redirect' => route('home'),
                'order_mode' => $orderMode
            ]); 
        }
        
        $message = 'Selamat datang! Silakan pesan makanan.'; 

        return redirect()->route('home')->with('success', $message);
    }

    /**
     * Cek validitas QR code via API
     */
    public function check(Request $request)
    {
        $code = $request->input('code');
        
        $qrCode = QrCodeModel::where('code', $code)
                        ->where('status', 'active')
                        ->first();

        if (!$qrCode) {
            return response()->json([
                'valid' => false,
                'message' => 'QR Code tidak ditemukan'
            ]);
        }

        if (!$qrCode->isValid()) {
            return response()->json([
                'valid' => false,
                'message' => 'QR Code sudah kadaluarsa'
            ]);
        }

        $activeOrder = Order::where('qr_code', $code)
            ->whereIn('order_status', ['waiting', 'processed'])
            ->exists();

        return response()->json([
            'valid' => true,
            'message' => 'QR Code valid',
            'has_active_order' => $activeOrder,
            'data' => [
                'code' => $qrCode->code,
                'meja' => $qrCode->meja,
                'status' => $qrCode->status,
                'expired_at' => $qrCode->expired_at
            ]
        ]);
    }

    /**
     * Reset sesi QR code secara manual
     */
    public function resetSession()
    {
        session()->forget(['qr_code', 'qr_validated', 'session_start', 'qr_scan_time', 'order_mode', 'cart']);
        return redirect()->route('scan.qr')->with('success', 'Sesi telah direset. Silakan scan ulang QR code meja Anda.');
    }
}