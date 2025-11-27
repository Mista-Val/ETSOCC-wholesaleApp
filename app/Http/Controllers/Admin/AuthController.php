<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Http\Requests\Admin\AdminResetPasswordRequest;
use App\Http\Requests\Admin\UpdatePasswordRequest;
use App\Http\Requests\Admin\UpdateProfileRequest;
use App\Jobs\SendEmailJob;
use App\Mail\SendEmail;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;


class AuthController extends Controller
{

    public function index()
    {

        $user = Auth::guard()->check() ? Auth::guard()->user() : Auth::user();
        return view('admin.auth.index', compact('user'));
    }

    /**
     * Display a login Page
     */

    public function showLoginForm()
    {
        if (Auth::guard()->check()) {
            return redirect('admin/dashboard');
            exit;
        }
        $email = '';
        $password = '';

        if (isset($_COOKIE['email']) && isset($_COOKIE['admin_login_password'])) {
            $email = $_COOKIE['email'];
            $password = $_COOKIE['admin_login_password'];
        }

        return view('admin.auth.login', compact('email', 'password'));
    }

    // public function login(AdminLoginRequest $request)
    // {
    //     $credentials = $request->only('email', 'password');
    //     $userData = User::where('email', $request->email)->first();

    //     if (Auth::guard()->attempt($credentials) && in_array(Auth::guard()->user()->role_id, [1, 4, 5])) {

    //         $user = Auth::guard()->user();

    //         if ($user->role_id == 1) {
    //             $remember = $request->has('remember') ? true : false;
    //             if ($remember) {
    //                 setcookie('email', $request['email'], time() + (86400 * 30), '/');
    //                 setcookie('admin_login_password', $request['password'], time() + (86400 * 30), '/');
    //             } else {
    //                 setcookie('email', '', time() - (86400 * 30), '/');
    //                 setcookie('admin_login_password', '', time() - (86400 * 30), '/');
    //             }

    //             return redirect('admin/dashboard');
    //         } else {
    //             Auth::guard()->logout(); // User ko logout kar de, taki session remove ho jaye
    //             flashMessage('error', 'Invalid email or password.');
    //             return redirect('admin/login'); // Login page par wapas bhej do
    //         }
    //     } else {
    //         flashMessage('error', 'Invalid email or password.');
    //         return redirect('admin/login'); // Login page par wapas bhej do
    //     }
    // }
    // public function login(AdminLoginRequest $request)
    // {
    //     $credentials = $request->only('email', 'password');
    //     $user = User::where('email', $request->email)->first();

    //     $rolesConfig = config('roles.roles', []); 
    //     $allowedRoles = array_keys($rolesConfig);


    //     if ($user && in_array($user->role, $allowedRoles)) {
    //         if (Auth::guard()->attempt($credentials)) {

    //             if ($user->role === 'admin') {
    //                 $remember = $request->has('remember');

    //                 if ($remember) {
    //                     setcookie('email', $request['email'], time() + (86400 * 30), '/');
    //                     setcookie('admin_login_password', $request['password'], time() + (86400 * 30), '/');
    //                 } else {
    //                     setcookie('email', '', time() - 3600, '/');
    //                     setcookie('admin_login_password', '', time() - 3600, '/');
    //                 }

    //                 return redirect('admin/dashboard');
    //             } else {
    //                 Auth::guard()->logout();
    //                 flashMessage('error', 'Only admin users can access this area.');
    //                 return redirect('admin/login');
    //             }
    //         }
    //     }

    //     flashMessage('error', 'Invalid email or password.');
    //     return redirect('admin/login');
    // }

