<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfitLossController extends Controller
{
    public function index()
    {
        //  Total Revenue (all projects received amount)
        $totalRev = Project::sum('received');

        //  Total Expenses (all expenses)
        $totalExp = Expense::sum('amount');

        //  Per-project profit summary
        $projects = Project::select('projects.*')
            ->selectSub(function ($q) {
                $q->from('expenses')
                    ->whereColumn('expenses.project_id', 'projects.id')
                    ->selectRaw('COALESCE(SUM(amount), 0)');
            }, 'total_exp')
            ->get();

        //  Monthly P&L for current year
        $year      = now()->year;
        $monthlyPL = $this->getMonthlyPL($year);

        //  Profit Margin
        $netProfit     = $totalRev - $totalExp;
        $profitMargin  = $totalRev > 0 ? round($netProfit / $totalRev * 100) : 0;

        return view('admin.profit', compact(
            'totalRev',
            'totalExp',
            'netProfit',
            'profitMargin',
            'projects',
            'monthlyPL',
            'year'
        ));
    }

    //  Monthly P&L calculation
    private function getMonthlyPL($year)
    {
        $monthlyPL = [];

        for ($m = 1; $m <= 12; $m++) {
            // Revenue — projects received amount by month
            $rev = DB::table('projects')
                ->whereYear('start_date', $year)
                ->whereMonth('start_date', $m)
                ->sum('received');

            // Expenses — by month
            $exp = DB::table('expenses')
                ->whereYear('date', $year)
                ->whereMonth('date', $m)
                ->sum('amount');

            $monthlyPL[] = [
                'month'  => $m,
                'rev'    => (float) $rev,
                'exp'    => (float) $exp,
                'profit' => (float) ($rev - $exp),
            ];
        }

        return $monthlyPL;
    }
}
