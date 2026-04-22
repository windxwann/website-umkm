<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Show today's transactions.
     */
    public function today()
    {
        $transactions = Order::whereDate('created_at', today())
            ->with('items')
            ->latest()
            ->get();

        // 🔥 UBAH KE ARRAY (bukan object)
        $summary = [
            'total' => $transactions->count(),
            'revenue' => $transactions->where('payment_status', 'paid')->sum('total_amount'),
            'cash' => $transactions->where('payment_method', 'cashier')->where('payment_status', 'paid')->sum('total_amount'),
            'ewallet' => $transactions->where('payment_method', 'e_wallet')->where('payment_status', 'paid')->sum('total_amount'),
            'transfer' => $transactions->where('payment_method', 'bank_transfer')->where('payment_status', 'paid')->sum('total_amount'),
        ];

        return view('cashier.transactions.today', compact('transactions', 'summary'));
    }

    /**
     * Show transaction history.
     */
    public function history(Request $request)
    {
        $query = Order::with('items');

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('order_status')) {
            $query->where('order_status', $request->order_status);
        }

        if ($request->filled('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        $orders = $query->latest()->paginate(20);
        
        $orders->appends($request->all());

        return view('cashier.transactions.history', compact('orders'));
    }

    /**
     * Show daily summary.
     */
    public function dailySummary(Request $request)
    {
        $selectedDate = $request->input('date', today()->format('Y-m-d'));
        
        // Data untuk tanggal yang dipilih
        $transactions = Order::whereDate('created_at', $selectedDate)
            ->with('user')
            ->get();

        // Summary untuk tanggal yang dipilih (tetap array)
        $summary = [
            'total_transactions' => $transactions->count(),
            'total_revenue' => $transactions->where('payment_status', 'paid')->sum('total_amount'),
            'cash_revenue' => $transactions->where('payment_method', 'cashier')->where('payment_status', 'paid')->sum('total_amount'),
            'ewallet_revenue' => $transactions->where('payment_method', 'e_wallet')->where('payment_status', 'paid')->sum('total_amount'),
            'transfer_revenue' => $transactions->where('payment_method', 'bank_transfer')->where('payment_status', 'paid')->sum('total_amount'),
            'cash_count' => $transactions->where('payment_method', 'cashier')->count(),
            'ewallet_count' => $transactions->where('payment_method', 'e_wallet')->count(),
            'transfer_count' => $transactions->where('payment_method', 'bank_transfer')->count(),
            'waiting_count' => $transactions->where('order_status', 'waiting')->count(),
            'processed_count' => $transactions->where('order_status', 'processed')->count(),
            'completed_count' => $transactions->where('order_status', 'completed')->count(),
            'cancelled_count' => $transactions->where('order_status', 'cancelled')->count(),
        ];

        // Hitung persentase
        $total = $summary['total_transactions'] ?: 1;
        $summary['waiting_percentage'] = round(($summary['waiting_count'] / $total) * 100);
        $summary['processed_percentage'] = round(($summary['processed_count'] / $total) * 100);
        $summary['completed_percentage'] = round(($summary['completed_count'] / $total) * 100);
        $summary['cancelled_percentage'] = round(($summary['cancelled_count'] / $total) * 100);

        // Data untuk grafik (7 hari terakhir)
        $weeklyData = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN payment_status = "paid" THEN total_amount ELSE 0 END) as revenue')
            )
            ->whereDate('created_at', '>=', now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Data peak hours (jam sibuk)
        $peakHours = Order::whereDate('created_at', $selectedDate)
            ->select(
                DB::raw('strftime("%H:00", created_at) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->map(function($item) use ($transactions) {
                $total = $transactions->count() ?: 1;
                return [
                    'hour' => $item->hour,
                    'count' => $item->count,
                    'percentage' => round(($item->count / $total) * 100)
                ];
            });

        return view('cashier.transactions.daily-summary', compact(
            'transactions', 
            'summary', 
            'selectedDate',
            'weeklyData',
            'peakHours'
        ));
    }

    /**
     * Export transactions to Excel
     */
    public function exportExcel(Request $request)
    {
        return redirect()->back()->with('info', 'Fitur export Excel akan segera tersedia');
    }

    /**
     * Export transactions to PDF
     */
    public function exportPDF(Request $request)
    {
        return redirect()->back()->with('info', 'Fitur export PDF akan segera tersedia');
    }
}