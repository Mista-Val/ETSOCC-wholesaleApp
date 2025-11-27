<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Outlet
{
    public function handle(Request $request, Closure $next)
    {
        $outletGuard = auth('outlet'); 

        if (!$outletGuard->check()) {
            return redirect()->route('index')->with('error', 'Access denied.');
        }

        $user = $outletGuard->user();

        // dd($user->toArray());

        if ($user->role !== 'outlet-manager') {
            return redirect()->route('index')->with('error', 'Access denied.');
        }

        return $next($request);
    }
}
