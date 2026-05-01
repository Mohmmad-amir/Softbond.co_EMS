<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Project;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    protected $categories = [
        'Salary', 'Rent', 'Utilities', 'Software',
        'Hardware', 'Marketing', 'Travel', 'Food', 'Other'
    ];

    //  Index
    public function index(Request $request)
    {
        $month = $request->query('month', now()->format('Y-m'));
        [$y, $m] = explode('-', $month);

        //  All expenses for selected month
        $expenses = Expense::with('project')
            ->whereYear('date', $y)
            ->whereMonth('date', $m)
            ->orderByDesc('date')
            ->get();

        //  Edit expense
        $editExp = null;
        if ($request->has('edit')) {
            $editExp = Expense::findOrFail($request->query('edit'));
        }

        //  Total this month
        $totalMonth = $expenses->sum('amount');

        //  Expenses by category
        $catData = $expenses->groupBy('category')->map(fn($g) => [
            'category' => $g->first()->category,
            's'        => $g->sum('amount'),
        ])->sortByDesc('s')->values();

        //  Expenses by project
        $projData = $expenses->whereNotNull('project_id')
            ->groupBy('project_id')
            ->map(fn($g) => [
                'name' => $g->first()->project->name ?? 'Unknown',
                's'    => $g->sum('amount'),
            ])->sortByDesc('s')->values();

        $projects   = Project::all();
        $categories = $this->categories;

        return view('admin.expenses', compact(
            'expenses', 'editExp', 'totalMonth',
            'catData', 'projData', 'projects',
            'categories', 'month', 'y', 'm'
        ));
    }

    //  Store
    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'category'    => 'required|string',
            'amount'      => 'required|numeric|min:0',
            'date'        => 'required|date',
            'project_id'  => 'nullable|integer|exists:projects,id',
            'note'        => 'nullable|string',
        ]);

        Expense::create($validated);

        return redirect()->back()->with('success', 'Expense added successfully!');
    }

    //  Update
    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'category'    => 'required|string',
            'amount'      => 'required|numeric|min:0',
            'date'        => 'required|date',
            'project_id'  => 'nullable|integer|exists:projects,id',
            'note'        => 'nullable|string',
        ]);

        $expense->update($validated);

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Expense updated successfully!');
    }

    //  Destroy
    public function destroy($id)
    {
        Expense::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Expense deleted successfully!');
    }
}
