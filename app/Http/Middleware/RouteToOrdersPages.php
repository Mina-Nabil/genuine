<?php

namespace App\Http\Middleware;

use App\Models\Users\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RouteToOrdersPages
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
        if (
            $loggedInUser 
            && !$loggedInUser->is_admin 
            && str_starts_with($request->path(), 'accounts')
        )
            return redirect('/');
        return $next($request);
    }
}
