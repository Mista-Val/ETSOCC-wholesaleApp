<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Warehouse
{
    public function handle(Request $request, Closure $next)
    {
        $warehouseGuard = auth('warehouse');

        if (!$warehouseGuard->check()) {
            return redirect()->route('index')->with('error', 'Access denied.');
        }

        $user = $warehouseGuard->user();

        if ($user->role != 'warehouse-manager') {
            return redirect()->route('index')->with('error', 'Access denied.');
        }

        return $next($request);
    }

}


