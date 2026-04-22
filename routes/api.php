<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/check-active-order/{qrCode}', function($qrCode) {
    $activeOrder = App\Models\Order::where('qr_code', $qrCode)
        ->whereIn('order_status', ['waiting', 'processed'])
        ->exists();
        
    return response()->json(['active' => $activeOrder]);
});