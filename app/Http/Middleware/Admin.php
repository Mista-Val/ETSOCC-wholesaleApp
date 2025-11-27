<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    // public function handle(Request $request, Closure $next)
    // {
    //     $adminGuard = auth('web');
    //     if ($adminGuard->check() && $adminGuard->user()->role != 'admin') {
    //         return redirect()->route('admin.login')->with('error', 'Access denied.');
    //     }
    // return $next($request);
    // }
    public function handle(Request $request, Closure $next)
    {
        $adminGuard = auth('web');

        // If user is not logged in, redirect to admin login
        if (!$adminGuard->check()) {
            return redirect()->route('admin.login')->with('error', 'Please login first.');
        }

        // If logged in user is not admin, logout and redirect
        if ($adminGuard->user()->role !== 'admin') {
            $adminGuard->logout();  // logout non-admin users
            return redirect()->route('admin.login')->with('error', 'Access denied.');
        }

        // Allow request to proceed
        return $next($request);
    }

}
