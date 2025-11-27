<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Supervisor
{
    public function handle(Request $request, Closure $next)
    {
        $supervisorGuard = auth('supervisor'); 

        if (!$supervisorGuard->check()) {
            return redirect()->route('index')->with('error', 'Access denied.');
        }

        $user = $supervisorGuard->user();

        // dd($user->toArray());

        if ($user->role !== 'supervisor') {
            return redirect()->route('index')->with('error', 'Access denied.');
        }

        return $next($request);
    }
}
