<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    //  Index — show profile
    public function index()
    {
        $profile = Auth::user();

        return view('admin.profile', compact('profile'));
    }

    //  Update profile info
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,' . $user->id,
            'company_name' => 'nullable|string|max:255',
            'phone'        => 'nullable|string|max:20',
            'address'      => 'nullable|string',
        ]);

        $user->update([
            'name'         => $request->name,
            'email'        => $request->email,
            'company_name' => $request->company_name,
            'phone'        => $request->phone,
            'address'      => $request->address,
        ]);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    //  Update password
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:8|confirmed', //  confirmed = new_password_confirmation
            'new_password_confirmation' => 'required',
        ]);

        //  Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Current password is incorrect.'])
                ->with('tab', 'password'); //  password tab open রাখবে
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->back()->with('success', 'Password updated successfully!');
    }
}
