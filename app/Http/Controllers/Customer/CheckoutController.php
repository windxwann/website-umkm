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
            // Validasi produk, ketersediaan, dan harga dari database
            $totalAmount = 0;
            $validatedItems = [];
            
            foreach ($request->items as $item) {
                $product = \App\Models\Product::find($item['id']);
                
                // 1. Cek apakah produk ada dan sedang tersedia (tidak habis)
                if (!$product || !$product->is_available) {
                    throw new \Exception("Mohon maaf, menu {$item['name']} saat ini sedang tidak tersedia.");
                }
                
                // 2. Cek ketersediaan stok (jika sistem menggunakan kolom stok)
                if ($product->stock !== null && $product->stock < $item['quantity']) {
                    throw new \Exception("Stok {$product->name} tidak mencukupi. Tersisa: {$product->stock}");
                }
                
                // 3. Gunakan HARGA ASLI dan NAMA ASLI dari database, bukan dari input user
                $realPrice = $product->price;
                $quantity = $item['quantity'];
                
                $totalAmount += $realPrice * $quantity;
                
                // Simpan ke array sementara untuk dimasukkan ke OrderItem nanti
                $validatedItems[] = [
                    'product' => $product,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                    'price' => $realPrice,
                    'subtotal' => $realPrice * $quantity
                ];
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
            
            // Simpan setiap item pesanan dan kurangi stok
            foreach ($validatedItems as $vItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $vItem['product_id'],
                    'product_name' => $vItem['product_name'],
                    'quantity' => $vItem['quantity'],
                    'price' => $vItem['price'],
                    'subtotal' => $vItem['subtotal']
                ]);
                
                // 4. Kurangi stok produk secara otomatis (jika menggunakan stok)
                if ($vItem['product']->stock !== null) {
                    $vItem['product']->decrement('stock', $vItem['quantity']);
                }
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