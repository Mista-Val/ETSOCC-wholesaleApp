<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Carbon;

class EnsureOtpVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next)
    // {
    //     // Check if session has otp_verified_for flag
    //     if (!$request->session()->has('otp_verified_for') && $request->session()->has('otp_verified_at')) {
    //         // Redirect back or to OTP request page with error
    //        return redirect()->route('warehouse.login')->with('error', 'Access denied.');
    //     }

    //     return $next($request);
    // }

     public function handle(Request $request, Closure $next)
    {
        $verifiedEmail = session('otp_verified_for');
         $verifiedAt = session('otp_verified_at');
        $maxMinutes = 15; 

        if (!$verifiedEmail || !$verifiedAt) {
            return redirect()->route('login')->with('error', 'OTP verification required.');
        }

        if (Carbon::parse($verifiedAt)->addMinutes($maxMinutes)->isPast()) {
            session()->forget(['otp_verified_for', 'otp_verified_at']);
            return redirect()->route('login')->with('error', 'OTP verification expired. Please try again.');
        }

        return $next($request);
    }
}
