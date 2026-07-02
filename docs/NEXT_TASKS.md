# ✅ NEXT TASKS - Action Items Priority List

**Date:** 01 Juli 2026  
**Current Progress:** 25%  
**Target:** MVP Ready (100%) in 4 weeks

---

## 🚨 P0 - CRITICAL (Must Do First)

### Week 1: Database Foundation

**Task 1.1: Create Menu Model & Migration**
```bash
php artisan make:model Menu -m
```

**Migration Schema:**
```php
Schema::create('menus', function (Blueprint $table) {
    $table->id();
    $table->string('vendor_name');     // Nama Warung
    $table->string('menu_name');       // Nama Hidangan
    $table->decimal('price', 10, 2);   // Harga (CRITICAL for budget filter)
    $table->text('description')->nullable();
    $table->string('image_url')->nullable();
    $table->boolean('is_available')->default(true);
    $table->timestamps();
    
    // Indexes untuk performance
    $table->index('price');
    $table->index('is_available');
});
```

**Estimated Time:** 30 minutes  
**Blocker:** ❌ Everything depends on this

---

**Task 1.2: Create MenuEvaluation Model & Migration**
```bash
php artisan make:model MenuEvaluation -m
```

**Migration Schema:**
```php
Schema::create('menu_evaluations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('menu_id')->constrained()->onDelete('cascade');
    $table->foreignId('criterion_id')->constrained('criteria')->onDelete('cascade');
    $table->decimal('value', 10, 2);   // Rating mentah (xij)
    $table->timestamps();
    
    // Unique constraint: satu menu-kriteria hanya satu nilai
    $table->unique(['menu_id', 'criterion_id']);
    
    // Indexes
    $table->index(['menu_id', 'criterion_id']);
});
```

**Estimated Time:** 30 minutes  
**Blocker:** ❌ SAW needs this data

---

**Task 1.3: Create BudgetHistory Model & Migration**
```bash
php artisan make:model BudgetHistory -m
```

**Migration Schema:**
```php
Schema::create('budget_histories', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->decimal('budget_amount', 10, 2);
    $table->foreignId('selected_menu_id')->nullable()->constrained('menus')->onDelete('set null');
    $table->json('recommendation_data')->nullable();  // Store full ranking
    $table->timestamp('created_at');
    
    // Indexes
    $table->index(['user_id', 'created_at']);
});
```

**Estimated Time:** 30 minutes

---

**Task 1.4: Setup Eloquent Relationships**

**In `Menu.php`:**
```php
public function evaluations()
{
    return $this->hasMany(MenuEvaluation::class);
}

public function criteria()
{
    return $this->belongsToMany(Criterion::class, 'menu_evaluations')
                ->withPivot('value')
                ->withTimestamps();
}
```

**In `Criterion.php`:**
```php
public function evaluations()
{
    return $this->hasMany(MenuEvaluation::class);
}

public function menus()
{
    return $this->belongsToMany(Menu::class, 'menu_evaluations')
                ->withPivot('value')
                ->withTimestamps();
}
```

**In `User.php`:**
```php
public function budgetHistories()
{
    return $this->hasMany(BudgetHistory::class)->latest();
}
```

**Estimated Time:** 20 minutes

---

**Task 1.5: Create Seeders**

```bash
php artisan make:seeder MenuSeeder
php artisan make:seeder CriterionSeeder
```

**CriterionSeeder** (example data):
```php
Criterion::create(['kode' => 'C1', 'nama_kriteria' => 'Kandungan Protein', 'tipe' => 'benefit', 'bobot' => 0.30]);
Criterion::create(['kode' => 'C2', 'nama_kriteria' => 'Kandungan Kalori', 'tipe' => 'benefit', 'bobot' => 0.25]);
Criterion::create(['kode' => 'C3', 'nama_kriteria' => 'Jarak ke Warung', 'tipe' => 'cost', 'bobot' => 0.20]);
Criterion::create(['kode' => 'C4', 'nama_kriteria' => 'Ukuran Porsi', 'tipe' => 'benefit', 'bobot' => 0.15]);
Criterion::create(['kode' => 'C5', 'nama_kriteria' => 'Rating Rasa', 'tipe' => 'benefit', 'bobot' => 0.10]);
```

