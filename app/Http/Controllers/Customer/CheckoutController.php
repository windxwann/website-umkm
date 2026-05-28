<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        return view('customer.checkout.index');
    }
    
    public function process(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string'
        ]);
        
        $qrCode = session('qr_code');
        if (!$qrCode) {
            return response()->json(['success' => false, 'message' => 'QR Code tidak ditemukan'], 400);
        }
        
        DB::beginTransaction();
        try {
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['price'] * $item['quantity'];
            }
            
            $order = Order::create([
                'order_number' => 'ORD-' . date('Ymd') . '-' . strtoupper(uniqid()),
                'qr_code' => $qrCode,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'notes' => $request->notes,
                'total_amount' => $totalAmount,
                'order_status' => 'waiting',
                'payment_status' => 'pending',
                'payment_method' => 'cashier'
            ]);
            
            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity']
                ]);
            }
            
            DB::commit();
            
            // Clear cart after successful checkout
            session()->forget('cart');
            
            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat!',
                'order_number' => $order->order_number
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}