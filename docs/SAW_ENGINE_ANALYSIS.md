# 🧮 SAW ENGINE ANALYSIS - Simple Additive Weighting

**Detailed Algorithm Implementation Guide**  
**Date:** 01 Juli 2026  
**Status:** ❌ NOT IMPLEMENTED (0%)

---

## 📚 Algorithm Overview

### What is SAW?

**Simple Additive Weighting (SAW)** adalah metode Multi-Criteria Decision Making (MCDM) yang digunakan untuk menentukan alternatif terbaik dari sejumlah alternatif berdasarkan kriteria tertentu.

**Core Principle:**
> Nilai akhir setiap alternatif = **SUM** (bobot kriteria × nilai ternormalisasi)

**Formula:**
```
Vi = Σ(wj × rij)
    j=1 sampai n

Where:
  Vi  = Nilai preferensi alternatif ke-i
  wj  = Bobot kriteria ke-j
  rij = Nilai rating kinerja ternormalisasi
  n   = Jumlah kriteria
```

---

## 🎯 SAW in SAKU Context

### Business Requirements

**Problem:**
Mahasiswa perlu memilih menu makanan harian yang:
1. Sesuai dengan budget mereka
2. Memenuhi kebutuhan gizi
3. Aksesibel (jarak, waktu, dll)
4. Seimbang antara semua kriteria di atas

**Solution:**
SAW akan merangking semua menu berdasarkan:
- Kriteria nutrisi (protein, kalori, serat, dll) - **BENEFIT**
- Kriteria aksesibilitas (jarak, waktu) - **COST**
- Kriteria kualitas (porsi, rating, kebersihan) - **BENEFIT**

**Constraint:**
Budget = **HARD FILTER** (pre-filter sebelum SAW)
- Bukan kriteria dalam matrix
- Menu dengan harga > budget langsung di-exclude
- Alasan: efisiensi komputasi


---

## 📐 Mathematical Foundation

### Step-by-Step Algorithm

#### Step 1: Build Decision Matrix (X)

Matrix berisi nilai rating mentah setiap alternatif terhadap setiap kriteria.

```
X = [xij]m×n

Where:
  m = jumlah alternatif (menu)
  n = jumlah kriteria
  xij = nilai alternatif i pada kriteria j
```

**Example:**
```
               C1(Protein) C2(Kalori) C3(Jarak) C4(Porsi)
Menu A1 (Ayam)      20         450        1.5       3
Menu A2 (Soto)      15         380        0.8       3
Menu A3 (Nasi)      10         500        2.0       4
```

#### Step 2: Identify Criterion Type

Setiap kriteria dikategorikan:
- **BENEFIT** (makin besar makin baik)
  - Protein, Kalori, Porsi, Rating, Kebersihan
- **COST** (makin kecil makin baik)
  - Jarak, Waktu Penyajian

#### Step 3: Normalize Matrix (R)

**For BENEFIT Criteria:**
```
rij = xij / max(xij)

Example:
  max(Protein) = 20
  r11 = 20/20 = 1.00
  r21 = 15/20 = 0.75
  r31 = 10/20 = 0.50
```

**For COST Criteria:**
```
rij = min(xij) / xij

Example:
  min(Jarak) = 0.8
  r13 = 0.8/1.5 = 0.53
  r23 = 0.8/0.8 = 1.00
  r33 = 0.8/2.0 = 0.40
```

**Normalized Matrix R:**
```
               C1(Protein) C2(Kalori) C3(Jarak) C4(Porsi)
Menu A1         1.00        0.90        0.53       0.75
Menu A2         0.75        0.76        1.00       0.75
Menu A3         0.50        1.00        0.40       1.00
```


#### Step 4: Calculate Weighted Score (V)

Kalikan setiap nilai ternormalisasi dengan bobot kriteria:

```
Vi = Σ(wj × rij)
     j=1 sampai n

Where:
  wj = weight/bobot kriteria j
  Constraint: Σwj = 1.00 (total bobot harus 100%)
```

**Example with weights:**
```
W = [0.4, 0.3, 0.2, 0.1]  // Protein(40%), Kalori(30%), Jarak(20%), Porsi(10%)

V1 = (0.4×1.00) + (0.3×0.90) + (0.2×0.53) + (0.1×0.75)
   = 0.40 + 0.27 + 0.11 + 0.08
   = 0.86

V2 = (0.4×0.75) + (0.3×0.76) + (0.2×1.00) + (0.1×0.75)
   = 0.30 + 0.23 + 0.20 + 0.08
   = 0.81

V3 = (0.4×0.50) + (0.3×1.00) + (0.2×0.40) + (0.1×1.00)
   = 0.20 + 0.30 + 0.08 + 0.10
   = 0.68
```

