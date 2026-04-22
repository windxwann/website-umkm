<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Simpan cart dari localStorage ke session
     */
    public function saveToSession(Request $request)
    {
        $request->validate([
            'cart' => 'required|array'
        ]);

        try {
            // Simpan cart ke session
            session(['cart' => $request->cart]);
            
            // Hitung total
            $total = 0;
            foreach ($request->cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Cart saved to session',
                'cart_count' => count($request->cart),
                'total' => $total
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus cart dari session
     */
    public function clearSession(Request $request)
    {
        session()->forget('cart');
        return response()->json([
            'success' => true,
            'message' => 'Cart cleared from session'
        ]);
    }

    /**
     * Get cart dari session
     */
    public function getFromSession(Request $request)
    {
        $cart = session('cart', []);
        
        return response()->json([
            'success' => true,
            'cart' => $cart,
            'count' => count($cart)
        ]);
    }
}