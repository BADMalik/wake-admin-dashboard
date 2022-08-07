<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    
    public function otpEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
        Mail::to($request->email)->send(new PasswordResetMail($request->email));
        return redirect()->route('password.otp.view', ['email' => $request->email]);
    }

    public function otpView(Request $request)
    {
        return view('auth.passwords.otp')->withEmail($request->email);
    }

    public function otp(Request $request)
    {
        $request->validate([
            'otp'   => 'required|numeric|digits:6|exists:password_resets,token',
            'email' => 'required|email'
        ],[],[
            'otp' => 'OTP'
        ]);
        
        $token = PasswordReset::firstWhere([['email' , $request->email], ['token' , $request->otp], ['created_at' , '>=', Carbon::now()->subMinutes(5)->toDateTimeString() ]]);
        
        if (!$token) {
            return back()->withErrors(['otp' => 'OTP is either expired or invalid']);
        }

        session(['otp_passed_email' => $request->email]);
        return redirect()->route('password.change.view');
    }

    public function changeView()
    {
        if(session()->has('otp_passed_email')){
            return view('auth.passwords.reset');
        }
        return redirect()->route('password.request')->withWarning('Invalid Request');
    }

    public function change(Request $request)
    {
        if(session()->missing('otp_passed_email')){
            return redirect()->route('password.request')->withWarning('Invalid Request');
        }
        $request->validate([
            'password'   => 'required|min:8|confirmed',
        ]);

        User::firstWhere('email', session('otp_passed_email'))->update(['password' => Hash::make($request->get('password'))]);
        session()->forget('otp_passed_email');

        return redirect()->route('login')->withPasswordStatus('Password Updated Successfully');
    }
}