#### Step 5: Ranking

Sort alternatif berdasarkan Vi descending:

```
Rank 1: Menu A1 (Ayam)  = 0.86  ⭐ RECOMMENDED
Rank 2: Menu A2 (Soto)  = 0.81
Rank 3: Menu A3 (Nasi)  = 0.68
```

---

## 💻 Implementation Blueprint

### Required Service Class

```php
<?php

namespace App\Services;

use App\Contracts\SAWServiceInterface;
use App\Models\Menu;
use App\Models\Criterion;
use App\Models\MenuEvaluation;
use Illuminate\Support\Collection;

class SAWCalculationService implements SAWServiceInterface
{
    /**
     * Get menu recommendations based on budget
     */
    public function getRecommendations(float $budget): Collection
    {
        // 1. Filter by budget
        $menus = $this->filterMenusByBudget($budget);
        
        if ($menus->isEmpty()) {
            return collect([]);
        }
        
        // 2. Get all criteria with weights
        $criteria = Criterion::all();
        
        // 3. Build decision matrix
        $matrix = $this->buildDecisionMatrix($menus, $criteria);
        
        // 4. Normalize matrix
        $normalizedMatrix = $this->normalizeMatrix($matrix, $criteria);
        
        // 5. Calculate weighted scores
        $scores = $this->calculateWeightedScore($normalizedMatrix, $criteria->pluck('bobot')->toArray());
        
        // 6. Rank alternatives
        return $this->rankAlternatives($scores, $menus);
    }
    
    /**
     * Filter menus by budget (hard constraint)
     */
    public function filterMenusByBudget(float $budget): Collection
    {
        return Menu::where('price', '<=', $budget)
                   ->where('is_available', true)
                   ->with('evaluations.criterion')
                   ->get();
    }
    
    /**
     * Build decision matrix X
     */
    public function buildDecisionMatrix(Collection $menus, Collection $criteria): array
    {
        $matrix = [];
        
        foreach ($menus as $menu) {
            $row = [];
            foreach ($criteria as $criterion) {
                $evaluation = $menu->evaluations
                    ->where('criterion_id', $criterion->id)
                    ->first();
                    
                $row[] = $evaluation ? $evaluation->value : 0;
            }
            $matrix[] = $row;
        }
        
        return $matrix;
    }
    
    /**
     * Normalize matrix to R
     */
    public function normalizeMatrix(array $matrix, Collection $criteria): array
    {
        $normalized = [];
        $criteriaArray = $criteria->toArray();
        
        // Transpose matrix to work with columns
        $columns = array_map(null, ...$matrix);
        
        foreach ($criteriaArray as $j => $criterion) {
            $column = $columns[$j];
            
            if ($criterion['tipe'] === 'benefit') {
                // Benefit: rij = xij / max(xij)
                $max = max($column);
                $normalized[$j] = array_map(fn($val) => $max > 0 ? $val / $max : 0, $column);
            } else {
                // Cost: rij = min(xij) / xij
                $min = min(array_filter($column, fn($val) => $val > 0)) ?: 1;
                $normalized[$j] = array_map(fn($val) => $val > 0 ? $min / $val : 0, $column);
            }
        }
        
        // Transpose back to rows
        return array_map(null, ...$normalized);
    }
    
    /**
     * Calculate weighted score Vi = Σ(wj × rij)
     */
    public function calculateWeightedScore(array $normalizedMatrix, array $weights): array
    {
        $scores = [];
        
        foreach ($normalizedMatrix as $row) {
            $score = 0;
            foreach ($row as $j => $value) {
                $score += $weights[$j] * $value;
            }
            $scores[] = round($score, 4);
        }
        
        return $scores;
    }
    
    /**
     * Rank alternatives by score
     */
    public function rankAlternatives(array $scores, Collection $menus): Collection
    {
        $ranked = [];
        
        foreach ($menus as $i => $menu) {
            $ranked[] = [
                'menu' => $menu,
                'score' => $scores[$i],
                'rank' => 0, // will be set after sorting
            ];
        }
        
        // Sort by score descending
        usort($ranked, fn($a, $b) => $b['score'] <=> $a['score']);
        
        // Assign rank
        foreach ($ranked as $i => &$item) {
            $item['rank'] = $i + 1;
        }
        
        return collect($ranked);
    }
}
```

