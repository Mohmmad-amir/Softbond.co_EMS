<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Employee;
use App\Models\Project;
use App\Models\Expense;
use Carbon\Carbon;
use App\Models\ProjectPayment;
use App\Models\Task;
use App\Models\ProjectExpense;



class PageController extends Controller
{
    //

    public function AdminDashboard(){
//        all user count
        $totalUser = User::where('role', '!=', 'admin')->count();
//        total project have active count
        $activeProjectCount = Project::where('status', 'active')->count();
//        total revenue received in a month
        $monthRev = Project::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('received');
//      total expense in a month
        $monthEXP = Expense::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');
//        yearly graph chart
        $monthlyData = [];
        $currentYear = Carbon::now()->year;
        for ($m = 1; $m <= 12; $m++) {
//            earn in a month
            $rev = Project::whereYear('created_at', $currentYear)->whereMonth('created_at', $m)->sum('received');
//            expense in a month
            $exp = Expense::whereYear('created_at', $currentYear)->whereMonth('created_at', $m)->sum('amount');
            $monthlyData[] = [
              'rev' => (float)$rev,
              'exp' => (float)$exp,
            ];
        }
        // recent 5 project
        $recentProjects = Project::latest()->take(5)->get();
//        return view with variables
        return view('admin/dashboard', compact('totalUser', 'activeProjectCount', 'monthRev', 'monthEXP', 'monthlyData', 'recentProjects'));
    }

    public function Employees(){
        $employees = Employee::all();
        return view('admin/employees', compact('employees'));
    }

    public function Attendance(){
        return view('admin/attendance');
    }
    public function Salary(){
        return view('admin/salary');
    }


//    project Index Function
    public function Project(){
        $totalPaid = Project::sum('received');
        $totalExp  = ProjectExpense::sum('amount');
        $profit = $totalPaid - $totalExp;
        $projectsAll = Project::all();

        $statusClasses = [
            'new' => 'secondary',
            'active' => 'info',
            'on_hold' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger'
        ];
        return view('admin/projects', compact('projectsAll', 'statusClasses', 'profit', 'totalExp'));
    }
//      project Edit Function
    public function ProjectEdit($id){
        $projectToEdit = $id ? Project::findOrFail($id) : null;
        $statusClasses = [
            'new' => 'secondary',
            'active' => 'info',
            'on_hold' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger'
        ];
        return view('admin/projects', compact('projectToEdit', 'statusClasses'));
    }
//      Project Store Funtion
    public function ProjectStore(Request $request)
    {
        $validatedData = $request->validate([
            'name'        => 'required|string|max:255',
            'client'      => 'nullable|string|max:255',
            'type'        => 'required|string',
            'budget'      => 'nullable|numeric',
            'received'    => 'nullable|numeric',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date',
            'status' => 'nullable|in:active,inactive,pending_approval', // ✅ match DB ENUM exactly
            'description' => 'nullable|string',
            'progress'    => 'nullable|integer|min:0|max:100',
        ]);

        // save database
        Project::create($validatedData);
        // return with success message
        return redirect()->back()->with('success', 'Project created successfully!');
    }

//    project update Function
    public function ProjectUpdate(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'client'      => 'nullable|string|max:255',
            'type'        => 'required|string|max:100',
            'budget'      => 'required|numeric|min:0',
            'received'    => 'nullable|numeric|min:0',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'status'      => 'nullable|in:active,inactive,pending_approval', // ✅ match DB ENUM exactly
            'description' => 'nullable|string',
            'progress'    => 'nullable|integer|min:0|max:100',
        ]);

        $project->update($validated);

        return redirect()->back()->with('success', 'Project updated successfully.')->with('modal_closed', true);
    }
//project Details Function
    public function projectDetail($id)
    {
        //Find the project by ID, if not found it will show error 404
        $project = Project::findOrFail($id);

        $totalPaid = Project::sum('received');
        $totalExp  = ProjectExpense::sum('amount');

        $profit = $totalPaid - $totalExp;
        $payments = ProjectPayment::where('project_id', $id)
            ->Orderby('payment_date', 'desc')->get();

        $tasks = Task::where('project_id', $id)
            ->with('employee')
            ->orderBy('due_date', 'asc')
            ->get();

        $expenses = ProjectExpense::where('project_id', $id)
            ->orderBy('expense_date', 'desc')
            ->get();

        $totalPaid = \App\Models\ProjectPayment::where('project_id', $id)->sum('amount');
        $totalExpenses = \App\Models\ProjectExpense::where('project_id', $id)->sum('amount');
        $netProfit = $totalPaid - $totalExpenses;
        $remainingBudget = $project->budget - $totalPaid;

// Will return a new blade file (eg: project_show.blade.php) for viewing details
        return view('admin/project_detail', compact(
            'project',
            'totalPaid',
            'totalExp',
            'profit',
            'payments',
            'tasks',
            'expenses',
            'totalExpenses',
            'netProfit',
            'remainingBudget'));
    }

    public function ProjectDestroy($id)
    {
        $projects = Project::findOrFail($id);
        $projects->delete();
        return redirect()->back()->with('success', 'Expense deleted successfully!');

    }


    public function ProjectExpenseDestroy($id){
        $expense = ProjectExpense::findOrFail($id);

        $expense->delete();
        return redirect()->back()->with('success', 'Expense deleted successfully!');

    }

    public function ProjectPaymentStore(Request $request)
    {
        $validated = $request->validate([
            'project_id'   => 'required|exists:projects,id',
            'amount'       => 'required|numeric|min:1',
            'note'         => 'nullable|string|max:255',
            'payment_date' => 'required|date',
        ]);

        ProjectPayment::create([
            'project_id'   => $validated['project_id'],
            'amount'       => $validated['amount'],
            'note'         => $validated['note'],
            'payment_date' => $validated['payment_date'],
        ]);

        return redirect()->back()->with('success', 'Payment recorded successfully!');
    }


    public function ProjectExpenseStore(Request $request)
    {
        $request->validate([
            'project_id'   => 'required|exists:projects,id',
            'description'  => 'required|string|max:255',
            'category'     => 'required|string',
            'amount'       => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'note'         => 'nullable|string',
        ]);

        ProjectExpense::create([
            'project_id'   => $request->project_id,
            'description'  => $request->description,
            'category'     => $request->category,
            'amount'       => $request->amount,
            'expense_date' => $request->expense_date,
            'note'         => $request->note,
        ]);

        return redirect()->back()->with('success', 'Expense recorded successfully!');
    }



    public function ProjectPaymentDestroy($id){
        $DelPeyment = ProjectPayment::findOrFail($id);

        $DelPeyment->delete();
        return redirect()->back()->with('success', 'Expense deleted successfully!');

    }

    public function logout(Request $request)
    {
        // user logout
        Auth::logout();

        // session invalid
        $request->session()->invalidate();

        // regenerate token
        $request->session()->regenerateToken();

        // redirect to login page with message
        return redirect('/login')->with('success', 'You have been logged out.');
    }

}
