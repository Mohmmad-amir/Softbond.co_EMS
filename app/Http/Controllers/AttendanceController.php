<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    //  Index — load attendance for a date
    public function index(Request $request)
    {
        $date      = $request->query('date', now()->format('Y-m-d'));
        $employees = Employee::where('status', 'active')->get();

        //  Selected date er attendance data
        $attendances = Attendance::where('date', $date)
            ->get()
            ->keyBy('employee_id')
            ->toArray();

        //  Metrics
        $total    = $employees->count();
        $present  = collect($attendances)->where('status', 'present')->count();
        $onLeave  = collect($attendances)->where('status', 'on_leave')->count();
        $absent   = $total - $present - $onLeave;

        return view('admin.attendance', compact(
            'employees',
            'attendances',
            'date',
            'total',
            'present',
            'onLeave',
            'absent'
        ));
    }

    //  Store — save attendance for all employees
    public function store(Request $request)
    {
        $request->validate([
            'att_date'  => 'required|date',
            'att'       => 'required|array',
        ]);

        $date = $request->att_date;

        foreach ($request->att as $employeeId => $data) {
            Attendance::updateOrCreate(
                [
                    'employee_id' => $employeeId,   //  find by employee_id
                    'date'    => $date,          //  and date
                ],
                [
                    'status'    => $data['status']    ?? 'absent',
                    'check_in'  => $data['check_in']  ?? null,
                    'check_out' => $data['check_out'] ?? null,
                ]
            );
        }

        return redirect()->back()->with('success', 'Attendance saved successfully!');
    }
}
