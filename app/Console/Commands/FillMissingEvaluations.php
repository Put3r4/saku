<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Menu;
use App\Models\Criterion;
use App\Models\MenuEvaluation;

class FillMissingEvaluations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'matrix:fill-missing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill missing menu evaluations with default values based on menu data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $menus = Menu::where('is_available', true)->get();
        $criteria = Criterion::all()->keyBy('kode');
        
        $this->info("Filling missing evaluations...");
        
        $filled = 0;
        
        foreach ($menus as $menu) {
            foreach ($criteria as $kode => $criterion) {
                // Check if evaluation already exists
                $exists = MenuEvaluation::where('menu_id', $menu->id)
                    ->where('criterion_id', $criterion->id)
                    ->exists();
                
                if (!$exists) {
                    $value = $this->calculateDefaultValue($menu, $criterion);
                    
                    MenuEvaluation::create([
                        'menu_id' => $menu->id,
                        'criterion_id' => $criterion->id,
                        'value' => $value,
                    ]);
                    
                    $this->info("Added {$kode} for {$menu->menu_name}: {$value}");
                    $filled++;
                }
            }
        }
        
        if ($filled > 0) {
            $this->info("✓ Filled {$filled} missing evaluations!");
        } else {
            $this->info("✓ No missing evaluations found!");
        }
        
        return 0;
    }

    /**
     * Calculate default value for a criterion based on menu data
     */
    private function calculateDefaultValue(Menu $menu, Criterion $criterion): float
    {
        switch ($criterion->kode) {
            case 'C1': // Kandungan Gizi (benefit) - scale 1-10
                return $this->calculateNutritionScore($menu->price, $menu->menu_name);
                
            case 'C2': // Jarak ke Kampus (cost) - km
                // Default: moderate distance
                return 0.8;
                
            case 'C3': // Higienitas (benefit) - scale 1-10
                return $this->calculateHygieneScore($menu->price, $menu->vendor_name);
                
            case 'C4': // Variasi Menu (benefit) - scale 1-10
                return $this->calculateVarietyScore($menu->menu_name, $menu->description);
                
            case 'C5': // Harga (cost) - Rupiah
                return $menu->price;
                
            default:
                return 5.0;
        }
    }

    private function calculateNutritionScore(float $price, string $menuName): float
    {
        $baseScore = 3 + (($price - 8000) / 22000) * 6;

        $proteinBonus = 0;
        if (stripos($menuName, 'seafood') !== false || stripos($menuName, 'rendang') !== false) {
            $proteinBonus = 1.5;
        } elseif (stripos($menuName, 'ayam') !== false || stripos($menuName, 'daging') !== false) {
            $proteinBonus = 1.0;
        } elseif (stripos($menuName, 'telur') !== false || stripos($menuName, 'bakso') !== false) {
            $proteinBonus = 0.5;
        }

        $vegBonus = 0;
        if (stripos($menuName, 'gado') !== false || stripos($menuName, 'pecel') !== false || 
            stripos($menuName, 'sayur') !== false || stripos($menuName, 'lalapan') !== false) {
            $vegBonus = 0.8;
        }

        $score = $baseScore + $proteinBonus + $vegBonus;
        return min(10, round($score, 1));
    }

    private function calculateHygieneScore(float $price, string $vendorName): float
    {
        $baseScore = 5.0;
        
        if (stripos($vendorName, 'restoran') !== false) {
            $baseScore = 7.0 + (($price - 20000) / 10000) * 2;
        } elseif (stripos($vendorName, 'kantin') !== false) {
            $baseScore = 6.0 + (($price - 14000) / 4000) * 2;
        } else {
            $baseScore = 4.0 + (($price - 8000) / 7000) * 3;
        }

        $variation = (rand(0, 10) - 5) * 0.1;
        $score = $baseScore + $variation;
        
        return max(1, min(10, round($score, 1)));
    }

    private function calculateVarietyScore(string $menuName, string $description): float
    {
        $ingredients = ['ayam', 'telur', 'sayur', 'sambal', 'kerupuk', 'udang', 'cumi', 
                       'ikan', 'daging', 'tahu', 'tempe', 'bakso', 'mie', 'lontong', 
                       'nasi', 'santan', 'bumbu', 'lalapan'];
        
        $count = 0;
        $descriptionLower = strtolower($description ?? '');
        foreach ($ingredients as $ingredient) {
            if (stripos($descriptionLower, $ingredient) !== false) {
                $count++;
            }
        }

        $baseScore = min(10, 4 + ($count * 0.8));

        $bonus = 0;
        if (stripos($menuName, 'komplit') !== false || stripos($menuName, 'spesial') !== false) {
            $bonus = 1.0;
        } elseif (stripos($menuName, 'campur') !== false) {
            $bonus = 0.8;
        }

        $score = $baseScore + $bonus;
        return max(1, min(10, round($score, 1)));
    }
}
