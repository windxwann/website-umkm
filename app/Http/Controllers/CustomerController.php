<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QrCode;

class CustomerController extends Controller
{
    public function reset()
    {
        // 🔥 TAMBAHAN: Lepaskan kunci di tabel QR Codes
        if (session()->has('qr_code')) {
            \App\Models\QrCode::where('code', session('qr_code'))->update([
                'current_session_id' => null,
                'session_expires_at' => null
            ]);
        }

        session()->forget([
            'qr_code',
            'qr_validated',
            'session_id',
            'session_start',
            'cart',
            'customer_name',
            'customer_phone',
            'last_order'
        ]);
        
        session()->regenerate();
        
        return response()->json([
            'success' => true,
            'message' => 'Session berhasil direset'
        ]);
    }
    
    public function checkSession()
    {
        if (!session()->has('qr_validated') || !session()->has('qr_code')) {
            return response()->json(['valid' => false]);
        }
        
        $qrCode = QrCode::where('code', session('qr_code'))->first();
        if (!$qrCode || !$qrCode->isValid()) {
            return response()->json(['valid' => false]);
        }
        
        // Check session lock
        if ($qrCode->current_session_id && $qrCode->current_session_id !== session()->getId()) {
            return response()->json(['valid' => false]);
        }

        return response()->json([
            'valid' => true,
            'qr_code' => session('qr_code')
        ]);
    }
}