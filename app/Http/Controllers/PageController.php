<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\EmployeeDocument;
use App\Models\SalaryRequest;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;




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


        //  Pending salary requests
        $salReqs     = SalaryRequest::with('employee')
            ->where('status', 'pending')
            ->orderByDesc('requested_at')
            ->get();
        $pendingSal  = $salReqs->count();

        //  Monthly Revenue vs Expenses for chart
        $year        = now()->year;
        $monthlyData = [];

        for ($m = 1; $m <= 12; $m++) {
            $rev = DB::table('projects')
                ->whereYear('start_date', $year)
                ->whereMonth('start_date', $m)
                ->sum('received');

            $exp = DB::table('expenses')
                ->whereYear('date', $year)
                ->whereMonth('date', $m)
                ->sum('amount');

            $monthlyData[] = [
                'rev' => (float) $rev,
                'exp' => (float) $exp,
            ];
        }

        $pendingSalary = SalaryRequest::where('status', 'pending')->count();


//        return view with variables
        return view('admin/dashboard', compact('totalUser', 'activeProjectCount', 'monthRev', 'monthEXP', 'monthlyData', 'recentProjects', 'salReqs',
            'pendingSal',
            'monthlyData',
            'year', 'pendingSalary'));
    }

    public function Employees(Request $request){

//        search
        $search = trim($request->query('search', ''));
        $dept   = $request->query('dept', '');
        $status = $request->query('status', '');
        $employeeSearch = Employee::query()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                });
            })
            ->when($dept, function ($q) use ($dept) {
                $q->where('department', $dept);
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->get();


        $employees = Employee::all();
        $editEmp = null;
        if ($request->has('edit')) {
            $editEmp = Employee::findOrFail($request->query('edit'));
        }

        $depts = ['Web Dev', 'App Dev', 'Game Dev', 'Marketing', 'Design', 'Management'];
        $statuses = ['active', 'on_leave', 'inactive'];

        return view('admin/employees', compact('employees', 'depts', 'editEmp', 'statuses', 'employeeSearch', 'search', 'dept', 'status'));
    }


    public function EmployeeStore(Request $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
//            'role'     => 'employee',
        ]);
        $validated = $request->validate([
            'name'                  => 'required|string|max:100',
            'email'                 => 'required|email|max:100|unique:employees,email',
            'phone'                 => 'nullable|string|max:20',
            'department'            => 'required|in:Web Dev,App Dev,Game Dev,Marketing,Design,Other',
            'designation'           => 'nullable|string|max:100',
            'salary'                => 'nullable|numeric|min:0',
            'join_date'             => 'nullable|date',
            'nid'                   => 'nullable|string|max:50',
            'address'               => 'nullable|string',
            'status'                => 'nullable|in:active,on_leave,inactive',
            'payment_method'        => 'nullable|in:bank,bkash,nagad,rocket,cash',
            'bank_name'             => 'nullable|string|max:100',
            'bank_account'          => 'nullable|string|max:100',
            'mobile_banking_number' => 'nullable|string|max:20',
            'photo'                 => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $validated['user_id'] = $user->id;


        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('employees/photos', 'public');
        }
         Employee::create($validated);
        return redirect()->back()->with('success', 'Employee created successfully!');
    }

    public function EmployeeUpdate(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'user_id'               => 'nullable|integer|exists:users,id',
            'name'                  => 'nullable|string|max:100',
            'email'                 => 'nullable|email|max:100|unique:employees,email,' . $id,
            'phone'                 => 'nullable|string|max:20',
            'department'            => 'nullable|in:Web Dev,App Dev,Game Dev,Marketing,Design,Management',
            'designation'           => 'nullable|string|max:100',
            'salary'                => 'nullable|numeric|min:0',
            'join_date'             => 'nullable|date',
            'nid'                   => 'nullable|string|max:50',
            'address'               => 'nullable|string',
            'status'                => 'nullable|in:active,on_leave,inactive',
            'payment_method'        => 'nullable|in:bank,bkash,nagad,rocket,cash',
            'bank_name'             => 'nullable|string|max:100',
            'bank_account'          => 'nullable|string|max:100',
            'mobile_banking_number' => 'nullable|string|max:20',
            'photo'                 => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($employee->photo) {
                Storage::disk('public')->delete($employee->photo);
            }
            $validated['photo'] = $request->file('photo')->store('employees/photos', 'public');
        }

        $employee->update($validated);

        return redirect()->back()
            ->with('success', 'Employee updated successfully!')
            ->with('modal_closed', true);
    }


