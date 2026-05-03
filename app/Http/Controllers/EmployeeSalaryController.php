<?php

namespace App\Http\Controllers;

use App\Models\SalaryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeSalaryController extends Controller
{
    // ✅ Index — show salary page
    public function index()
    {
        $emp   = Auth::user()->employee;
        $empId = $emp->id;

        // ✅ All requests
        $requests = SalaryRequest::where('employee_id', $empId)
            ->orderByDesc('requested_at')
            ->get();

        // ✅ Total approved amount
        $approved = SalaryRequest::where('employee_id', $empId)
            ->where('status', 'approved')
            ->sum('amount');

        // ✅ Pending count
        $pending = SalaryRequest::where('employee_id', $empId)
            ->where('status', 'pending')
            ->count();

        return view('employee.salary', compact(
            'emp',
            'requests',
            'approved',
            'pending'
        ));
    }

    // ✅ Store — submit salary request
    public function store(Request $request)
    {
        $emp   = Auth::user()->employee;
        $empId = $emp->id;

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'month'  => 'required|string',
            'note'   => 'nullable|string',
        ]);

        // ✅ Check if already requested this month
        $existing = SalaryRequest::where('employee_id', $empId)
            ->where('month', $request->month)
            ->where('status', 'pending')
            ->exists();

        if ($existing) {
            return redirect()->back()
                ->with('error', 'You already have a pending request for this month.');
        }

        SalaryRequest::create([
            'employee_id'  => $empId,
            'amount'       => $request->amount,
            'month'        => $request->month,
            'note'         => $request->note,
            'requested_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Salary request submitted! Waiting for admin approval.');
    }
}
