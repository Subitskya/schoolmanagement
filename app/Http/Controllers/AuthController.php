<?php

namespace App\Http\Controllers;

use App\Mail\ForgotPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
class AuthController extends Controller
{
    public function login(){
        // dd(Hash::make(123456));
        if(!empty(Auth::check())){
            if(Auth::user() -> user_type == 1){
                return redirect('admin/dashboard');
            }
            elseif(Auth::user() -> user_type == 3){
                return redirect('student/dashboard');
            }
            elseif(Auth::user() -> user_type == 2){
                return redirect('teacher/dashboard');
            }
            elseif(Auth::user() -> user_type == 5){
                return redirect('parent/dashboard');
            }
        }
        return view('auth.login');
    }

    public function AuthLogin(Request $request){
        // dd($request->all());
        $remember = !empty($request->remember) ? true : false;
        if(Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ], $remember)){
            if(Auth::user() -> user_type == 1){
                return redirect('admin/admin/list');
            }
            elseif(Auth::user() -> user_type == 3){
                return redirect('student/dashboard');
            }
            elseif(Auth::user() -> user_type == 2){
                return redirect('teacher/dashboard');
            }
            elseif(Auth::user() -> user_type == 5){
                return redirect('parent/dashboard');
            }
            // return redirect('admin/dashboard');
        }
        else {
            return redirect()->back()->with('error', 'Please enter correct email and password.');
        }
    }

    public function logout(){
        Auth::logout();
        return redirect(url('/'));
    }

    public function forgotPassword(){
        return view('auth.forgot');
    }

    public function PostForgotPassword(Request $request) {
        // Access the email from the request
        $email = $request->input('email'); // or $request->get('email')
        
        // Retrieve the user with the given email
        $user = User::getEmailSingle($email);
        
        if (!empty($user)) {
            $user->remember_token = Str::random(30);
            $user->save();
    
            Mail::to($user->email)->send(new ForgotPasswordMail($user));
    
            return redirect()->back()->with('success', 'Please check your email and reset your password');
        } else {
            return redirect()->back()->with('error', 'Email not found');
        }
    }

    public function reset($remember_token){
        $user = User::getTokenSingle($remember_token);

        if (!empty($user)) {
            $data['user'] = $user;
            return view('auth.reset', $data);
        } else {
            abort(404);
        }
    }

    public function PostReset($remember_token, Request $request){
        if($request->password == $request->cpassword){
            $user = User::getTokenSingle($remember_token);
            $user->password = Hash::make($request->password);
            $user->remember_token = Str::random(30);
            $user->save();

            return redirect(url('/'))->with('success', 'Password successfully reset');
        }
        else{
            return redirect()->back()->with('error', 'Password and Confirm Password does not match');
        }
    }
}
