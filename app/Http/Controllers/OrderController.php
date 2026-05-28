<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\NewOrderNotification;
use App\Events\PaymentNotification;
use App\Events\OrderProcessed;
use App\Events\OrderCompleted;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Menampilkan halaman form pemesanan dengan data cart dari session
     */
    public function create()
    {
        $categories = Category::with('products')->get();
        $products = Product::all();
        
        // Ambil data cart dari session
        $cartItems = session('cart', []);
        $orderMode = session('order_mode', 'offline');
        $packagingFee = setting('packaging_fee', 2000);
        
        Log::info('Order create page loaded', [
            'cart_count' => count($cartItems),
            'order_mode' => $orderMode,
            'session_id' => session()->getId()
        ]);
        
        return view('order.create', compact('categories', 'products', 'cartItems', 'orderMode', 'packagingFee'));
    }

    /**
     * Menyimpan order baru ke database
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'payment_method' => 'required|in:e_wallet,cashier,bank_transfer',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Hitung total amount
            $totalAmount = 0;
            $orderItems = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Cek ketersediaan produk
                if (!$product->is_available) {
                    return response()->json([
                        'success' => false,
                        'message' => "Produk {$product->name} sedang tidak tersedia"
                    ], 400);
                }
                
                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal
                ];
            }

            // Cek apakah meja masih ada order aktif
            if (session()->has('qr_code')) {
                $activeOrder = Order::where('qr_code', session('qr_code'))
                    ->whereIn('order_status', ['waiting', 'processed'])
                    ->exists();
                    
                if ($activeOrder) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Meja ini masih memiliki pesanan aktif. Silakan selesaikan pesanan sebelumnya.'
                    ], 400);
                }
            }

            // 🔥 FORCED OFFLINE MODE
            $finalOrderType = 'offline';
            $qrCode = session('qr_code');
            $packagingFee = 0;

            // Buat order baru
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'qr_code' => $qrCode,
                'session_id' => session()->getId(),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'order_type' => $finalOrderType,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'order_status' => 'waiting',
                'total_amount' => $totalAmount,
                'packaging_fee' => $packagingFee,
                'delivery_address' => null,
                'paid_amount' => 0,
                'notes' => $request->notes,
                'payment_due_at' => $request->payment_method === 'cashier' ? now()->addMinutes(30) : null
            ]);

            // Simpan items order
            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            // 🔥 TRIGGER REAL-TIME NOTIFICATION untuk order baru
            try {
                broadcast(new NewOrderNotification($order));
                
                // Jika pembayaran via kasir, kirim notifikasi pembayaran juga
                if ($order->payment_method === 'cashier') {
                    // Simpan ke database agar muncul di dropdown
                    \App\Models\PaymentNotification::create([
                        'order_id' => $order->id,
                        'type' => 'cashier',
                        'message' => "Pesanan baru #{$order->order_number} menunggu pembayaran di kasir."
                    ]);
                    
                    broadcast(new PaymentNotification($order));
                }
            } catch (\Exception $e) {
                Log::warning('Broadcast notification failed: ' . $e->getMessage());
            }

            // 🔥 Hapus cart dari session dan localStorage
            session()->forget('cart');
            
            // Simpan order_number ke session untuk tracking
            session(['last_order' => $order->order_number]);

            DB::commit();

            Log::info('Order created successfully', [
                'order_number' => $order->order_number,
                'order_id' => $order->id,
                'session_id' => session()->getId(),
                'total_amount' => $totalAmount
            ]);

            return response()->json([
                'success' => true,
                'redirect' => route('order.success', $order->order_number),
                'order_number' => $order->order_number,
                'clear_cart' => true // 🔥 Tanda untuk clear cart di frontend
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan halaman sukses setelah order
     */
    public function success($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
                     ->with('items')
                     ->firstOrFail();

        // Validasi: Pastikan order ini milik session yang sama
        if ($order->session_id !== session()->getId() && $order->qr_code !== session('qr_code')) {
            Log::warning('Unauthorized access attempt to order success', [
                'order_number' => $orderNumber,
                'session_id' => session()->getId(),
                'order_session_id' => $order->session_id
            ]);
            abort(403, 'Anda tidak berhak mengakses pesanan ini');
        }

        return view('order.success', compact('order'));
    }

    /**
     * Menampilkan halaman pembayaran
     */
    public function payment($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
                     ->with('items')
                     ->firstOrFail();

        // Validasi: Pastikan order ini milik session yang sama
        if ($order->session_id !== session()->getId() && $order->qr_code !== session('qr_code')) {
            Log::warning('Unauthorized access attempt to payment page', [
                'order_number' => $orderNumber,
                'session_id' => session()->getId(),
                'order_session_id' => $order->session_id
            ]);
            abort(403, 'Anda tidak berhak mengakses pesanan ini');
        }

        // Jika sudah dibayar, redirect ke halaman sukses
        if ($order->payment_status === 'paid') {
            return redirect()->route('order.success', $order->order_number);
        }

        return view('order.payment', compact('order'));
    }

    /**
     * Proses pembayaran (untuk e-wallet)
     */
    public function processPayment(Request $request, $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        // Validasi: Pastikan order ini milik session yang sama
        if ($order->session_id !== session()->getId() && $order->qr_code !== session('qr_code')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak berhak memproses pesanan ini'
            ], 403);
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('order.success', $order->order_number);
        }

        if ($order->payment_method === 'e_wallet') {
            try {
                DB::beginTransaction();

                $order->update([
                    'payment_status' => 'paid',
                    'paid_amount' => $order->total_amount,
                    'paid_at' => now(),
                    'order_status' => 'processed'
                ]);

                // 🔥 TRIGGER NOTIFIKASI ORDER DIPROSES
                try {
                    broadcast(new OrderProcessed($order));
                } catch (\Exception $e) {
                    Log::warning('Broadcast OrderProcessed failed: ' . $e->getMessage());
                }

                DB::commit();

                return redirect()->route('order.success', $order->order_number)
                               ->with('success', 'Pembayaran berhasil');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('E-wallet payment failed', [
                    'order_number' => $orderNumber,
                    'error' => $e->getMessage()
                ]);
                return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        }

        return back()->with('error', 'Metode pembayaran tidak valid');
    }

    /**
     * Konfirmasi bahwa pelanggan sudah membayar (QRIS atau Transfer)
     * Ini dipanggil via AJAX untuk memberi notifikasi ke Admin
     */
    public function confirmPaymentIntent(Request $request, $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        // Validasi session/qr_code
        if ($order->session_id !== session()->getId() && $order->qr_code !== session('qr_code')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // 🔥 SAVE TO DATABASE
            \App\Models\PaymentNotification::create([
                'order_id' => $order->id,
                'type' => 'cashier',
                'message' => "Pelanggan mengklaim sudah bayar via " . ($order->payment_method === 'e_wallet' ? 'QRIS' : 'Transfer') . ". Mohon cek dan konfirmasi pesanan #{$order->order_number}."
            ]);

            // 🔥 TRIGGER REAL-TIME NOTIFICATION KE ADMIN
            broadcast(new PaymentNotification($order, 'customer_claim'));
            
            Log::info('Payment confirmation intent sent by customer', [
                'order_number' => $orderNumber,
                'payment_method' => $order->payment_method
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi sudah dikirim ke kasir. Mohon tunggu konfirmasi.'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to broadcast payment intent: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengirim notifikasi'], 500);
        }
    }

    /**
     * Update status order (dipanggil dari admin/kasir)
     */
    public function updateStatus(Request $request, $orderNumber)
    {
        $request->validate([
            'order_status' => 'required|in:waiting,processed,completed,cancelled',
            'payment_status' => 'nullable|in:pending,paid',
            'notes' => 'nullable|string'
        ]);

        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        if ($order->order_status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan yang sudah dibatalkan tidak dapat diubah lagi.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $oldStatus = $order->order_status;
            $newStatus = $request->order_status;

            // Update order
            $order->order_status = $newStatus;
            
            if ($request->has('payment_status')) {
                $order->payment_status = $request->payment_status;
                
                if ($request->payment_status === 'paid' && !$order->paid_at) {
                    $order->paid_at = now();
                    $order->paid_amount = $order->total_amount;
                }
            }
            
            if ($request->has('notes')) {
                $order->notes = $request->notes;
            }
            
            $order->save();

            // Catat history status
            $order->statusHistories()->create([
                'status' => $newStatus,
                'notes' => $request->notes,
                'user_id' => Auth::id(),
                'old_status' => $oldStatus
            ]);

            // 🔥 RESET SESSION & QR LOCK IF COMPLETED OR CANCELLED
            if ($newStatus === 'completed' || $newStatus === 'cancelled') {
                if ($order->qr_code) {
                    \App\Models\QrCode::where('code', $order->qr_code)->update([
                        'current_session_id' => null,
                        'session_expires_at' => null
                    ]);
                    \Log::info("QR session lock released via status update: {$order->qr_code}");
                }
                
                if ($order->session_id) {
                    try {
                        event(new \App\Events\CustomerSessionReset($order->session_id));
                    } catch (\Exception $e) {
                        \Log::warning("CustomerSessionReset event failed: " . $e->getMessage());
                    }
                }
            }

            // 🔥 TRIGGER NOTIFIKASI BERDASARKAN STATUS
            try {
                if ($newStatus === 'processed' && $oldStatus !== 'processed') {
                    broadcast(new OrderProcessed($order));
                } elseif ($newStatus === 'completed' && $oldStatus !== 'completed') {
                    broadcast(new OrderCompleted($order));
                } elseif ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                    broadcast(new OrderCompleted($order)); // Notify completion/cancellation
                }
            } catch (\Exception $e) {
                Log::warning('Broadcast status update failed: ' . $e->getMessage());
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status pesanan berhasil diupdate',
                'order' => $order
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order status update failed', [
                'order_number' => $orderNumber,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cek order baru yang selesai (untuk AJAX polling)
     */
    public function checkNewCompletedOrders(Request $request)
    {
        $sessionId = session()->getId();
        $qrCode = session('qr_code');

        // Order yang completed dalam 5 menit terakhir
        $completedOrders = Order::where('is_archived_for_table', false)
            ->where(function($q) use ($sessionId, $qrCode) {
                $q->where('session_id', $sessionId);
                if ($qrCode) {
                    $q->orWhere('qr_code', $qrCode);
                }
            })
            ->where('order_status', 'completed')
            ->where('updated_at', '>=', now()->subMinutes(5))
            ->get(['order_number', 'order_status', 'payment_status']);

        // Order yang processed dalam 5 menit terakhir
        $processedOrders = Order::where('is_archived_for_table', false)
            ->where(function($q) use ($sessionId, $qrCode) {
                $q->where('session_id', $sessionId);
                if ($qrCode) {
                    $q->orWhere('qr_code', $qrCode);
                }
            })
            ->where('order_status', 'processed')
            ->where('updated_at', '>=', now()->subMinutes(5))
            ->get(['order_number', 'order_status', 'payment_status']);

        return response()->json([
            'success' => true,
            'completed_orders' => $completedOrders,
            'processed_orders' => $processedOrders,
            'has_new_completed' => $completedOrders->count() > 0,
            'has_new_processed' => $processedOrders->count() > 0
        ]);
    }

    /**
     * Generate nomor order unik
     */
    private function generateOrderNumber()
    {
        $date = date('Ymd');
        $lastOrder = Order::whereDate('created_at', today())
                         ->orderBy('id', 'desc')
                         ->first();
        
        if ($lastOrder) {
            $lastNumber = intval(substr($lastOrder->order_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return 'ORD-' . $date . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Cek status order (untuk API)
     */
    public function checkStatus($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
                     ->select('order_number', 'order_status', 'payment_status', 'updated_at')
                     ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * Mendapatkan data cart dari session (untuk AJAX)
     */
    public function getCartFromSession()
    {
        $cart = session('cart', []);
        
        return response()->json([
            'success' => true,
            'cart' => $cart,
            'count' => count($cart)
        ]);
    }

    /**
     * Menghapus cart dari session
     */
    public function clearCartFromSession()
    {
        session()->forget('cart');
        
        return response()->json([
            'success' => true,
            'message' => 'Cart cleared from session'
        ]);
    }

    /**
     * 🔥 RESET CART (untuk dipanggil dari frontend setelah checkout)
     */
    public function resetCart(Request $request)
    {
        session()->forget('cart');
        
        return response()->json([
            'success' => true,
            'message' => 'Cart has been reset'
        ]);
    }
}