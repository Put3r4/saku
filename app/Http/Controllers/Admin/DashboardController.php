<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BudgetHistory;
use App\Models\Criterion;
use App\Models\Menu;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'totalMenus' => Menu::count(),
            'totalCriteria' => Criterion::count(),
            'totalWeight' => Criterion::sum('bobot'),
            'totalStudents' => User::where('role', 'mahasiswa')->count(),
            'totalRecommendations' => BudgetHistory::count(),
        ];

        // Get 5 recent activities from budget history
        $recentActivities = BudgetHistory::with(['user', 'selectedMenu'])
            ->whereNotNull('selected_menu_id')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($activity) {
                return [
                    'user_name' => $activity->user->name ?? 'Unknown User',
                    'menu_name' => $activity->selectedMenu->menu_name ?? 'Unknown Menu',
                    'budget' => $activity->budget_amount,
                    'saw_score' => $activity->recommendation_data['ranked_menus'][0]['saw_score'] ?? 0,
                    'created_at' => $activity->created_at,
                ];
            });

        return view('admin.dashboard', compact('stats', 'recentActivities'));
    }
}