//    project delete
    public function EmployeeDestroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->user()->delete();
        // ✅ Photo delete from storage
        if ($employee->photo) {
            Storage::disk('public')->delete($employee->photo);
        }

        $employee->delete();

        return redirect()->back()->with('success', 'Employee deleted successfully!');
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




//employee documents code
    public function index($employee_id)
    {
        $documents = EmployeeDocument::where('employee_id', $employee_id)->get();

        return view('admin.employee_documents.index', compact('documents', 'employee_id'));
    }

    // ✅ Show — single document
    public function show($id)
    {
        $document = EmployeeDocument::findOrFail($id);

        return view('admin.employee_documents.show', compact('document'));
    }

    // ✅ Store — save new document
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|integer|exists:employees,id',
            'doc_name'    => 'required|string|max:255',
            'doc_type'    => 'required|in:nid,passport,certificate,contract,other',
            'file_path'   => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        // ✅ File upload
        if ($request->hasFile('file_path')) {
            $validated['file_path'] = $request->file('file_path')
                ->store('employee_documents', 'public');
        }

        $validated['uploaded_at'] = now();

        EmployeeDocument::create($validated);

        return redirect()->back()->with('success', 'Document uploaded successfully!');
    }

    // ✅ Edit — show edit form
    public function edit($id)
    {
        $document = EmployeeDocument::findOrFail($id);

        return view('admin.employee_documents.edit', compact('document'));
    }

    // ✅ Update — update document
    public function DocumentsUpdate(Request $request, $id)
    {
        $document = EmployeeDocument::findOrFail($id);

        $validated = $request->validate([
            'doc_name'  => 'required|string|max:255',
            'doc_type'  => 'required|in:nid,passport,certificate,contract,other',
            'file_path' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        // ✅ New file upload হলে পুরনো file delete করবে
        if ($request->hasFile('file_path')) {
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path); // ✅ old file delete
            }
            $validated['file_path'] = $request->file('file_path')
                ->store('employee_documents', 'public');
        }

        $document->update($validated);

        return redirect()->back()->with('success', 'Document updated successfully!');
    }

    // ✅ Destroy — delete document
    public function destroy($id)
    {
        $document = EmployeeDocument::findOrFail($id);

        // ✅ File storage থেকে delete
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->back()->with('success', 'Document deleted successfully!');
    }


//    user panel functions


    public function userDashboard()
    {
        $emp   = Auth::user()->employee; //  logged in employee
        $empId = $emp->id;

        //  Task counts
        $totalTasks   = Task::where('assigned_to', $empId)->count();
        $doneTasks    = Task::where('assigned_to', $empId)->where('status', 'done')->count();
        $pendingTasks = Task::where('assigned_to', $empId)->where('status', 'pending')->count();

        //  Attendance this month
        $monthAtt = Attendance::where('employee_id', $empId)
            ->where('status', 'present')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();

        //  Today's attendance
        $todayAtt = Attendance::where('employee_id', $empId)
            ->whereDate('date', now()->toDateString())
            ->first();

        //  Recent tasks
        $recentTasks = Task::with('project')
            ->where('assigned_to', $empId)
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        //  Recent salary requests
        $salaryRequests = SalaryRequest::where('employee_id', $empId)
            ->orderByDesc('requested_at')
            ->limit(3)
            ->get();

        return view('employee.dashboard', compact(
            'emp',
            'totalTasks',
            'doneTasks',
            'pendingTasks',
            'monthAtt',
            'todayAtt',
            'recentTasks',
            'salaryRequests'
        ));
    }




}
