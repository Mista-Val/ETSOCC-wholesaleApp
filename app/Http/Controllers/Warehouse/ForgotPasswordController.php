<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Illuminate\Support\Carbon;

class ForgotPasswordController extends Controller
{
    public function forgot_password()
    {
        return view('web.auth.forgotpassword');
    }
    
    public function sendOtp(Request $request) {
         $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        
        $user = User::where('email', $request->email)->first();
       
        $otp = rand(1000, 9999); 
        $user->otp = $otp;
        $user->otp_expire_time = Carbon::now()->addMinutes(5);
        $user->save();
        $mailData = attachEmailTemplate('otp-for-reset-password', [
            'name' => $user->name,
            'email' => $user->email,
            'otp' => $otp
        ]);
        sendEmail($mailData['body'], $mailData['subject'], $user->email);
        session(['role' => $request->role]);
        return redirect()->route('otp.verify')->with('success', 'OTP sent successfully.')->with('email', $request->email);
        }   
}
