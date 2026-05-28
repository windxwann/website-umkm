<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\QrCode;
use App\Models\PaymentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\CustomerSessionReset;
use App\Events\OrderCompleted;
use App\Events\OrderProcessed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of orders for cashier.
     */
    public function index(Request $request)
    {
        $query = Order::with('items');

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('order_status', $request->status);
        }

        // Filter by payment status
        if ($request->has('payment') && $request->payment != '') {
            $query->where('payment_status', $request->payment);
        }

        // Filter by date
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('created_at', $request->date);
        }

        // Search by order number
        if ($request->has('search') && $request->search != '') {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        $orders = $query->latest()->paginate(15);

        // ============================================
        // STATISTICS UNTUK CASHIER
        // ============================================
        
        $pendingPayment = Order::where('payment_status', 'pending')
            ->where('payment_method', 'cashier')
            ->count();
        
        $waitingOrders = Order::where('order_status', 'waiting')->count();
        $processedOrders = Order::where('order_status', 'processed')->count();
        $completedToday = Order::where('order_status', 'completed')
            ->whereDate('created_at', today())
            ->count();
        
        $todayRevenue = Order::whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $stats = [
            'pending_payment' => $pendingPayment,
            'waiting' => $waitingOrders,
            'processed' => $processedOrders,
            'completed_today' => $completedToday,
            'today_revenue' => $todayRevenue,
        ];

        return view('cashier.orders.index', compact('orders', 'stats'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load('items.product');
        return view('cashier.orders.show', compact('order'));
    }

    /**
     * UPDATE STATUS PESANAN
     */
    public function updateStatus(Request $request, Order $order)
    {
        if ($order->order_status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan yang sudah dibatalkan tidak dapat diubah lagi.'
            ], 422);
        }

        $request->validate([
            'status' => 'required|in:waiting,processed,completed,cancelled'
        ]);

        try {
            DB::beginTransaction();

            $oldStatus = $order->order_status;
            
            $order->update([
                'order_status' => $request->status
            ]);

            $statusText = [
                'waiting' => 'menunggu diproses',
                'processed' => 'sedang diproses',
                'completed' => 'selesai',
                'cancelled' => 'dibatalkan'
            ];

            PaymentNotification::create([
                'order_id' => $order->id,
                'type' => 'customer',
                'message' => "Status pesanan #{$order->order_number} berubah menjadi: {$statusText[$request->status]}"
            ]);

            // ============================================
            // LOGIKA BISNIS BERDASARKAN STATUS
            // ============================================
            switch ($request->status) {
                case 'processed':
                    PaymentNotification::create([
                        'order_id' => $order->id,
                        'type' => 'customer',
                        'message' => "Pesanan #{$order->order_number} sedang diproses. Mohon tunggu."
                    ]);
                    // 🔥 BROADCAST KE CUSTOMER DASHBOARD
                    try {
                        broadcast(new OrderProcessed($order));
                        Log::info('OrderProcessed broadcast sent for: ' . $order->order_number);
                    } catch (\Exception $e) {
                        Log::warning('Broadcast OrderProcessed failed: ' . $e->getMessage());
                    }
                    break;
                    
                case 'completed':
                    if ($order->payment_status !== 'paid') {
                        $order->update([
                            'payment_status' => 'paid',
                            'paid_at' => now(),
                            'paid_amount' => $order->total_amount
                        ]);
                    }
                    // 🔥 RESET SESSION CUSTOMER
                    if ($order->session_id) {
                        try {
                            event(new CustomerSessionReset($order->session_id));
                            Log::info('Session reset triggered for order: ' . $order->order_number);
                        } catch (\Exception $e) {
                            Log::error('Failed to trigger session reset: ' . $e->getMessage());
                        }
                    }
                    // 🔥 RESET QR SESSION LOCK
                    if ($order->qr_code) {
                        QrCode::where('code', $order->qr_code)->update([
                            'current_session_id' => null,
                            'session_expires_at' => null
                        ]);
                        Log::info('QR Session Lock released for: ' . $order->qr_code);
                    }
                    // 🔥 BROADCAST KE CUSTOMER DASHBOARD - NOTIFIKASI PESANAN SELESAI
                    try {
                        broadcast(new OrderCompleted($order));
                        Log::info('OrderCompleted broadcast sent for: ' . $order->order_number);
                    } catch (\Exception $e) {
                        Log::warning('Broadcast OrderCompleted failed: ' . $e->getMessage());
                    }
                    break;
                    
                case 'cancelled':
                    // Kembalikan stok jika pesanan dibatalkan
                    foreach ($order->items as $item) {
                        $product = Product::find($item->product_id);
                        if ($product) {
                            $product->increment('stock', $item->quantity);
                        }
                    }
                    $order->update([
                        'payment_status' => 'failed'
                    ]);

                    // 🔥 RESET SESSION CUSTOMER
                    if ($order->session_id) {
                        try {
                            event(new CustomerSessionReset($order->session_id));
                        } catch (\Exception $e) {
                            Log::error('Failed to trigger session reset in updateStatus: ' . $e->getMessage());
                        }
                    }

                    // 🔥 RESET QR SESSION LOCK
                    if ($order->qr_code) {
                        QrCode::where('code', $order->qr_code)->update([
                            'current_session_id' => null,
                            'session_expires_at' => null
                        ]);
                    }

                    // 🔥 BROADCAST KE CUSTOMER
                    try {
                        broadcast(new OrderCompleted($order));
                    } catch (\Exception $e) {
                        Log::warning('Broadcast failed in updateStatus (cancelled): ' . $e->getMessage());
                    }
                    break;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status pesanan berhasil diubah menjadi ' . $statusText[$request->status],
                'order' => $order
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET order details for AJAX
     */
    public function getOrderDetails(Order $order)
    {
        $order->load('items');
        
        return response()->json([
            'id' => $order->id,
            'order_number' => $order->order_number,
            'total_amount' => $order->total_amount,
            'customer_name' => $order->customer_name,
            'customer_phone' => $order->customer_phone,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'order_status' => $order->order_status,
            'qr_code' => $order->qr_code,
            'session_id' => $order->session_id,
            'created_at' => $order->created_at->format('d/m/Y H:i'),
            'items' => $order->items->map(function($item) {
                return [
                    'id' => $item->id,
                    'product_name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => $item->subtotal
                ];
            })
        ]);
    }

    /**
     * Confirm payment for order (non-cash payment)
     */
    public function confirmPayment(Request $request, Order $order)
    {
        if ($order->order_status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat mengkonfirmasi pembayaran untuk pesanan yang sudah dibatalkan.'
            ], 422);
        }

        if ($order->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan sudah dibayar'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $order->update([
                'payment_status' => 'paid',
                'paid_amount' => $order->total_amount,
                'paid_at' => now(),
                'order_status' => 'processed'
            ]);

            PaymentNotification::create([
                'order_id' => $order->id,
                'type' => 'customer',
                'message' => "Pembayaran pesanan #{$order->order_number} telah dikonfirmasi. Pesanan sedang diproses."
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dikonfirmasi',
                'order' => $order
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process cash payment
     */
    public function processCashPayment(Request $request, Order $order)
    {
        if ($order->order_status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat memproses pembayaran untuk pesanan yang sudah dibatalkan.'
            ], 422);
        }

        $request->validate([
            'amount_paid' => 'required|numeric|min:' . $order->total_amount
        ]);

        $change = $request->amount_paid - $order->total_amount;

        try {
            DB::beginTransaction();

            $order->update([
                'payment_status' => 'paid',
                'paid_amount' => $request->amount_paid,
                'paid_at' => now(),
                'order_status' => 'processed'
            ]);

            PaymentNotification::create([
                'order_id' => $order->id,
                'type' => 'customer',
                'message' => "Pembayaran tunai pesanan #{$order->order_number} berhasil. Kembalian: Rp " . number_format($change, 0, ',', '.')
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran tunai berhasil',
                'change' => $change,
                'order' => $order
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Print receipt for order
     */
    public function printReceipt(Order $order)
    {
        $order->load('items');
        return view('cashier.receipt', compact('order'));
    }

    /**
     * Cancel order
     */
    public function cancelOrder(Request $request, Order $order)
    {
        // Cashier can cancel even if paid (refund scenario handled manually)

        try {
            DB::beginTransaction();

            // Kembalikan stok produk
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }

            $order->update([
                'order_status' => 'cancelled',
                'payment_status' => 'failed'
            ]);

            // 🔥 RESET SESSION CUSTOMER
            if ($order->session_id) {
                try {
                    event(new CustomerSessionReset($order->session_id));
                    Log::info('Session reset from cancelOrder: ' . $order->order_number);
                } catch (\Exception $e) {
                    Log::error('Failed to trigger session reset in cancelOrder: ' . $e->getMessage());
                }
            }

            // 🔥 RESET QR SESSION LOCK
            if ($order->qr_code) {
                QrCode::where('code', $order->qr_code)->update([
                    'current_session_id' => null,
                    'session_expires_at' => null
                ]);
                Log::info('QR Session Lock released from cancelOrder for: ' . $order->qr_code);
            }

            PaymentNotification::create([
                'order_id' => $order->id,
                'type' => 'customer',
                'message' => "Pesanan #{$order->order_number} telah dibatalkan"
            ]);

            // 🔥 BROADCAST KE CUSTOMER DASHBOARD - NOTIFIKASI PESANAN DIBATALKAN (menggunakan event yang sama atau relevan)
            try {
                broadcast(new OrderCompleted($order)); // Customer page treats completed/cancelled similarly for redirection
            } catch (\Exception $e) {
                Log::warning('Broadcast failed in cancelOrder: ' . $e->getMessage());
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibatalkan'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get today's transactions summary
     */
    public function todayTransactions()
    {
        $today = now()->format('Y-m-d');
        
        $transactions = Order::whereDate('created_at', $today)
            ->with('items')
            ->get();

        $summary = [
            'total' => $transactions->count(),
            'revenue' => $transactions->where('payment_status', 'paid')->sum('total_amount'),
            'cash' => $transactions->where('payment_method', 'cashier')->where('payment_status', 'paid')->sum('total_amount'),
            'ewallet' => $transactions->where('payment_method', 'e_wallet')->where('payment_status', 'paid')->sum('total_amount'),
            'transfer' => $transactions->where('payment_method', 'bank_transfer')->where('payment_status', 'paid')->sum('total_amount'),
        ];

        return response()->json([
            'success' => true,
            'transactions' => $transactions,
            'summary' => $summary
        ]);
    }

    /**
     * Get order history with filters
     */
    public function transactionHistory(Request $request)
    {
        $query = Order::with('items');

        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->has('payment_method') && $request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        $orders = $query->latest()->paginate(20);

        return view('cashier.transactions.history', compact('orders'));
    }

    /**
     * Get active orders for a specific QR code
     */
    public function getActiveOrderByQr($qrCode)
    {
        $activeOrder = Order::where('qr_code', $qrCode)
            ->whereIn('order_status', ['waiting', 'processed'])
            ->with('items')
            ->first();

        if (!$activeOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada order aktif untuk QR code ini'
            ]);
        }

        return response()->json([
            'success' => true,
            'order' => $activeOrder
        ]);
    }

    /**
     * Mark order as completed (reset session)
     */
    public function markAsCompleted(Request $request, Order $order)
    {
        try {
            DB::beginTransaction();

            if (!in_array($order->order_status, ['processed', 'ready'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan harus dalam status "Diproses" atau "Siap" untuk diselesaikan'
                ], 400);
            }

            $order->update([
                'order_status' => 'completed',
                'completed_at' => now()
            ]);

            // 🔥 RESET SESSION CUSTOMER
            if ($order->session_id) {
                try {
                    event(new CustomerSessionReset($order->session_id));
                    Log::info('Session reset from markAsCompleted: ' . $order->order_number);
                } catch (\Exception $e) {
                    Log::error('Failed to trigger session reset: ' . $e->getMessage());
                }
            }

            // 🔥 RESET QR SESSION LOCK
            if ($order->qr_code) {
                QrCode::where('code', $order->qr_code)->update([
                    'current_session_id' => null,
                    'session_expires_at' => null
                ]);
                Log::info('QR Session Lock released from markAsCompleted for: ' . $order->qr_code);
            }

            PaymentNotification::create([
                'order_id' => $order->id,
                'type' => 'customer',
                'message' => "Pesanan #{$order->order_number} selesai. Terima kasih!"
            ]);

            // 🔥 BROADCAST KE CUSTOMER DASHBOARD - NOTIFIKASI PESANAN SELESAI
            try {
                broadcast(new OrderCompleted($order));
                Log::info('OrderCompleted broadcast sent from markAsCompleted: ' . $order->order_number);
            } catch (\Exception $e) {
                Log::warning('Broadcast OrderCompleted failed: ' . $e->getMessage());
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan selesai, meja direset untuk customer berikutnya'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * RESET MEJA OLEH CASHIER
     * Menyelesaikan semua pesanan aktif dan membersihkan meja untuk customer berikutnya
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetTable(Request $request)
    {
        $qrCode = $request->qr_code;
        
        if (!$qrCode) {
            return response()->json([
                'success' => false,
                'message' => 'QR code tidak ditemukan'
            ], 400);
        }
        
        try {
            DB::beginTransaction();
            
            // Ambil semua order aktif untuk QR code ini
            $activeOrders = Order::where('qr_code', $qrCode)
                ->whereIn('order_status', ['waiting', 'processed'])
                ->get();
            
            $completedCount = 0;
            
            foreach ($activeOrders as $order) {
                /** @var \App\Models\Order $order */
                // Jika sudah dibayar, tandai sebagai completed
                if ($order->payment_status === 'paid') {
                    $order->order_status = 'completed';
                    $order->is_archived_for_table = true;
                    $order->completed_at = now();
                    $order->notes = ($order->notes ? $order->notes . ' | ' : '') . 'Diselesaikan via reset meja oleh kasir pada ' . now()->format('d/m/Y H:i');
                    $order->save();
                    
                    $completedCount++;
                } else {
                    // Jika belum dibayar, batalkan dan kembalikan stok
                    foreach ($order->items as $item) {
                        $product = Product::find($item->product_id);
                        if ($product) {
                            $product->increment('stock', $item->quantity);
                        }
                    }
                    
                    $order->order_status = 'cancelled';
                    $order->payment_status = 'failed';
                    $order->is_archived_for_table = true;
                    $order->completed_at = now();
                    $order->notes = ($order->notes ? $order->notes . ' | ' : '') . 'Dibatalkan via reset meja oleh kasir pada ' . now()->format('d/m/Y H:i');
                    $order->save();
                }
                
                // Reset session customer
                if ($order->session_id) {
                    try {
                        event(new CustomerSessionReset($order->session_id));
                    } catch (\Exception $e) {
                        Log::warning('Failed to reset customer session: ' . $e->getMessage());
                    }
                }

                // Broadcast notifikasi ke customer
                try {
                    broadcast(new OrderCompleted($order));
                } catch (\Exception $e) {
                    Log::warning('Broadcast failed during table reset: ' . $e->getMessage());
                }
            }

            // JUGA ARSIPKAN ORDER YANG SUDAH COMPLETED/CANCELLED SEBELUMNYA
            Order::where('qr_code', $qrCode)
                ->where('is_archived_for_table', false)
                ->update([
                    'is_archived_for_table' => true,
                    'completed_at' => now()
                ]);
            
            // 🔥 RESET QR SESSION LOCK (UTAMA)
            QrCode::where('code', $qrCode)->update([
                'current_session_id' => null,
                'session_expires_at' => null
            ]);
            Log::info('QR Session Lock released via Table Reset for: ' . $qrCode);
            
            // Hitung total yang diproses
            $cancelledCount = $activeOrders->where('payment_status', 'pending')->count();
            
            DB::commit();
            
            Log::info('Cashier table reset successfully', [
                'qr_code' => $qrCode,
                'completed_count' => $completedCount,
                'cancelled_count' => $cancelledCount,
                'cashier_id' => Auth::user()?->id
            ]);
            
            return response()->json([
                'success' => true,
                'message' => "Meja berhasil direset! {$completedCount} pesanan diselesaikan, {$cancelledCount} pesanan dibatalkan.",
                'data' => [
                    'completed_count' => $completedCount,
                    'cancelled_count' => $cancelledCount
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cashier table reset failed', [
                'qr_code' => $qrCode,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mereset meja: ' . $e->getMessage()
            ], 500);
        }
    }
}