**MenuSeeder** (20-30 dummy menus):
```php
Menu::create(['vendor_name' => 'Warung Bu Siti', 'menu_name' => 'Nasi Ayam Bakar', 'price' => 15000]);
// ... etc
```

**Estimated Time:** 1 hour

---

**Task 1.6: Migrate & Seed**

```bash
php artisan migrate:fresh --seed
php artisan tinker  # Test relationships
```

**Estimated Time:** 10 minutes

**✅ Deliverable Week 1:** Complete database dengan sample data

**Total Time Week 1:** ~3 hours


---

## 🔥 P0 - CRITICAL (SAW Engine)

### Week 2: Core Algorithm Implementation

**Task 2.1: Create Service Interface**

```bash
# Create manually (no artisan command)
mkdir app/Contracts
touch app/Contracts/SAWServiceInterface.php
```

**Interface Code:**
```php
<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface SAWServiceInterface
{
    public function getRecommendations(float $budget): Collection;
    public function filterMenusByBudget(float $budget): Collection;
    public function buildDecisionMatrix(Collection $menus, Collection $criteria): array;
    public function normalizeMatrix(array $matrix, Collection $criteria): array;
    public function calculateWeightedScore(array $normalizedMatrix, array $weights): array;
    public function rankAlternatives(array $scores, Collection $menus): Collection;
}
```

**Estimated Time:** 15 minutes

---

**Task 2.2: Create SAW Service Class**

```bash
mkdir app/Services
touch app/Services/SAWCalculationService.php
```

**Implementation:** See `/docs/SAW_ENGINE_ANALYSIS.md` for full code

**Key Methods:**
1. `filterMenusByBudget()` - WHERE price <= budget
2. `buildDecisionMatrix()` - Build matrix X from evaluations
3. `normalizeMatrix()` - Benefit/Cost normalization
4. `calculateWeightedScore()` - Vi = Σ(wj × rij)
5. `rankAlternatives()` - Sort by score DESC
6. `getRecommendations()` - Facade method (calls all above)

**Estimated Time:** 4-6 hours (CRITICAL - harus benar)

---

**Task 2.3: Register Service in AppServiceProvider**

**In `app/Providers/AppServiceProvider.php`:**
```php
use App\Contracts\SAWServiceInterface;
use App\Services\SAWCalculationService;

public function register(): void
{
    $this->app->bind(SAWServiceInterface::class, SAWCalculationService::class);
}
```

**Estimated Time:** 5 minutes

---

**Task 2.4: Write Unit Tests (MANDATORY)**

```bash
php artisan make:test --unit Services/SAWCalculationServiceTest
```

**Tests Must Cover:**
```php
test('it filters menus by budget correctly')
test('it builds decision matrix from evaluations')
test('it normalizes benefit criteria correctly')
test('it normalizes cost criteria correctly')
test('it calculates weighted score accurately')
test('it ranks alternatives in correct order')
test('it handles empty menu list gracefully')
test('it handles zero budget edge case')
test('it handles missing evaluation data')
```

**Estimated Time:** 3-4 hours  
**Coverage Target:** >90%

**✅ Deliverable Week 2:** Working SAW engine dengan tests passing

**Total Time Week 2:** ~10 hours


---

## 🎯 P1 - Important (Admin CRUD)

### Week 2-3: Admin Menu Management

**Task 3.1: Create MenuController**

```bash
php artisan make:controller Admin/MenuController --resource
```

**Implement:**
- `index()` - List all menus with pagination
- `create()` - Show create form
- `store()` - Validate & save menu
- `edit()` - Show edit form
- `update()` - Update menu
- `destroy()` - Soft delete menu

**Validation Rules:**
```php
'vendor_name' => 'required|string|max:255',
'menu_name' => 'required|string|max:255',
'price' => 'required|numeric|min:0|max:999999.99',
'description' => 'nullable|string',
'image_url' => 'nullable|url',
'is_available' => 'boolean'
```

**Estimated Time:** 4 hours

---

**Task 3.2: Create Menu Views**

