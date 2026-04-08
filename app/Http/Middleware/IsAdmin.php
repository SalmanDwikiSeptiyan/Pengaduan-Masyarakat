<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->role !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Akses ditolak. Hanya admin yang diizinkan.',
                ], 403);
            }

            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Akses ditolak. Hanya admin yang diizinkan.']);
        }

        return $next($request);
    }
}
