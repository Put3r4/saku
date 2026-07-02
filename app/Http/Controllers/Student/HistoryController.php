<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\BudgetHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HistoryController extends Controller
{
    /**
     * Display the budget history with trend chart data.
     */
    public function index()
    {
        // Get paginated history for current user
        $histories = BudgetHistory::with('selectedMenu')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Get aggregated data for trend chart (last 14 days)
        $chartData = $this->getChartData();
        
        return view('student.history', [
            'histories' => $histories,
            'chartData' => $chartData,
        ]);
    }

    /**
     * Get chart data for the last 14 days.
     */
    private function getChartData(): array
    {
        $startDate = Carbon::now()->subDays(13)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        
        // Get daily budget totals
        $dailyBudgets = BudgetHistory::where('user_id', Auth::id())
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(budget_amount) as total_budget')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Create array with all 14 days (fill missing days with 0)
        $labels = [];
        $data = [];
        
        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($date)->format('d M');
            
            // Find matching budget total for this date
            $dailyBudget = $dailyBudgets->firstWhere('date', $date);
            $data[] = $dailyBudget ? (float) $dailyBudget->total_budget : 0;
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}
