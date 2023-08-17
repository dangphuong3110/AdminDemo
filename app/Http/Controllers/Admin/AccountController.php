<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailResetPassword;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accounts = User::all();

        return view('admin.setting.account-setting', compact('accounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.setting.account-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|not_regex:/\s/|unique:users',
            'first-name' => 'required|min:2|max:32',
            'last-name' => 'required|min:2|max:32',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'confirm-password' => 'required|string|min:6|same:password',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $account = new User();

        $account->username = $request->input('username');
        $account->name = $request->input('last-name') . " " . $request->input('first-name');
        $account->email = $request->input('email');
        if ($request->input('phone-number')) {
            $account->phone_number = $request->input('phone-number');
        }

        $account->role = "admin";
        $account->password = Hash::make($request->input('password'));

        $account->save();

        return redirect()->route('account.index')->with('success', 'Account has been added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $account = User::findOrFail($id);

        return view('admin.setting.account-edit', compact('account'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $account = User::findOrFail($id);
        $username = $request->input('username');
        $firstName = $request->input('first-name');
        $lastName = $request->input('last-name');
        $email = $request->input('email');
        $password = $request->input('password');
        $phoneNumber = $request->input('phone-number');
        $validator = Validator::make($request->all(), [
            'username' => $username != $account->username ? 'required|not_regex:/\s/|unique:users' : 'required|not_regex:/\s/',
            'first-name' => 'required|min:2|max:32',
            'last-name' => 'required|min:2|max:32',
            'email' => $email != $account->email ? 'required|email|unique:users' : 'required|email',
            'password' => $password != "######" && $password != '' ? 'required|string|min:6' : '',
            'confirm-password' => $password != "######" && $password != '' ? 'required|string|min:6|same:password' : '',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $account->username = $username;
        $account->name = $lastName . " " . $firstName;
        $account->email = $email;
        if ($phoneNumber) {
            $account->phone_number = $phoneNumber;
        }

        $account->role = "admin";
        $account->password = Hash::make($password);

        $account->save();

        return redirect()->route('account.index')->with('success', 'Account has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $account = User::findOrFail($id);
        if (Auth::user() == $account) {
            return redirect()->back()->with('error', 'You can not delete your own account!');
        }

        $account->delete();

        return redirect()->route('account.index')->with('success', 'Account deleted successfully.');
    }

    public function showEmailForm () {
        return view('account.input-email');
    }

    public function validatePasswordRequest (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors(['email' => 'Please provide a valid email.']);
        }

        $email = $request->input('email');

        $user = DB::table('users')->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->withErrors(['email' => 'User does not exist.']);
        }

        $tokenExists = DB::table('password_reset_tokens')->where('email', $email)->first();
        if ($tokenExists) {
            DB::table('password_reset_tokens')->where('email', $user->email)->delete();
        }

        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => Str::random(60),
            'created_at' => Carbon::now(),
        ]);

        $tokenData = DB::table('password_reset_tokens')->where('email', $email)->first();

        if ($this->sendResetEmail($email, $tokenData->token)) {
            return redirect()->route('login')->with('success', 'A reset link has been sent to your email address.');
        } else {
            return redirect()->route('login')->withErrors(['error' => 'A Network Error occurred. Please try again.']);
        }
    }

    private function sendResetEmail ($email, $token)
    {
        $user = DB::table('users')->where('email', $email)->select('name', 'email')->first();

        $link = env('APP_URL') . '/admin/reset_password?token=' . $token . '&email=' . urlencode($user->email);

        try {
            SendEmailResetPassword::dispatch($user, $link);
            return true;
        } catch (\Exception $e) {
            dd($e);
            return false;
        }
    }

    public function showResetPasswordForm (Request $request)
    {
        $token = $request->query('token');
        $email = urldecode($request->query('email'));

        $tokenExists = DB::table('password_reset_tokens')->where('token', $token)->where('email', $email)->exists();

        if ($tokenExists) {
            return view('account.reset-password')->with(['token' => $token]);
        }
        else {
            return redirect()->route('login')->withErrors(['error' => 'Invalid token.']);
        }
    }

    public function resetPassword (Request $request, $token = null)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6',
            'confirm-password' => 'required|string|min:6|same:password',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $password = $request->input('password');
        $tokenData = DB::table('password_reset_tokens')->where('token', $token)->first();
        if (!$tokenData) {
            return view('account.input-email');
        }

        $user = User::where('email', $tokenData->email)->first();

        if (!$user) {
            return redirect()->route('show-email-form')->withErrors(['email' => 'Email not found.']);
        }

        $user->password = Hash::make($password);
        $user->update();

        Auth::login($user);

        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        return redirect()->route('homepage.index')->with('success', 'Password has been updated successfully.');
    }
}
