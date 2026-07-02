<?php

namespace Database\Seeders;

use App\Models\Criterion;
use Illuminate\Database\Seeder;

class CriterionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define 8 criteria for comprehensive student meal evaluation
        $criteria = [
            [
                'kode' => 'C1',
                'nama_kriteria' => 'Kandungan Gizi',
                'tipe' => 'benefit',
                'bobot' => 0.20, // Nutrition is very important for students
            ],
            [
                'kode' => 'C2',
                'nama_kriteria' => 'Jarak ke Kampus',
                'tipe' => 'cost',
                'bobot' => 0.15, // Distance matters for time efficiency
            ],
            [
                'kode' => 'C3',
                'nama_kriteria' => 'Higienitas',
                'tipe' => 'benefit',
                'bobot' => 0.18, // Hygiene is crucial for health
            ],
            [
                'kode' => 'C4',
                'nama_kriteria' => 'Variasi Menu',
                'tipe' => 'benefit',
                'bobot' => 0.10, // Variety prevents boredom
            ],
            [
                'kode' => 'C5',
                'nama_kriteria' => 'Harga',
                'tipe' => 'cost',
                'bobot' => 0.17, // Price is important for budget-conscious students
            ],
            [
                'kode' => 'C6',
                'nama_kriteria' => 'Porsi',
                'tipe' => 'benefit',
                'bobot' => 0.08, // Portion size matters for satisfaction
            ],
            [
                'kode' => 'C7',
                'nama_kriteria' => 'Kecepatan Layanan',
                'tipe' => 'benefit',
                'bobot' => 0.07, // Speed is important between classes
            ],
            [
                'kode' => 'C8',
                'nama_kriteria' => 'Rasa',
                'tipe' => 'benefit',
                'bobot' => 0.05, // Taste affects overall satisfaction
            ],
        ];

        // Verify total weight sums to 1.00
        $totalWeight = array_sum(array_column($criteria, 'bobot'));
        
        if (abs($totalWeight - 1.00) >= 0.001) {
            throw new \Exception("Total criteria weight must equal 1.00. Current total: {$totalWeight}");
        }

        // Insert criteria into database
        foreach ($criteria as $criterion) {
            Criterion::create($criterion);
        }
    }
}
