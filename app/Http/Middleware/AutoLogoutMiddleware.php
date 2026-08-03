<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AutoLogoutMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $lastActivity = session('last_activity_time');
            $timeout = 900; // 15 minutes in seconds

            if ($lastActivity && (time() - $lastActivity > $timeout)) {
                Auth::logout();
                session()->flush();
                return redirect()->route('login')->with('warning', 'Sesi Anda telah berakhir karena tidak ada aktivitas.');
            }

            session(['last_activity_time' => time()]);
        }

        return $next($request);
    }
}
