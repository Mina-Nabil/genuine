<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RouteDriverToShiftPage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User */
        $loggedInUser = Auth::user();
        if ($loggedInUser && $loggedInUser->is_driver && $request->path() !== '/orders/driver')
            return redirect('/orders/driver');
        return $next($request);
    }
}
