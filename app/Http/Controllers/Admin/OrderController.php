<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Product;
use App\Models\Category;

class OrderController extends Controller
{
    /**
     * Display list of orders
     */
    public function index(Request $request)
    {
        $query = Order::with('items');

        if ($request->status) {
            $query->where('order_status', $request->status);
        }

        if ($request->payment) {
            $query->where('payment_status', $request->payment);
        }

        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $request->search . '%');
            });
        }

        $orders = $query->latest()->paginate(15);

        $stats = [
            'total_orders' => Order::count(),
            'pending_payment' => Order::where('payment_status', 'pending')->count(),
            'waiting' => Order::where('order_status', 'waiting')->count(),
            'processed' => Order::where('order_status', 'processed')->count(),
            'completed' => Order::where('order_status', 'completed')->count(),
            'cancelled' => Order::where('order_status', 'cancelled')->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'today_revenue' => Order::whereDate('created_at', today())
                ->where('payment_status', 'paid')
                ->sum('total_amount'),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }


    /**
     * Show create page
     */
    public function create()
    {
        $products = Product::all();

        $categories = Category::with('products')->get();

        return view('admin.orders.create', compact('products', 'categories'));
    }

    /**
     * Store order (optional)
     */
    public function store(Request $request)
    {
        return redirect()->route('admin.orders.index')
            ->with('success', 'Order berhasil dibuat');
    }


    /**
     * Show order detail
     */
    public function show(Order $order)
    {
        $order->load('items.product');
        return view('admin.orders.show', compact('order'));
    }


    /**
     * Show edit page
     */
    public function edit(Order $order)
    {
        return view('admin.orders.edit', compact('order'));
    }


    /**
     * Update order
     */
    public function update(Request $request, Order $order)
    {
        $order->update($request->all());

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order berhasil diupdate');
    }


    /**
     * Delete order
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order berhasil dihapus');
    }


    /**
     * Update status
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

        $oldStatus = $order->order_status;
        $order->update([
            'order_status' => $request->status
        ]);

        // LOGIKA PENUNJANG
        if ($request->status === 'cancelled' || $request->status === 'completed') {
            // 1. Kembalikan Stok jika dibatalkan
            if ($request->status === 'cancelled') {
                foreach ($order->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                    }
                }
                
                // Tandai Pembayaran Gagal
                $order->update(['payment_status' => 'failed']);
            }
            
            // 2. Reset Session Customer & QR Lock
            if ($order->session_id) {
                try {
                    event(new \App\Events\CustomerSessionReset($order->session_id));
                } catch (\Exception $e) {}
            }
            if ($order->qr_code) {
                \App\Models\QrCode::where('code', $order->qr_code)->update([
                    'current_session_id' => null,
                    'session_expires_at' => null
                ]);
            }
            
            // 3. Broadcast Notification
            try {
                broadcast(new \App\Events\OrderCompleted($order));
            } catch (\Exception $e) {}
        }

        PaymentNotification::create([
            'order_id' => $order->id,
            'type' => 'customer',
            'message' => "Status pesanan #{$order->order_number} diubah menjadi {$request->status}"
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diupdate'
        ]);
    }


    /**
     * Confirm payment
     */
    public function confirmPayment(Order $order)
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
                'message' => 'Sudah dibayar'
            ], 400);
        }

        DB::beginTransaction();

        try {

            $order->update([
                'payment_status' => 'paid',
                'paid_amount' => $order->total_amount,
                'paid_at' => now(),
                'order_status' => 'processed'
            ]);

            PaymentNotification::create([
                'order_id' => $order->id,
                'type' => 'customer',
                'message' => "Pembayaran pesanan #{$order->order_number} dikonfirmasi"
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran dikonfirmasi'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Print invoice
     */
    public function printInvoice(Order $order)
    {
        $order->load('items');
        return view('admin.orders.invoice', compact('order'));
    }


    /**
     * Export PDF
     */
    public function exportPdf(Order $order)
    {
        $order->load('items');

        $pdf = Pdf::loadView('admin.orders.pdf', compact('order'));

        return $pdf->download('invoice-' . $order->order_number . '.pdf');
    }
}
