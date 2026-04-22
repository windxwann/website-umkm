<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Get customer identifier from session (qr code or phone)
        $qrCode = session('qr_code');
        
        // Ambil SEMUA order yang belum diarsip untuk statistik
        $allOrders = Order::where('is_archived_for_table', false)
                      ->where(function($query) use ($qrCode) {
                          $query->where('qr_code', $qrCode)
                                ->orWhere('customer_phone', session('customer_phone'));
                      })
                      ->with('items')
                      ->latest()
                      ->get();
        
        // Filter HANYA order aktif untuk daftar history/tabel
        $activeOrders = $allOrders->whereIn('order_status', ['waiting', 'processed']);
        
        $stats = [
            'total_orders' => $allOrders->count(),
            'pending_orders' => $allOrders->where('order_status', 'waiting')->count(),
            'completed_orders' => $allOrders->where('order_status', 'completed')->count(),
            'total_spent' => $allOrders->where('payment_status', 'paid')->sum('total_amount')
        ];
        
        return view('customer.dashboard', [
            'orders' => $activeOrders, // Kirim hanya yang aktif ke tabel
            'stats' => $stats
        ]);
    }
    
    public function trackOrder($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
                    ->with('items')
                    ->firstOrFail();
        
        // Validasi akses
        if ($order->qr_code !== session('qr_code') && $order->session_id !== session()->getId()) {
            abort(403, 'Anda tidak berhak mengakses pesanan ini');
        }
        
        return view('customer.track-order', compact('order'));
    }
}