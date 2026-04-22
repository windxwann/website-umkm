<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSessionAge
{
    public function handle(Request $request, Closure $next)
    {
        if (session()->has('session_start')) {
            $sessionAge = now()->diffInMinutes(session('session_start'));
            
            // Jika session lebih dari 3 jam, hapus
            if ($sessionAge > 180) {
                session()->forget(['qr_code', 'qr_validated', 'session_id', 'session_start']);
                return redirect()->route('scan.qr')->with('error', 'Sesi habis, silakan scan ulang');
            }
        }
        
        return $next($request);
    }
}