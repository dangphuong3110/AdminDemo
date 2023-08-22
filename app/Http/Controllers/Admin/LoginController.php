<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use App\Models\Session;

class LoginController extends Controller
{
    public function showLoginForm ()
    {
        if (Auth::check()) {
            return redirect()->route('homepage.index');
        }
        return view('login.login');
    }

    public function loginAuthenticate (Request $request)
    {
        $inputs = $request->all();
        $isEmail = filter_var($inputs['email'], FILTER_VALIDATE_EMAIL);


        if ($isEmail) {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|string|max:255',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return redirect()->route('login')->withErrors($validator)->withInput();
            }
            $credentials['email'] = $request->input('email');
        }
        else {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|max:255',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return redirect()->route('login')->withErrors($validator)->withInput();
            }
            $credentials['username'] = $request->input('email');
        }
        $credentials['password'] = $request->input('password');

        if(Auth::attempt($credentials)) {
            $request->session()->regenerate();

            Session::create([
                'id' => Str::random(40),
                'user_id' => Auth::user()->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'payload' => 'value',
                'last_activity' => time(),
            ]);

            if (session()->has('intended_url')) {
                $intendedUrl = session('intended_url');
                session()->forget('intended_url');
                return redirect()->to($intendedUrl);
            }

            return redirect()->intended('/admin/homepage');
        }

        throw ValidationException::withMessages([
            'email' => 'Incorrect email or password.',
            'username' => 'Incorrect username or password.',
        ])->redirectTo(route('login'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
}
