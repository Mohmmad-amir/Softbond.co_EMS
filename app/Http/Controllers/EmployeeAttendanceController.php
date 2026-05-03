<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmployeeAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $empId = Auth::user()->employee->id;
        $month = $request->query('month', now()->format('Y-m'));
        [$y, $m] = explode('-', $month);

        $daysInMonth = Carbon::createFromDate($y, $m, 1)->daysInMonth;

        $attRecords = Attendance::where('employee_id', $empId)
            ->whereYear('date', $y)
            ->whereMonth('date', $m)
            ->get();

        // ✅ Fixed: att_date instead of date
        $attMap = $attRecords->keyBy(fn($a) => $a->date->format('Y-m-d'));

        $present = $attRecords->where('status', 'present')->count();
        $absent  = $attRecords->where('status', 'absent')->count();
        $onLeave = $attRecords->where('status', 'on_leave')->count();
        $halfDay = $attRecords->where('status', 'half_day')->count();

        $monthLabel = Carbon::createFromDate($y, $m, 1)->format('F Y');
        $firstDay   = Carbon::createFromDate($y, $m, 1)->dayOfWeek;

        return view('employee.attendance', compact(
            'month', 'monthLabel', 'y', 'm',
            'daysInMonth', 'firstDay',
            'attMap', 'present', 'absent', 'onLeave', 'halfDay'
        ));
    }
}
