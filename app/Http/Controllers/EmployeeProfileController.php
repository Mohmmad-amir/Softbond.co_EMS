<?php

namespace App\Http\Controllers;

use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EmployeeProfileController extends Controller
{
    //  Index — show profile
    public function index()
    {
        $emp  = Auth::user()->employee;
        $docs = EmployeeDocument::where('employee_id', $emp->id)
            ->orderByDesc('uploaded_at')
            ->get();

        return view('employee.profile', compact('emp', 'docs'));
    }

    //  Update phone and address
    public function updateProfile(Request $request)
    {
        $emp = Auth::user()->employee;

        $request->validate([
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $emp->update([
            'phone'   => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    //  Update password
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:8',
        ]);

        //  Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->back()->with('success', 'Password updated successfully!');
    }
}
