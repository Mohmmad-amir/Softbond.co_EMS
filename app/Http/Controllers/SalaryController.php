<?php

namespace App\Http\Controllers;

use App\Models\SalaryRequest;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    //  Index
    public function index()
    {

        $pending = SalaryRequest::with('employee')
            ->where('status', 'pending')
            ->orderByDesc('requested_at')
            ->get();

        $history = SalaryRequest::with('employee')
            ->where('status', '!=', 'pending')
            ->orderByDesc('actioned_at')
            ->limit(30)
            ->get();

        $pendingCount = $pending->count();

        return view('admin.salary', compact(
            'pending',
            'history',
            'pendingCount',
        ));
    }

    //  Approve
    public function approve($id)
    {
        SalaryRequest::findOrFail($id)->update([
            'status'      => 'approved',
            'actioned_at' => now(),
        ]);

        return redirect()->route('admin.salary.index')
            ->with('success', 'Request approved successfully!');
    }

    //  Deny
    public function deny($id)
    {
        SalaryRequest::findOrFail($id)->update([
            'status'      => 'denied',
            'actioned_at' => now(),
        ]);

        return redirect()->route('admin.salary.index')
            ->with('success', 'Request denied.');
    }
}
