<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Show today's transactions.
     */
    public function today(Request $request)
    {
        $query = Order::whereDate('created_at', today())
            ->with(['items', 'table']); // Eager load table relation

        // Apply filters
        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $transactions = $query->latest()->paginate(10);
        
        // 🔥 Summary data
        $allTodayTransactions = Order::whereDate('created_at', today())->get();
        
        $summary = [
            'total' => $allTodayTransactions->count(),
            'revenue' => $allTodayTransactions->where('payment_status', 'paid')->sum('total_amount'),
            'cash' => $allTodayTransactions->where('payment_method', 'cashier')->where('payment_status', 'paid')->sum('total_amount'),
            'ewallet' => $allTodayTransactions->where('payment_method', 'e_wallet')->where('payment_status', 'paid')->sum('total_amount'),
            'transfer' => $allTodayTransactions->where('payment_method', 'bank_transfer')->where('payment_status', 'paid')->sum('total_amount'),
        ];

        return view('cashier.transactions.today', compact('transactions', 'summary'));
    }

    /**
     * Show transaction history.
     */
    public function history(Request $request)
    {
        $query = Order::with(['items', 'table']); // Eager load table

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
            $query->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $request->search . '%');
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
        
        // Data untuk tanggal yang dipilih dengan relasi table
        $transactions = Order::whereDate('created_at', $selectedDate)
            ->with(['table'])
            ->get();

        // Summary untuk tanggal yang dipilih
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

        // Statistik berdasarkan lokasi meja
        $summary['indoor_transactions'] = $transactions->filter(function($transaction) {
            return $transaction->table && $transaction->table->location === 'indoor';
        })->count();
        
        $summary['outdoor_transactions'] = $transactions->filter(function($transaction) {
            return $transaction->table && $transaction->table->location === 'outdoor';
        })->count();
        
        $summary['indoor_revenue'] = $transactions->filter(function($transaction) {
            return $transaction->table && $transaction->table->location === 'indoor' && $transaction->payment_status === 'paid';
        })->sum('total_amount');
        
        $summary['outdoor_revenue'] = $transactions->filter(function($transaction) {
            return $transaction->table && $transaction->table->location === 'outdoor' && $transaction->payment_status === 'paid';
        })->sum('total_amount');

        // Hitung persentase
        $total = $summary['total_transactions'] ?: 1;
        $summary['waiting_percentage'] = round(($summary['waiting_count'] / $total) * 100);
        $summary['processed_percentage'] = round(($summary['processed_count'] / $total) * 100);
        $summary['completed_percentage'] = round(($summary['completed_count'] / $total) * 100);
        $summary['cancelled_percentage'] = round(($summary['cancelled_count'] / $total) * 100);

        // Data untuk grafik (7 hari terakhir) - menggunakan Query Builder untuk kompatibilitas
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

        // Data peak hours (jam sibuk) - menggunakan Query Builder
        $peakHours = Order::whereDate('created_at', $selectedDate)
            ->select(
                DB::raw("strftime('%H', created_at) as hour"),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->map(function($item) use ($transactions) {
                $total = $transactions->count() ?: 1;
                return [
                    'hour' => sprintf('%02d:00', $item->hour),
                    'count' => $item->count,
                    'percentage' => round(($item->count / $total) * 100)
                ];
            });

        // Data transaksi per jam untuk hari yang dipilih
        $hourlyTransactions = Order::whereDate('created_at', $selectedDate)
            ->select(
                DB::raw("strftime('%H', created_at) as hour"),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->map(function($item) {
                return [
                    'hour' => sprintf('%02d:00', $item->hour),
                    'count' => $item->count,
                    'revenue' => $item->revenue
                ];
            });

        return view('cashier.transactions.daily-summary', compact(
            'transactions', 
            'summary', 
            'selectedDate',
            'weeklyData',
            'peakHours',
            'hourlyTransactions'
        ));
    }

    /**
     * Show order detail
     */
    public function show(Order $order)
    {
        $order->load(['items.product', 'table']);
        return view('cashier.orders.show', compact('order'));
    }

    /**
     * Show receipt
     */
    public function receipt(Order $order)
    {
        $order->load(['items.product', 'table']);
        return view('cashier.receipt', compact('order'));
    }

    /**
     * Edit order
     */
    public function edit(Order $order)
    {
        if ($order->order_status === 'completed' || $order->order_status === 'cancelled') {
            return redirect()->back()->with('error', 'Transaksi yang sudah selesai atau dibatalkan tidak dapat diedit.');
        }
        
        $products = Product::where('is_active', true)->get();
        $order->load(['items.product']);
        
        return view('cashier.orders.edit', compact('order', 'products'));
    }

    /**
     * Update order
     */
    public function update(Request $request, Order $order)
    {
        if ($order->order_status === 'completed' || $order->order_status === 'cancelled') {
            return redirect()->back()->with('error', 'Transaksi yang sudah selesai atau dibatalkan tidak dapat diedit.');
        }
        
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'table_id' => 'nullable|exists:tables,id',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);
        
        DB::beginTransaction();
        try {
            // Update order info
            $order->update([
                'customer_name' => $request->customer_name,
                'table_id' => $request->table_id,
            ]);
            
            // Update items
            $updatedItems = [];
            $totalAmount = 0;
            
            foreach ($request->items as $item) {
                $product = Product::find($item['id']);
                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;
                
                $updatedItems[$item['id']] = [
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $subtotal
                ];
            }
            
            // Sync items
            $order->items()->sync($updatedItems);
            
            // Update total amount
            $order->update(['total_amount' => $totalAmount]);
            
            DB::commit();
            
            return redirect()->route('cashier.order.show', $order)
                ->with('success', 'Order berhasil diupdate');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal mengupdate order: ' . $e->getMessage());
        }
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