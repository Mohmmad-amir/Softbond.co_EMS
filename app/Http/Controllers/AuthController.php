<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //
//    for show login page
    public function showLogin() {
        return view('admin/login');
    }
//    for login proccess
public function login(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->role == 'admin'){
                return redirect()->route('admin.dashboard');
            }
            if ($user->role == 'user') {
                return redirect()->route('/');
            }
            return redirect()->route('admin.login');
        }
        return back()->with('error', 'Incorrect email or password!');
}
// for logout
public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
}
}
