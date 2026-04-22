<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()
                ->route('admin.login')
                ->with('error', 'Silakan login terlebih dahulu');
        }

        if (($user->role ?? null) !== 'admin') {
            abort(403, 'Unauthorized access. Admin only.');
        }

        return $next($request);
    }
}