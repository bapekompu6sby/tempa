<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequirePassword
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Allow access if session flag present
        if ($request->session()->get('access_granted')) {
            return $next($request);
        }

        // If not unlocked, redirect to welcome page where the unlock form lives
        return redirect('/');
    }
}
