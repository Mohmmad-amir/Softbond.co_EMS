<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\Employee;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // ✅ Index — list all tasks with filter
    public function index(Request $request)
    {
        $filterProj = $request->query('project', '');
        $filterEmp  = $request->query('employee', '');

        $tasks = Task::with(['project', 'employee'])
            ->when($filterProj, fn($q) => $q->where('project_id', $filterProj))
            ->when($filterEmp,  fn($q) => $q->where('assigned_to', $filterEmp))
            ->get();

        // ✅ Edit task
        $editTask = null;
        if ($request->has('edit')) {
            $editTask = Task::findOrFail($request->query('edit'));
        }

        $pArr = Project::pluck('name', 'id');
        $eArr = Employee::pluck('name', 'id');

        return view('admin.tasks', compact('tasks', 'editTask', 'pArr', 'eArr', 'filterProj', 'filterEmp'));
    }

    // ✅ Store — save new task
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'project_id'  => 'required|integer|exists:projects,id',
            'assigned_to' => 'required|integer|exists:employees,id',
            'due_date'    => 'nullable|date',
            'priority'    => 'required|in:low,medium,high',
            'status'      => 'required|in:pending,in_progress,done',
            'description' => 'nullable|string',
        ]);

        Task::create($validated);

        return redirect()->back()->with('success', 'Task assigned successfully!');
    }

    // ✅ Update — update existing task
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'project_id'  => 'required|integer|exists:projects,id',
            'assigned_to' => 'required|integer|exists:employees,id',
            'due_date'    => 'nullable|date',
            'priority'    => 'required|in:low,medium,high',
            'status'      => 'required|in:pending,in_progress,done',
            'description' => 'nullable|string',
        ]);

        $task->update($validated);

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task updated successfully!');
    }

    // ✅ Status update — quick status change from dropdown
    public function statusUpdate(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,in_progress,done',
        ]);

        $task->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status updated!');
    }

    // ✅ Destroy — delete task
    public function destroy($id)
    {
        Task::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Task deleted successfully!');
    }
}
