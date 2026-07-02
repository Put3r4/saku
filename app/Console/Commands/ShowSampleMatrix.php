<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Menu;
use App\Models\Criterion;

class ShowSampleMatrix extends Command
{
    protected $signature = 'matrix:show {--limit=5 : Number of menus to display}';
    protected $description = 'Display sample matrix data in a table format';

    public function handle()
    {
        $limit = $this->option('limit');
        $menus = Menu::where('is_available', true)
            ->with('menuEvaluations.criterion')
            ->take($limit)
            ->get();
        
        $criteria = Criterion::orderBy('kode')->get();
        
        $this->info("Sample Matrix Data (first {$limit} menus):\n");
        
        foreach ($menus as $menu) {
            $this->info("📋 {$menu->vendor_name} - {$menu->menu_name} (Rp {$menu->price})");
            
            $tableData = [];
            foreach ($criteria as $criterion) {
                $eval = $menu->menuEvaluations->where('criterion_id', $criterion->id)->first();
                $tableData[] = [
                    'Kode' => $criterion->kode,
                    'Kriteria' => $criterion->nama_kriteria,
                    'Tipe' => ucfirst($criterion->tipe),
                    'Nilai' => $eval ? $eval->value : 'N/A',
                ];
            }
            
            $this->table(
                ['Kode', 'Kriteria', 'Tipe', 'Nilai'],
                $tableData
            );
            
            $this->newLine();
        }
        
        return 0;
    }
}
