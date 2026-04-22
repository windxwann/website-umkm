<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function reset()
    {
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
        return response()->json([
            'valid' => session()->has('qr_validated'),
            'qr_code' => session('qr_code')
        ]);
    }
}