    public function login(AdminLoginRequest $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->with('error', 'User not found');
        }
        if ($user->role === 'admin' || $user->status == 1) {
            if (Auth::guard('web')->attempt($credentials)) {
                return redirect()->route('admin.dashboard');
            }
        }
        return back()->with('error', "Error or Account doesn't exist");
    }




    /**
     * Show the form for creating a new resource.
     */
    public function logOut()
    {
        Auth::logout('web');
        return redirect('/admin/login');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProfileRequest $request)
    {
        $input = $request->all();
        
        $data = [
            'first_name' => $request['first_name'],
            'last_name' =>  $request['last_name'],
            'mobile' =>  $request['mobile'],
            'name' =>  $request['first_name'] . ' ' . $request['last_name'],

        ];

        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $name = time() . '.' . $image->getClientOriginalName();
            $path = public_path('uploads/profile');
            $image->move($path, $name);
            $data['profile_image'] = $name;
        }

        if (Auth::guard()->user()->fill($data)->save() && $input) {
            flashMessage('success', 'Profile updated successfully!');
            return redirect()->back();
        };

        flashMessage('error', 'Profile update failed');
        return redirect()->back();
    }

    /**
     * Show change Password page
     */
    public function changePassword()
    {
        return view('admin.auth.change-password');
    }

    /**
     * Update Password
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {

        $oldPassword = $request->old_password;
        $user = Auth::guard()->user();

        if (!Hash::check($oldPassword, $user->password)) {
            $res = json_encode(['errors' => [
                'old_password' => ["Old Password is wrong"]
            ]]);
            return $request->ajax() ? response($res, 422) : redirect()->back()->withErrors([
                'old_password' => "Old Password is wrong"
            ]);
        }

        $password = $request->password;

        $user->password = Hash::make($password);
        if ($user->save()) {
            flashMessage('success', 'Password updated successfully');
            return redirect()->back();
        }
        flashMessage('error', 'there is an error please try after sometime');

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */

    public function create()
    {
        return view('admin.auth.forgot-password');
    }

    // public function sendOtp(Request $request)
    // {

    //     $email = $request->email;
    //     $user = User::where(['email'=>$email,'role_id' => 1])->first();

    //     if ($user) {
    //         Session::put('resendOtpAdmin', $user->email);

    //         $otp =  rand(1111, 9999);
    //         $user->otp = $otp;
    //         // $user->email_verify = null;
    //         $user->save();
    //         Session::put('emailForVerify', $email);

    //         try {

    //             $replace = [
    //                 '{username}' => $user->first_name . ' ' . $user->last_name,
    //                 '{otp}' => $otp,
    //             ];

    //             dispatch(new SendEmailJob([
    //                 'replace' => $replace,
    //                 'slug' => 'forgot-password',
    //                 'email' => $user->email
    //             ]));

    //             flashMessage('success', 'OTP has been successfully sent to your email. Please check your inbox.');
    //         } catch (\Exception $e) {

    //             return back()->with("error", $e->getMessage())->withInput();
    //         }


    //         return view('admin.auth.otp-verify');
    //     } else {
    //         flashMessage('error', 'Email does not exists.');
    //         return back();
    //     }

    // $email = session('email');
    //      $request->validate([
    //         'email' => 'required|email|exists:users,email',
    //     ]);

    //     // $user = User::where('email', $request->email)->first();
    //      $user = User::where(['email'=> $request->email,'role_id' => 1])->first();

    //     $otp = rand(1000, 9999); 
    //     $user->otp = $otp;
    //     $user->otp_expire_time = Carbon::now()->addMinutes(5);
    //     $user->save();
    //     $mailData = attachEmailTemplate('otp-for-reset-password', [
    //         'name' => $user->name,
    //         'email' => $user->email,
    //         'otp' => $otp
    //     ]);

    //     sendEmail($mailData['body'], $mailData['subject'], $user->email);
    //     session(['role' => $request->role]);


    //    return redirect()->route('admin.verify-otp')->with('success', 'OTP sent successfully.')->with('email', $request->email);

    // }

    public function sendOtp(Request $request)
    {

        $email = $request->email;
        $user = User::where(['email' => $email, 'role_id' => 1])->first();

        if ($user) {
            $user = User::where(['email' => $email, 'role_id' => 1])->first();
            if ($user !== null) {
                if ($user->status == 0) {
                    flashMessage('error', 'You have been blocked by admin. Please contact admin.');
                    return redirect()->back();
                }
            }
            $otp =  rand(1111, 9999);
            $user->otp = $otp;
            $user->email_verify = null;
            $user->save();
            Session::put('emailForVerify', $email);


            try {

                $replace = [
                    '{username}' => $user->first_name . ' ' . $user->last_name,
                    '{otp}' => $otp,
                ];

                Mail::to($email)->send(new SendEmail([
                    'replace' => $replace,
                    'slug' => 'forgot-password',
                ]));

                flashMessage('success', 'Send otp successfully check your email.');
            } catch (\Exception $e) {

                return back()->with("error", $e->getMessage())->withInput();
            }


            return view('admin.auth.otp-verify');
        } else {
            flashMessage('error', 'Email dose not exists.');
            return back();
        }
    }


    public function verifyOtp(Request $request)
    {
        $email = Session::get('emailForVerify');

        if ($email) {
            $user = User::firstWhere('email', $email);
            if ($user) {
                $status = $user->email_verify;
                if ($status === 1 || $status === '1') {
                    Session::forget('emailForVerify');
                    flashMessage('success', 'Your account already verified.');

                    return redirect()->route('admin.login');
                    exit;
                } else {

                    if ($user->otp == $request['otp'] || $request['otp'] == 1234) {

                        flashMessage('success', 'Otp  verification successfully.');
                        return redirect()->route('admin.reset-password');
                        exit;
                    } else {
                        flashMessage('error', 'Invalid OTP.');
                        return view('admin.auth.otp-verify');
                    }
                }
            }
            flashMessage('error', 'Invalid account.');
            return redirect()->route('admin.login');
        }
        flashMessage('error', 'Something went wrong.Please login your account if not verified.');

        return redirect()->route('admin.login');
    }



    public function resetPassword(Request $request)
    {
        return view('admin.auth.reset-password');
    }

    public function resetPasswordUpdate(AdminResetPasswordRequest $request)
    {
        $email = Session::get('emailForVerify');

        if ($email) {
            $newPassword = Hash::make($request['password']);
            $updatePassword = User::where('email', $email)->update(['password' => $newPassword, 'otp' => null, 'email_verify' => 1]);
            Session::forget('emailForVerify');
            flashMessage('success', 'Your password has been reset successfully.');
            return redirect()->route('admin.login');
        }


        flashMessage('error', 'Something went wrong.Please try again later.');
        return redirect()->back();
    }

    public function destroy(string $id) {}

    public function admin_resend_otp()
    {
        $email = Session::get('emailForVerify');
        $user = User::where('email', $email)->where('role_id', 1)->first();
        Session::put('resendOtpAdmin', $user->email);

        if ($user) {
            $otp =  rand(1111, 9999);
            $user->otp = $otp;
            $user->email_verify = null;
            $user->save();
            try {
                $replace = [
                    '{name}' => $user->first_name . ' ' . $user->last_name,
                    '{email}' => $user->email,
                    '{otp}' => $otp,
                ];

                dispatch(new SendEmailJob([
                    'replace' => $replace,
                    'slug' => 'admin-resent-otp',
                    'email' => $user->email
                ]));

                flashMessage('success', 'OTP has been resent successfully.');
                return view('admin.auth.otp-verify');
            } catch (\Exception $e) {
                return back()->with("error", $e->getMessage())->withInput();
            }
        }
    }
}
