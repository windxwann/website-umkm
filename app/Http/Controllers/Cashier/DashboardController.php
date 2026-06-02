<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // ============================================
        // STATISTICS UNTUK DASHBOARD KASIR
        // ============================================
        
        // Pending payments
        $pendingPayments = Order::where('payment_status', 'pending')
            ->where('payment_method', 'cashier')
            ->count();
        
        // Waiting orders
        $waitingOrders = Order::where('order_status', 'waiting')->count();
        
        // Processed orders
        $processedOrders = Order::where('order_status', 'processed')->count();
        
        // Completed orders today
        $completedOrders = Order::where('order_status', 'completed')
            ->whereDate('created_at', today())
            ->count();
        
        // ============================================
        // PENDAPATAN HARI INI - YANG DIPERBAIKI
        // ============================================
        $todayRevenue = Order::whereDate('created_at', today())
            ->where('payment_status', 'paid')  // Hanya yang LUNAS
            ->sum('total_amount');

        $stats = [
            'pending_payments' => $pendingPayments,
            'waiting_orders' => $waitingOrders,
            'processed_orders' => $processedOrders,
            'completed_orders' => $completedOrders,
            'today_revenue' => $todayRevenue,
        ];

        // Recent orders untuk ditampilkan di dashboard
        $recentOrders = Order::with(['items', 'qrCodeRelation'])
            ->latest()
            ->limit(10)
            ->get();

        // ============================================
        // DATA MEJA (QR CODES) DENGAN PESANAN AKTIF
        // ============================================
        // DATA MEJA (QR CODES) DENGAN PESANAN AKTIF
        $tables = \App\Models\QrCode::where('status', 'active')
            ->with(['orders' => function($query) {
                $query->whereIn('order_status', ['waiting', 'processed'])
                      ->with('items');
            }])
            ->get()
            ->map(function ($qr) {
                // DEBUGGING: Cek apakah session_id terbaca
                \Log::info('Dashboard Debug - Meja: ' . $qr->code . ' | SessionID: ' . $qr->current_session_id);
                
                $activeOrders = $qr->orders;
                $completedOrders = Order::where('qr_code', $qr->code)
                    ->where('order_status', 'completed')
                    ->whereDate('created_at', today())
                    ->get();

                return (object) [
                    'qr_code' => $qr->code,
                    'meja' => $qr->meja ?? $qr->code,
                    'nama_tempat' => $qr->nama_tempat,
                    'active_orders_count' => $activeOrders->count(),
                    'active_orders' => $activeOrders,
                    'total_active_amount' => $activeOrders->sum('total_amount'),
                    'completed_today' => $completedOrders->count(),
                    'has_unpaid' => $activeOrders->where('payment_status', 'pending')->count() > 0,
                    // Perbaikan: Meja dianggap terkunci jika sesi masih aktif (is_locked = true)
                    'is_locked' => !empty($qr->current_session_id) || $activeOrders->count() > 0
                ];
            });
        return view('cashier.dashboard', compact('stats', 'recentOrders', 'tables'));
    }
}