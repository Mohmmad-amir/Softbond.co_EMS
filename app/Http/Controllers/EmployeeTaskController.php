<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeTaskController extends Controller
{
    // ✅ Index — show all tasks
    public function index(Request $request)
    {
        $empId  = Auth::user()->employee->id;
        $filter = $request->query('filter', 'all');

        $query = Task::with('project')
            ->where('assigned_to', $empId);

        // ✅ Filter by status
        if ($filter === 'pending') {
            $query->where('status', 'pending');
        } elseif ($filter === 'active') {
            $query->where('status', 'in_progress');
        } elseif ($filter === 'done') {
            $query->where('status', 'done');
        }

        $tasks = $query->orderByDesc('id')->get();

        // ✅ Metrics
        $total   = Task::where('assigned_to', $empId)->count();
        $inprog  = Task::where('assigned_to', $empId)->where('status', 'in_progress')->count();
        $pending = Task::where('assigned_to', $empId)->where('status', 'pending')->count();
        $done    = Task::where('assigned_to', $empId)->where('status', 'done')->count();

        return view('employee.tasks', compact(
            'tasks',
            'filter',
            'total',
            'inprog',
            'pending',
            'done'
        ));
    }

    // ✅ Update task status
    public function updateStatus(Request $request, $id)
    {
        $empId = Auth::user()->employee->id;

        $task = Task::where('id', $id)
            ->where('assigned_to', $empId) // ✅ only own tasks
            ->firstOrFail();

        $request->validate([
            'status' => 'required|in:pending,in_progress,done',
        ]);

        $task->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Task status updated!');
    }
}
