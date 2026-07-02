<?php

namespace App\Console\Commands;

use App\Models\Criterion;
use App\Models\Menu;
use App\Models\MenuEvaluation;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('seed:show')]
#[Description('Display seeded data summary')]
class ShowSeedData extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== DATA SUMMARY ===');
        $this->newLine();
        
        // Users
        $this->info('USERS: ' . User::count());
        User::all()->each(function ($user) {
            $this->line("  - {$user->name} ({$user->email}) - Role: {$user->role}");
        });
        $this->newLine();
        
        // Criteria
        $this->info('CRITERIA: ' . Criterion::count());
        Criterion::orderBy('kode')->get()->each(function ($criterion) {
            $this->line(sprintf(
                "  %s - %-25s [%-7s] Bobot: %.2f",
                $criterion->kode,
                $criterion->nama_kriteria,
                $criterion->tipe,
                $criterion->bobot
            ));
        });
        $this->newLine();
        
        // Menus
        $this->info('MENUS: ' . Menu::count());
        $this->line('  Category breakdown:');
        $this->line('    - Warung: ' . Menu::where('vendor_name', 'like', 'Warung%')->count());
        $this->line('    - Kantin: ' . Menu::where('vendor_name', 'like', 'Kantin%')->count());
        $this->line('    - Restoran: ' . Menu::where('vendor_name', 'like', 'Restoran%')->count());
        $this->newLine();
        
        // Evaluations
        $this->info('EVALUATIONS: ' . MenuEvaluation::count() . ' (150 menus × 8 criteria)');
        $this->newLine();
        
        // Sample Menu with Evaluations
        $this->info('SAMPLE MENU WITH EVALUATIONS:');
        $menu = Menu::with('evaluations.criterion')->first();
        $this->line("  Menu: {$menu->menu_name}");
        $this->line("  Vendor: {$menu->vendor_name}");
        $this->line("  Price: Rp " . number_format($menu->price, 0, ',', '.'));
        $this->line("  Evaluations:");
        $menu->evaluations->each(function ($eval) {
            $this->line(sprintf(
                "    %s - %-25s : %.2f",
                $eval->criterion->kode,
                $eval->criterion->nama_kriteria,
                $eval->value
            ));
        });
        
        $this->newLine();
        $this->info('✓ All data seeded successfully!');
        
        return 0;
    }
}

