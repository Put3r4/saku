<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\BudgetConstraintRequest;
use App\Contracts\SAWServiceInterface;
use App\Models\BudgetHistory;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the student dashboard with budget input form.
     */
    public function index()
    {
        return view('student.dashboard');
    }

    /**
     * Process budget constraint and generate menu recommendations.
     */
    public function recommend(BudgetConstraintRequest $request, SAWServiceInterface $sawService)
    {
        $budget = $request->validated()['budget'];
        
        // Get recommendations from SAW service
        $recommendations = $sawService->getRecommendations($budget);
        
        // Transform recommendations to include menu details in flat structure
        $transformedRecommendations = $recommendations->map(function ($item) {
            return [
                'menu_id' => $item['menu']->id,
                'menu_name' => $item['menu']->menu_name,
                'vendor' => $item['menu']->vendor_name,
                'price' => $item['menu']->price,
                'saw_score' => $item['score'],
                'rank' => $item['rank'],
            ];
        });
        
        // Limit to top 5 recommendations
        $topRecommendations = $transformedRecommendations->take(5);
        
        // Store recommendations in session for later use when user selects a menu
        session(['current_recommendations' => $topRecommendations->toArray()]);
        session(['current_budget' => $budget]);
        
        return view('student.recommendation', [
            'recommendations' => $topRecommendations,
            'budget' => $budget,
        ]);
    }

    /**
     * Save selected menu to budget history.
     */
    public function selectMenu(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'budget' => 'required|numeric|min:0',
        ]);

        $menuId = $request->input('menu_id');
        $budget = $request->input('budget');
        
        // Get recommendation data from session
        $recommendations = session('current_recommendations', []);
        
        // Prepare recommendation data (top 5 only)
        $recommendationData = [
            'criteria_weights' => [], // Will be populated from service
            'ranked_menus' => array_slice($recommendations, 0, 5),
            'calculation_method' => 'SAW',
            'budget_constraint' => $budget,
        ];
        
        // Save to budget history
        BudgetHistory::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'budget_amount' => $budget,
            'selected_menu_id' => $menuId,
            'recommendation_data' => $recommendationData,
        ]);
        
        // Clear session data
        session()->forget(['current_recommendations', 'current_budget']);
        
        return redirect()->route('student.history.index')
            ->with('success', 'Menu berhasil dipilih dan disimpan ke riwayat!');
    }
}