Create:
- `resources/views/admin/menu/index.blade.php` - List
- `resources/views/admin/menu/create.blade.php` - Form
- `resources/views/admin/menu/edit.blade.php` - Edit form

**Style:** Follow existing admin criteria UI pattern

**Estimated Time:** 3 hours

---

**Task 3.3: Create MatrixController**

```bash
php artisan make:controller Admin/MatrixController
```

**Methods:**
- `index()` - Show matrix table (menus × criteria)
- `update()` - Batch update all values

**Matrix UI:**
```html
<table>
  <thead>
    <tr>
      <th>Menu</th>
      <th>C1 (Protein)</th>
      <th>C2 (Kalori)</th>
      <th>C3 (Jarak)</th>
      ...
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Ayam Bakar</td>
      <td><input type="number" name="values[1][1]" value="20"></td>
      <td><input type="number" name="values[1][2]" value="450"></td>
      ...
    </tr>
  </tbody>
</table>
```

**Estimated Time:** 6 hours (complex UI)

---

**Task 3.4: Update Routes**

**In `routes/web.php`:**
```php
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('criteria', CriterionController::class);
    Route::resource('menu', MenuController::class);        // NEW
    Route::get('matrix', [MatrixController::class, 'index'])->name('matrix.index');  // NEW
    Route::post('matrix', [MatrixController::class, 'update'])->name('matrix.update'); // NEW
});
```

**Estimated Time:** 10 minutes

---

**Task 3.5: Fix Criteria Edit/Delete**

**Add to CriterionController:**
```php
public function edit(Criterion $criterion)
{
    return view('admin.criteria.edit', compact('criterion'));
}

public function update(Request $request, Criterion $criterion)
{
    $validated = $request->validate([...]);
    $criterion->update($validated);
    return redirect()->route('admin.criteria.index')->with('success', 'Updated');
}

public function destroy(Criterion $criterion)
{
    $criterion->delete();
    return back()->with('success', 'Deleted');
}
```

Create `resources/views/admin/criteria/edit.blade.php`

**Estimated Time:** 1 hour

---

**✅ Deliverable Week 2-3:** Admin bisa CRUD menu & input matrix

**Total Time Week 2-3:** ~14 hours


---

## 🎓 P0 - Critical (Student Features)

### Week 3: Student Dashboard & Recommendation

**Task 4.1: Create Student Dashboard Controller**

```bash
php artisan make:controller Student/DashboardController
```

**Methods:**
```php
public function index()
{
    $user = Auth::user();
    $history = $user->budgetHistories()->take(10)->get();
    return view('student.dashboard', compact('history'));
}

public function recommend(Request $request)
{
    $validated = $request->validate([
        'budget' => 'required|numeric|min:1000|max:1000000'
    ]);
    
    $sawService = app(SAWServiceInterface::class);
    $recommendations = $sawService->getRecommendations($validated['budget']);
    
    // Log history
    BudgetHistory::create([
        'user_id' => Auth::id(),
        'budget_amount' => $validated['budget'],
        'recommendation_data' => $recommendations->toJson(),
    ]);
    
    return view('student.recommendation', compact('recommendations'));
}

public function selectMenu(Request $request, Menu $menu)
{
    $history = BudgetHistory::where('user_id', Auth::id())
                            ->latest()
                            ->first();
    
    if ($history) {
        $history->update(['selected_menu_id' => $menu->id]);
    }
    
    return back()->with('success', 'Menu dipilih!');
}
```

**Estimated Time:** 2 hours

---

**Task 4.2: Create Student Views**

Create:
- `resources/views/student/dashboard.blade.php` - Input budget form + history
- `resources/views/student/recommendation.blade.php` - Ranking display

**Dashboard UI:**
```html
<!-- Budget Input Form -->
<form method="POST" action="{{ route('student.recommend') }}">
    @csrf
    <label>Budget Harian (Rp)</label>
    <input type="number" name="budget" value="{{ old('budget', 20000) }}" required>
    <button type="submit">Cari Rekomendasi</button>
</form>

<!-- History -->
<h3>Riwayat Budget</h3>
@foreach($history as $item)
    <div>
        {{ $item->budget_amount }} - {{ $item->created_at->diffForHumans() }}
        @if($item->selected_menu_id)
            Dipilih: {{ $item->selectedMenu->menu_name }}
        @endif
    </div>
@endforeach
```

