<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CashierMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()
                ->route('admin.login')
                ->with('error', 'Silakan login terlebih dahulu');
        }

        if (!in_array($user->role, ['cashier', 'admin'])) {
            abort(403, 'Unauthorized access. Cashier only.');
        }

        return $next($request);
    }
}