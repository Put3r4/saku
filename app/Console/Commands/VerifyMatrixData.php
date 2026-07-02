<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Menu;
use App\Models\Criterion;
use App\Models\MenuEvaluation;

class VerifyMatrixData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'matrix:verify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify that all menu evaluations are complete';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $menus = Menu::where('is_available', true)->get();
        $criteria = Criterion::all();
        
        $this->info("Checking matrix data completeness...");
        $this->info("Total Menus: " . $menus->count());
        $this->info("Total Criteria: " . $criteria->count());
        $this->info("Total Evaluations: " . MenuEvaluation::count());
        $this->info("Expected: " . ($menus->count() * $criteria->count()));
        $this->newLine();
        
        $incomplete = [];
        
        foreach ($menus as $menu) {
            $evals = $menu->menuEvaluations->count();
            if ($evals < $criteria->count()) {
                $missingCriteria = [];
                foreach ($criteria as $criterion) {
                    $hasEval = $menu->menuEvaluations->where('criterion_id', $criterion->id)->count() > 0;
                    if (!$hasEval) {
                        $missingCriteria[] = $criterion->kode;
                    }
                }
                
                $incomplete[] = [
                    'id' => $menu->id,
                    'name' => $menu->vendor_name . ' - ' . $menu->menu_name,
                    'has' => $evals,
                    'missing' => implode(', ', $missingCriteria),
                ];
            }
        }
        
        if (empty($incomplete)) {
            $this->info("✓ All menu evaluations are complete!");
        } else {
            $this->warn("⚠ Found " . count($incomplete) . " menu(s) with incomplete evaluations:");
            $this->table(
                ['Menu ID', 'Menu Name', 'Has Evals', 'Missing Criteria'],
                array_map(function($item) {
                    return [
                        $item['id'],
                        $item['name'],
                        $item['has'],
                        $item['missing'],
                    ];
                }, $incomplete)
            );
        }
        
        return 0;
    }
}