**Recommendation UI:**
```html
@foreach($recommendations as $rec)
<div class="card rank-{{ $rec['rank'] }}">
    <div class="badge">Rank #{{ $rec['rank'] }}</div>
    <h3>{{ $rec['menu']->menu_name }}</h3>
    <p>{{ $rec['menu']->vendor_name }}</p>
    <p>Rp {{ number_format($rec['menu']->price) }}</p>
    <div class="score">Score: {{ number_format($rec['score'], 2) }}</div>
    <form method="POST" action="{{ route('student.selectMenu', $rec['menu']) }}">
        @csrf
        <button>Pilih Menu Ini</button>
    </form>
</div>
@endforeach
```

**Estimated Time:** 4 hours

---

**Task 4.3: Update Routes**

```php
Route::middleware('auth')->prefix('mahasiswa')->name('student.')->group(function () {
    Route::get('dashboard', [Student\DashboardController::class, 'index'])->name('dashboard');
    Route::post('recommend', [Student\DashboardController::class, 'recommend'])->name('recommend');
    Route::post('select/{menu}', [Student\DashboardController::class, 'selectMenu'])->name('selectMenu');
    Route::get('history', [Student\DashboardController::class, 'history'])->name('history');
});
```

**Estimated Time:** 10 minutes

---

**Task 4.4: Fix Admin Dashboard Data**

**In `Admin/DashboardController@index`:**
```php
$totalMenus = Menu::count();
$totalCriteria = Criterion::count();
$totalStudents = User::where('role', 'mahasiswa')->count();
$recentRecommendations = BudgetHistory::with('user')->latest()->take(5)->get();

return view('admin.dashboard', compact(
    'totalMenus',
    'totalCriteria',
    'totalStudents',
    'recentRecommendations'
));
```

Update view to use dynamic data instead of hardcoded 0.

**Estimated Time:** 30 minutes

---

**✅ Deliverable Week 3:** Student bisa input budget & melihat rekomendasi

**Total Time Week 3:** ~7 hours


---

## 🔧 P1 - Polish & Quality

### Week 4: Bug Fixes & Testing

**Task 5.1: Fix Known Bugs**

1. **BUG-001: Weight Validation**
   ```php
   // In CriterionController@store, add:
   $currentTotal = Criterion::where('id', '!=', $criterion->id ?? 0)->sum('bobot');
   $newTotal = $currentTotal + $request->bobot;
   
   if ($newTotal > 1.00) {
       return back()->withErrors([
           'bobot' => 'Total bobot akan melebihi 1.00. Sisa: ' . (1.00 - $currentTotal)
       ]);
   }
   ```

2. **BUG-002: Edit/Delete UI**
   - Make buttons functional (already fixed in Task 3.5)

3. **BUG-003: Dashboard Hardcoded Data**
   - Use real data (already fixed in Task 4.4)

4. **BUG-004: Student Placeholder**
   - Implement proper controller (already fixed in Task 4.1)

**Estimated Time:** 1 hour

---

**Task 5.2: Add Loading States & Validation**

- Add loading spinner during SAW calculation
- Add client-side validation for forms
- Add confirmation dialogs for delete
- Improve error messages (user-friendly)
- Add success toasts/flash messages

**Estimated Time:** 3 hours

---

**Task 5.3: Write Feature Tests**

```bash
php artisan make:test Feature/Admin/MenuManagementTest
php artisan make:test Feature/Student/RecommendationFlowTest
```

**Tests:**
```php
test('admin can create menu')
test('admin can edit menu')
test('admin can delete menu')
test('admin can input matrix values')
test('student can input budget')
test('student sees recommendations sorted by rank')
test('student can select menu from recommendation')
test('student sees budget history')
test('system excludes menus above budget')
test('system calculates SAW correctly')
```

**Estimated Time:** 4 hours

---

**Task 5.4: Code Documentation**

Add PHPDoc to all classes:
```php
/**
 * Calculate SAW recommendations based on budget constraint
 *
 * @param float $budget Maximum budget amount
 * @return \Illuminate\Support\Collection Collection of ranked menus with scores
 * @throws \Exception If no menus available within budget
 */
public function getRecommendations(float $budget): Collection
```

