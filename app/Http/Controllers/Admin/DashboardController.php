<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ============================================
        // 1. STATISTIK PENDAPATAN & PESANAN
        // ============================================
        
        // Pendapatan hari ini (order yang sudah lunas)
        $todayRevenue = Order::whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total_amount');
        
        // Total pesanan hari ini
        $todayOrders = Order::whereDate('created_at', today())->count();
        
        // Pesanan menunggu diproses
        $pendingOrders = Order::where('order_status', 'waiting')->count();
        
        // Pesanan diproses
        $processedOrders = Order::where('order_status', 'processed')->count();
        
        // Pesanan selesai
        $completedOrders = Order::where('order_status', 'completed')->count();
        
        // Pesanan dibatalkan
        $cancelledOrders = Order::where('order_status', 'cancelled')->count();
        
        // Total pesanan
        $totalOrders = Order::count();
        
        // ============================================
        // 2. STATISTIK PRODUK
        // ============================================
        
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_available', true)->count();
        
        // ============================================
        // 3. GRAFIK PENJUALAN 7 HARI TERAKHIR
        // ============================================
        
        $chartLabels = [];
        $chartData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->format('d M');
            
            $revenue = Order::whereDate('created_at', $date)
                ->where('payment_status', 'paid')
                ->sum('total_amount');
            
            $chartData[] = $revenue;
        }
        
        // ============================================
        // 4. PRODUK TERLARIS
        // ============================================
        
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();
        
        // Hitung persentase untuk progress bar
        $maxSold = $topProducts->isNotEmpty() ? $topProducts->first()->total_sold : 1;
        foreach ($topProducts as $product) {
            $product->percentage = ($product->total_sold / $maxSold) * 100;
        }
        
        // ============================================
        // 5. PESANAN TERBARU
        // ============================================
        
        $recentOrders = Order::with('items')
            ->latest()
            ->limit(10)
            ->get();
        
        // ============================================
        // 6. STATISTIK TAMBAHAN
        // ============================================
        
        $totalCategories = Category::count();
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        
        // Metode pembayaran yang sering digunakan
        $paymentMethods = Order::select('payment_method', DB::raw('count(*) as total'))
            ->groupBy('payment_method')
            ->get();
        
        // ============================================
        // 7. DATA UNTUK VIEW
        // ============================================
        
        $stats = [
            'today_revenue' => $todayRevenue,
            'today_orders' => $todayOrders,
            'pending_orders' => $pendingOrders,
            'processed_orders' => $processedOrders,
            'completed_orders' => $completedOrders,
            'cancelled_orders' => $cancelledOrders,
            'total_orders' => $totalOrders,
            'total_products' => $totalProducts,
            'active_products' => $activeProducts,
            'total_categories' => $totalCategories,
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
        ];
        
        return view('admin.dashboard', compact(
            'stats',
            'chartLabels',
            'chartData',
            'topProducts',
            'recentOrders',
            'paymentMethods'
        ));
    }
}