**Estimated Time:** 2 hours

---

**Task 5.5: Run Code Quality Tools**

```bash
composer lint                    # Laravel Pint
npm run lint                     # ESLint
npm run format                   # Prettier
php artisan test --coverage      # Coverage report
```

**Target:**
- Code style: 100% compliant
- Test coverage: >80%
- No security issues

**Estimated Time:** 1 hour

---

**✅ Deliverable Week 4:** Stable, tested, documented MVP

**Total Time Week 4:** ~11 hours


---

## 📊 Summary Timeline

| Week | Focus | Tasks | Hours | Status |
|------|-------|-------|-------|--------|
| 1 | Database Setup | 1.1 - 1.6 | 3h | ⏳ TODO |
| 2 | SAW Engine | 2.1 - 2.4 | 10h | ⏳ TODO |
| 2-3 | Admin CRUD | 3.1 - 3.5 | 14h | ⏳ TODO |
| 3 | Student Features | 4.1 - 4.4 | 7h | ⏳ TODO |
| 4 | Polish & Testing | 5.1 - 5.5 | 11h | ⏳ TODO |
| **TOTAL** | | **24 tasks** | **45h** | **~6 days** |

**Breakdown:**
- P0 Critical: 20 hours
- P1 Important: 25 hours
- Total: 45 hours (~6 working days for 1 developer)

---

## ✅ Checklist Before Calling "MVP DONE"

### Functionality
- [ ] Admin dapat CRUD criteria dengan validation
- [ ] Admin dapat CRUD menu
- [ ] Admin dapat input nilai matrix (menu × criteria)
- [ ] Student dapat login
- [ ] Student dapat input budget
- [ ] System filter menu berdasarkan budget
- [ ] System hitung SAW algorithm dengan benar
- [ ] System tampilkan ranking menu
- [ ] Student dapat pilih menu
- [ ] System simpan history

### Quality
- [ ] All unit tests passing
- [ ] All feature tests passing
- [ ] Code coverage >80%
- [ ] No security vulnerabilities
- [ ] Code style compliant (Pint + ESLint)
- [ ] No console errors
- [ ] Responsive UI (mobile & desktop)

### Documentation
- [ ] README.md created
- [ ] API documentation (if needed)
- [ ] PHPDoc for all public methods
- [ ] User manual for admin
- [ ] User manual for student

### Deployment Ready
- [ ] .env.example up to date
- [ ] Migration files complete
- [ ] Seeders for demo data
- [ ] Assets built for production
- [ ] Database optimized (indexes)

---

## 🚀 Quick Start Guide for New Developer

1. **Read documentation:**
   ```
   docs/REKAP.md                 # Overall audit
   docs/PROJECT_STRUCTURE.md     # File organization
   docs/SAW_ENGINE_ANALYSIS.md   # Algorithm explanation
   docs/NEXT_TASKS.md            # This file
   ```

2. **Setup environment:**
   ```bash
   composer install
   npm install
   cp .env.example .env
   php artisan key:generate
   touch database/database.sqlite
   php artisan migrate:fresh --seed
   ```

3. **Start Week 1 tasks:**
   - Create Menu model & migration
   - Create MenuEvaluation model & migration
   - Create BudgetHistory model & migration
   - Setup relationships
   - Create seeders
   - Migrate & seed

4. **Test your work:**
   ```bash
   php artisan tinker
   >>> Menu::with('criteria')->first()
   >>> Criterion::with('menus')->first()
   ```

5. **Commit often:**
   ```bash
   git add .
   git commit -m "feat: create Menu model and migration"
   git push
   ```

---

## 📞 Need Help?

- Algorithm unclear? Read `/docs/SAW_ENGINE_ANALYSIS.md`
- Structure unclear? Read `/docs/PROJECT_STRUCTURE.md`
- Bug found? Add to `/docs/BUG_REPORT.md`
- Progress update? Update `/docs/CHANGELOG_RECOVERY.md`

**Good luck! 🎉**

---

*End of Next Tasks Document*
